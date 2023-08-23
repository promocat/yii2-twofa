<?php

use promocat\twofa\models\TwoFaForm;
use yii\bootstrap\ActiveForm;
use yii\helpers\Html;
use yii\web\View;

/**
 * @var TwoFaForm $model
 */

$form = ActiveForm::begin(['id' => 'login-verification-form']); ?>
<div class="form-group">
    <?= $form->field($model, 'code')->label(false)->textInput([
        'autofocus' => true,
        'class' => 'form-control',
        'autocomplete' => 'off',
    ]) ?>
</div>
<div class="row">
    <div class="col-sm-5">
        <?= Html::a(Yii::t('default', 'Cancel'), ['login'], ['class' => 'btn btn-default btn-block']) ?>
    </div>
    <div class="col-sm-offset-2 col-sm-5">
        <?= Html::submitButton(Yii::t('default', 'Login'), ['class' => 'btn btn-primary btn-block']) ?>
    </div>
</div>
<?php ActiveForm::end(); ?>
