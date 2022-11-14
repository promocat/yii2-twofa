<?php
/**
 * Created by PhpStorm.
 * User: Brandon Tilstra
 * Date: 4-5-2018
 * Time: 15:16
 */

namespace promocat\twofa\behaviors;

use promocat\twofa\TwoFa;
use Yii;
use yii\base\Behavior;
use yii\db\BaseActiveRecord;

class TwoFaBehavior extends Behavior
{
    /** @var string the attribute that will receive secret value */
    public string $secretAttribute = 'totp_secret';

    /** @var string The Yii2 component name, as defined in config */
    public string $twoFaComponent = 'twoFa';

    public bool $twoFaEnabled;

    private TwoFa $twoFa;

    /**
     * @inheritdoc
     */
    public function events()
    {
        return [
            BaseActiveRecord::EVENT_AFTER_FIND => 'initAttributes',
        ];
    }

    public function init()
    {
        parent::init();

        $this->twoFa = Yii::$app->get($this->twoFaComponent);
    }

    /**
     * Fill the twoFaEnabled variable.
     * Do this afterFind, so that we can set a new secret runtime without marking 2Fa enabled.
     */
    public function initAttributes()
    {
        if (!isset($this->twoFaEnabled)) {
            $this->twoFaEnabled = $this->getTwoFaSecret() !== null;
        }
    }

    public function getTwoFaSecret(): ?string
    {
        return $this->owner->{$this->secretAttribute};
    }

    public function hasTwoFaEnabled(): bool
    {
        return $this->twoFaEnabled;
    }

    public function generateTwoFaSecret(): string
    {
        return $this->twoFa->generateSecret();
    }

    public function enableTwoFa(string $secret = null)
    {
        return $this->owner->updateAttributes([$this->secretAttribute => $secret ?? $this->generateTwoFaSecret()]);
    }

    public function disableTwoFa()
    {
        return $this->owner->updateAttributes([$this->secretAttribute => null]);
    }

    public function validateTwoFaCode(string $code, ?string $secret = null, ?int $window = null): bool
    {
        return $this->twoFa->checkCode($secret ?? $this->getTwoFaSecret(), $code, $window);
    }

    public function getCurrentTwoFaCode(string $secret = null): string
    {
        return $this->twoFa->getCurrentCode($secret ?? $this->getTwoFaSecret());
    }
}
