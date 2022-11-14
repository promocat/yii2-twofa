<?php
/**
 * Created by PhpStorm.
 * User: Brandon Tilstra
 * Date: 16-5-2018
 * Time: 11:03
 */

namespace promocat\twofa;

use Yii;
use yii\web\IdentityInterface;

class User extends \yii\web\User
{
    public string $loginVerificationSessionKey = 'loginVerification';

    /**
     * {@inheritdoc}
     * @param IdentityInterface $identity the user identity information
     * @param bool $cookieBased whether the login is cookie-based
     * @param int $duration number of seconds that the user can remain in logged-in status.
     * If 0, it means login till the user closes the browser or the session is manually destroyed.
     */
    protected function afterLogin($identity, $cookieBased, $duration)
    {
        $this->destroyLoginVerificationSession();

        return parent::afterLogin($identity, $cookieBased, $duration);
    }

    protected function hasValidLoginVerificationSession(): bool
    {
        $data = Yii::$app->session->get($this->loginVerificationSessionKey);
        if ($data === null) {
            return false;
        }
        if (is_array($data) && count($data) == 3) {
            if (time() < $data['exp']) {
                return true;
            }
        }
        $this->destroyLoginVerificationSession();

        return false;
    }

    /**
     * This method attempts to authenticate a user using the information in the login verification session.
     *
     * @return IdentityInterface|null Returns an 'identity' if valid, otherwise null.
     */
    public function getIdentityFromLoginVerificationSession(): ?IdentityInterface
    {
        if ($this->hasValidLoginVerificationSession()) {
            $data = Yii::$app->session->get($this->loginVerificationSessionKey);
            /* @var $class IdentityInterface */
            $class = $this->identityClass;
            $identity = $class::findIdentity($data['id']);
            if ($identity !== null) {
                if (!$identity instanceof IdentityInterface) {
                    throw new InvalidValueException("$class::findIdentity() must return an object implementing IdentityInterface.");
                }
                if ($data['returnUrl']) {
                    $this->setReturnUrl($data['returnUrl']);
                }

                return $identity;
            }
        }
        $this->destroyLoginVerificationSession();

        return null;
    }

    /**
     * @param IdentityInterface $identity
     * @param string|null $returnUrl The Url the user should be redirected to after a valid login verification attempt
     * @param int|null $expirationTime The verification ID is valid till this Unix timestamp. Defaults to 5 minutes in the future
     */
    public function createLoginVerificationSession(IdentityInterface $identity, ?string $returnUrl = null, ?int $expirationTime = null)
    {
        if ($expirationTime === null) {
            $expirationTime = time() + (5 * 60); // Expires in 5 minutes
        }

        Yii::$app->session->set($this->loginVerificationSessionKey, [
            'id' => $identity->getId(),
            'exp' => $expirationTime,
            'returnUrl' => $this->getReturnUrl($returnUrl),
        ]);
    }

    public function destroyLoginVerificationSession()
    {
        Yii::$app->session->remove($this->loginVerificationSessionKey);
    }
}
