<?php

class SiteControllerExamples {

    /**
     * Login action.
     *
     * @return string
     */
    public function actionLogin() {

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

        return $this->render('login', [
            'model' => $model,
        ]);
    }

    public function actionLoginVerification() {
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
        return $this->render('login-verification', [
            'model' => $model,
        ]);
    }

}

class UserControllerExamples {
    /**
     * Enables Two Factor Authentication an existing User model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionEnableTwoFa($id) {
        $model = new TwoFaForm();
        $user = $this->findModel($id);

        if ($user->id !== Yii::$app->user->id) {
            throw new ForbiddenHttpException('You are not allowed to update this user.');
        }

        if ($user->hasTwoFaEnabled()) {
            Yii::$app->session->setFlash('error', Yii::t('twofa', 'Two-Factor authentication is already enabled.'));
            return $this->redirect(['view', 'id' => $user->id]);
        }

        $model->setUser($user);
        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $user->id]);
        }
        return $this->render('enable-two-fa', [
            'model' => $model,
        ]);
    }

    /**
     * Enables Two Factor Authentication an existing User model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionDisableTwoFa($id) {
        $user = $this->findModel($id);
        if ($user->id !== Yii::$app->user->id) {
            throw new ForbiddenHttpException('You are not allowed to update this user.');
        }
        if (!$user->hasTwoFaEnabled()) {
            Yii::$app->session->setFlash('error', Yii::t('twofa', 'Two-Factor authentication is not enabled.'));
        } else {
            $user->disableTwoFa();
        }
        return $this->redirect(['view', 'id' => $user->id]);
    }
}