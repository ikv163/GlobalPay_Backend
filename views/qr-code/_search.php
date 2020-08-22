<?php

use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\QrCodeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="qr-code-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <div class="row">
        <div class="col-sm-2">
            <?= $form->field($model, 'username') ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'query_team')->dropDownList(Yii::t('app', 'default_select') + Yii::t('app', 'query_team')) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'qr_code') ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'qr_address') ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'qr_nickname') ?>
        </div>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'qr_account') ?>
        </div>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'per_max_amount') ?>
        </div>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'per_min_amount') ?>
        </div>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'per_day_amount') ?>
        </div>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'per_day_orders') ?>
        </div>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'qr_location')->input('text',['placeholder'=>"填‘空'，即可查询所在地为空的二维码"]) ?>
        </div>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'is_shopowner')->dropDownList(Yii::t('app', 'default_select') + Yii::t('app', 'is_shopowner')) ?>
        </div>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'qr_type')->dropDownList(Yii::t('app', 'default_select') + Yii::t('app', 'qr_type')) ?>
        </div>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'qr_status')->dropDownList(Yii::t('app', 'default_select') + Yii::t('app', 'qr_status')) ?>
        </div>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'qr_relation') ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'last_money_time_start')->widget(DateTimePicker::classname(), [
                'options' => ['placeholder' => ''],
                'pluginOptions' => [
                    'autoclose' => true,
                    'todayHighlight' => true,
                    'format' => 'yyyy-mm-dd hh:ii:ss'
                ]
            ]); ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'last_money_time_end')->widget(DateTimePicker::classname(), [
                'options' => ['placeholder' => ''],
                'pluginOptions' => [
                    'autoclose' => true,
                    'todayHighlight' => true,
                    'format' => 'yyyy-mm-dd hh:ii:ss'
                ]
            ]); ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'last_code_time_start')->widget(DateTimePicker::classname(), [
                'options' => ['placeholder' => ''],
                'pluginOptions' => [
                    'autoclose' => true,
                    'todayHighlight' => true,
                    'format' => 'yyyy-mm-dd hh:ii:ss'
                ]
            ]); ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'last_code_time_end')->widget(DateTimePicker::classname(), [
                'options' => ['placeholder' => ''],
                'pluginOptions' => [
                    'autoclose' => true,
                    'todayHighlight' => true,
                    'format' => 'yyyy-mm-dd hh:ii:ss'
                ]
            ]); ?>
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
    </div>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app/menu', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app/menu', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
