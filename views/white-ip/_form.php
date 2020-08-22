<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\WhiteIp */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="white-ip-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'user_ip')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'ip_remark')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'ip_status')->dropDownList(Yii::t('app', 'default_select') + Yii::t('app', 'ip_status')) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app/menu', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
