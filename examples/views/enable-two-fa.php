<?php

use promocat\twofa\models\TwoFaForm;
use promocat\twofa\widgets\TwoFaQr;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var TwoFaForm $model
 */
?>
<div class="row">
    <div class="col-lg-6">
        <?php
        $form = ActiveForm::begin(['id' => 'enable-2fa-form']);
        echo Yii::t('twofa', 'Scan the following QR code using a TOTP compatible app, like Google Authenticator or Authy.');
        ?>
        <br/>
        <?php
        echo TwoFaQr::widget([
            'accountName' => $model->user->email,
            'secret' => $model->secret,
            'issuer' => Yii::$app->params['twoFaIssuer'],
            'size' => 300,
        ]);
        echo $form->field($model, 'secret')
                  ->hiddenInput()
                  ->label(false);
        ?>
        <br/>
        <?= Yii::t('twofa', 'Enter the generated code to enable two-factor authentication') ?>:
        <?= $form->field($model, 'code')
                 ->textInput([
                     'autofocus' => true,
                     'placeholder' => $model->getAttributeLabel('code'),
                     'autocomplete' => 'off',
                 ])
                 ->label(false); ?>
        <div class="form-group">
            <?= Html::submitButton(Yii::t('default', 'Enable'), ['class' => 'btn btn-primary']) ?>
        </div>
        <?php
        ActiveForm::end(); ?>
    </div>
</div>
