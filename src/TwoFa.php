<?php
/**
 * Created by PhpStorm.
 * User: Brandon Tilstra
 * Date: 4-5-2018
 * Time: 15:20
 */

namespace promocat\twofa;

use PragmaRX\Google2FA\Google2FA;
use yii\base\Component;

class TwoFa extends Component {

    /** @var Google2FA */
    private $g;

    public $secretLength = 16;

    /*
     * Time window in which the key is valid. 4 = 4*30seconds in the past, but also in the future.
     * This gives the user time to enter the code, but also compensates for a minor time difference.
     */
    public $window = 4;

    public function init() {
        parent::init();
        $this->g = new Google2FA();
    }

    public function generateSecret() {
        return $this->g->generateSecretKey($this->secretLength);
    }

    public function checkCode(String $secret, String $code, $window = null) {
        $window = $window === null ? $this->window : $window;
        return $this->g->verifyKey($secret, $code, $window);
    }

    public function getCurrentCode($secret) {
        return $this->g->getCurrentOtp($secret);
    }

    public function generateQrCodeInline($issuer, $accountName, $secret, $size = 200) {
        return $this->g->getQRCodeInline(
            $issuer,
            $accountName,
            $secret,
            $size
        );
    }
}