<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model app\models\DepositSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="deposit-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-sm-2">
            <?= $form->field($model, 'username') ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'query_team')->dropDownList(Yii::t('app', 'default_select') + Yii::t('app', 'query_team')) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'out_deposit_id') ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'system_deposit_id') ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'deposit_money_range')->textInput(['placeholder'=>'请输入查询金额范围, 如: 1-100'])->label(Yii::t('app/menu', 'Deposit_Amount_Range')) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'deposit_status')->dropDownList(Yii::t('app', 'default_select') + Yii::t('app', 'deposit_status')) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'deposit_remark') ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'system_remark') ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'begintime')->label(Yii::t('app/menu','begintime'))->widget(DateTimePicker::classname(), [
                'options' => ['placeholder' => ''],
                'pluginOptions' => [
                    'autoclose' => true,
                    'todayHighlight' => true,
                    'format' => 'yyyy-mm-dd hh:ii:ss'
                ]
            ]); ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'endtime')->label(Yii::t('app/menu','endtime'))->widget(DateTimePicker::classname(), [
                'options' => ['placeholder' => ''],
                'pluginOptions' => [
                    'autoclose' => true,
                    'todayHighlight' => true,
                    'format' => 'yyyy-mm-dd hh:ii:ss'
                ]
            ]); ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app/menu','Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app/menu','Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
