<?php
/**
 * Created by PhpStorm.
 * User: Brandon Tilstra
 * Date: 4-5-2018
 * Time: 15:52
 */

namespace promocat\twofa\widgets;

use Yii;
use yii\base\Widget;

class TwoFaQr extends Widget {

    public $twoFaComponent = 'twoFa';
    public $secret;
    public $accountName;
    public $issuer = null;
    public $showSecret = true;
    public $size = 200;

    /** @var \promocat\twofa\TwoFa */
    private $twoFa;

    public function init() {
        parent::init();
        $this->twoFa = Yii::$app->get($this->twoFaComponent);
        $this->issuer = $this->issuer === null ? Yii::$app->name : $this->issuer;
    }

    public function run() {
        $twoFaQrCodeUrl = $this->twoFa->generateQrCodeInline(
            $this->issuer,
            $this->accountName,
            $this->secret,
            $this->size
        );
        $this->renderWidget($this->secret, $twoFaQrCodeUrl);
    }

    public function renderWidget($secret, $twoFaQrCodeUrl) {
        ?>
        <div>
            <img src="<?= $twoFaQrCodeUrl ?>" alt="<?= $secret ?>"/>
        </div>
        <?php if ($this->showSecret === true) { ?>
            <p>
                <?= Yii::t('yii2-twofa', 'Or you can also enter the secret manually:') ?>
            <pre><?= $secret; ?></pre>
            </p>
            <?php
        }
    }
}