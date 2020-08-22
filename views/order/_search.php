<?php

use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\OrderSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-search">

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
            <?= $form->field($model, 'order_id') ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'mch_order_id') ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'qr_code') ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'mch_name')->widget(\kartik\select2\Select2::className(), [
                'data' => $merchants,
                'options' => ['placeholder' => '请选择'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'order_type')->dropDownList(Yii::t('app', 'default_select') + Yii::t('app', 'order_type')) ?>
        </div>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'order_status')->dropDownList(Yii::t('app', 'default_select') + Yii::t('app', 'order_status')) ?>
        </div>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'notify_status')->dropDownList(Yii::t('app', 'default_select') + Yii::t('app', 'notify_status')) ?>
        </div>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'is_settlement')->dropDownList(Yii::t('app', 'default_select') + Yii::t('app', 'is_settlement')) ?>
        </div>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'order_amount') ?>
        </div>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'read_remark') ?>
        </div>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'operator') ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'insert_at_start')->widget(DateTimePicker::classname(), [
                'options' => ['placeholder' => ''],
                'pluginOptions' => [
                    'autoclose' => true,
                    'todayHighlight' => true,
                    'format' => 'yyyy-mm-dd hh:ii:ss'
                ]
            ]); ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'insert_at_end')->widget(DateTimePicker::classname(), [
                'options' => ['placeholder' => ''],
                'pluginOptions' => [
                    'autoclose' => true,
                    'todayHighlight' => true,
                    'format' => 'yyyy-mm-dd hh:ii:ss'
                ]
            ]); ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'update_at_start')->widget(DateTimePicker::classname(), [
                'options' => ['placeholder' => ''],
                'pluginOptions' => [
                    'autoclose' => true,
                    'todayHighlight' => true,
                    'format' => 'yyyy-mm-dd hh:ii:ss'
                ]
            ]); ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'update_at_end')->widget(DateTimePicker::classname(), [
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
        <?= Html::submitButton(Yii::t('app/menu', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app/menu', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
