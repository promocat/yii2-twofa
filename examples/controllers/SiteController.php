<?php

namespace app\controllers;

use Yii;
use app\models\forms\LoginForm;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;
use promocat\twofa\models\TwoFaForm;

class SiteController
{
    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $model = new LoginForm();
        if ($model->load(Yii::$app->request->post()) && $model->validate()) {
            $user = $model->getUser();

            if (!$user->hasTwoFaEnabled()) {
                $model->login();

                return $this->goBack();
            }
            Yii::$app->user->createLoginVerificationSession($user); //Allow the user to verify the login

            return $this->redirect(['login-verification']);
        }
        $model->password = '';

        return $this->render('login', ['model' => $model]);
    }

    public function actionLoginVerification()
    {
        if (!Yii::$app->user->isGuest) {
            return $this->goHome();
        }

        $user = Yii::$app->user->getIdentityFromLoginVerificationSession();
        if ($user === null) {
            Yii::$app->session->destroy();

            return $this->goHome();
        }

        $model = new TwoFaForm();
        $model->setScenario(TwoFaForm::SCENARIO_LOGIN);
        $model->setUser($user);

        if ($model->load(Yii::$app->request->post()) && $model->login()) {
            return $this->goBack();
        }

        return $this->render('login-verification', ['model' => $model]);
    }

}
