<?php

use app\common\DES;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Merchant */
/* @var $form yii\widgets\ActiveForm */
?>
<style type="text/css">
    .col-sm-3 {
        height: 80px !important;
    }
</style>
<div class="merchant-form">
    <?php
    $disabled = false;
    if ($model->id) {
        $disabled = true;
    }
    ?>
    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'mch_name')->textInput(['maxlength' => true, 'disabled' => $disabled]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'mch_code')->textInput(['maxlength' => true, 'disabled' => $disabled]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'mch_key')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'mch_status')->dropDownList(Yii::t('app', 'default_select') + Yii::t('app', 'mch_status')) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'available_money')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'wechat_rate')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'alipay_rate')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'union_pay_rate')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'bank_card_rate')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?php
            if (isset($model->pay_password) && $model->pay_password != null) {
                $des = new DES(Yii::$app->params['password'], 'DES-CBC', DES::OUTPUT_BASE64);
                $model->pay_password = $des->decrypt($model->pay_password);
            }
            ?>
            <?= $form->field($model, 'pay_password')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'telephone')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app/menu', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
