<?php
/**
 * Created by PhpStorm.
 * User: Brandon Tilstra
 * Date: 4-5-2018
 * Time: 15:16
 */

namespace promocat\twofa\behaviors;

use Yii;
use yii\base\Behavior;
use yii\db\BaseActiveRecord;

class TwoFaBehavior extends Behavior {

    /** @var string the attribute that will receive secret value */
    public $secretAttribute = 'totp_secret';

    /** @var The Yii2 component name, as defined in config */
    public $twoFaComponent = 'twoFa';

    public $twoFaEnabled;

    /** @var \promocat\twofa\TwoFa */
    private $twoFa;

    /**
     * @inheritdoc
     */
    public function events() {
        return [
            BaseActiveRecord::EVENT_AFTER_FIND => 'initAttributes',
        ];
    }

    public function init() {
        parent::init();
        $this->twoFa = Yii::$app->get($this->twoFaComponent);
    }

    /**
     * Fill the twoFaEnabled variable.
     * Do this afterFind, so that we can set a new secret runtime without marking 2Fa enabled.
     */
    public function initAttributes() {
        if ($this->twoFaEnabled === null) {
            $this->twoFaEnabled = $this->getTwoFaSecret() !== null;
        }
    }

    public function getTwoFaSecret() {
        return $this->owner->{$this->secretAttribute};
    }

    public function hasTwoFaEnabled() {
        return $this->twoFaEnabled;
    }

    public function generateTwoFaSecret() {
        return $this->twoFa->generateSecret();
    }

    public function enableTwoFa(string $secret = null) {
        if ($secret === null) {
            $secret = $this->generateTwoFaSecret();
        }
        return $this->owner->updateAttributes([$this->secretAttribute => $secret]);
    }

    public function disableTwoFa() {
        return $this->owner->updateAttributes([$this->secretAttribute => null]);
    }

    public function validateTwoFaCode(string $code, string $secret = null, $window = null) {
        if ($secret === null) {
            $secret = $this->getTwoFaSecret();
        }
        return $this->twoFa->checkCode($secret, $code, $window);
    }

    public function getCurrentTwoFaCode($secret = null) {
        if ($secret === null) {
            $secret = $this->getTwoFaSecret();
        }
        return $this->twoFa->getCurrentCode($secret);
    }
}