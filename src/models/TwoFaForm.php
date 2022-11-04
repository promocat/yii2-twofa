<?php

namespace promocat\twofa\models;

use Yii;
use yii\base\Model;

/**
 * Enable Two-Factor Authentication form
 */
class TwoFaForm extends Model {

    /**
     * @var string Scenario defaults to "default". Otherwise, override the constructor or init.
     * @see https://github.com/yiisoft/yii2/issues/12707
     */
    const SCENARIO_ACTIVATE = self::SCENARIO_DEFAULT;
    const SCENARIO_LOGIN = 'login';

    /**
     * @var string The generated secret
     */
    public $secret;
    /**
     * @var string The code entered by the user
     */
    public $code;

    /**
     * @var string Keeps the user logged in.
     */
    public $rememberMe = true;

    /**
     * Time window in which the key is valid. Leave this null to use the default component setting.
     * @var int
     */
    public $window;

    private $_user;

    /**
     * @inheritdoc
     */
    public function rules() {
        return [
            [['secret', 'code'], 'required'],
            ['code', 'filter', 'filter' => 'trim'],
            ['code', 'string', 'min' => 6],
            ['code', 'validateCode'],
            ['rememberMe', 'required', 'on' => self::SCENARIO_LOGIN],
            ['rememberMe', 'boolean', 'on' => self::SCENARIO_LOGIN],
        ];
    }

    public function validateCode($attribute, $params) {
        if (!$this->hasErrors()) {
            $user = $this->getUser();
            if (!$user || !$user->validateTwoFaCode($this->code, $this->secret, $this->window)) {
                $this->addError($attribute, 'Incorrect code.');
            }
        }
    }

    /**
     * @return User
     */
    public function getUser() {
        return $this->_user;
    }

    /**
     * @param User $user
     */
    public function setUser($user) {
        $this->_user = $user;
        $this->secret = $user->hasTwoFaEnabled() ? $user->getTwoFaSecret() : $user->generateTwoFaSecret();
    }

    /**
     * Logs in a user using the provided code.
     *
     * @return bool whether the user is logged in successfully
     */
    public function login() {
        if ($this->validate()) {
            return Yii::$app->user->login($this->getUser(), $this->rememberMe ? 3600 * 24 * 30 : 0);
        }
        return false;
    }

    /**
     * Enables Two Factor Authentication for a user.
     * @return bool the saved model or null if saving fails
     * @throws Exception
     */
    public function save(): bool {
        if ($this->validate()) {
            $user = $this->getUser();
            $user->enableTwoFa($this->secret);
            return !$user->hasErrors();
        }
        return false;
    }
}
