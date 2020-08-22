<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PayChannel */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pay-channel-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'channel_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?php echo $form->field($model, 'pay_type')->dropDownList(Yii::t('app', 'default_select') + Yii::t('app', 'bank_card_pay_type')) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'per_max_amount')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'per_min_amount')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?php echo $form->field($model, 'channel_status')->dropDownList(Yii::t('app', 'default_select') + Yii::t('app', 'channel_status')) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'user_level')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'credit_level')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton('保存', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
