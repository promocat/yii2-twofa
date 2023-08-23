<?php

namespace app\controllers;

use Yii;
use yii\web\ForbiddenHttpException;
use yii\web\NotFoundHttpException;

class UsersController
{
    /**
     * Enables Two Factor Authentication an existing User model.
     *
     * @param int $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionEnableTwoFa(int $id)
    {
        $model = new \app\controllers\TwoFaForm();
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
     *
     * @param int $id
     *
     * @return mixed
     * @throws NotFoundHttpException
     * @throws \yii\base\Exception
     */
    public function actionDisableTwoFa(int $id)
    {
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
