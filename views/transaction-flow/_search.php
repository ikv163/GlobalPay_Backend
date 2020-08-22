<?php

use kartik\datetime\DateTimePicker;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TransactionFlowSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="transaction-flow-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>
    <div class="row">
        <div class="col-sm-2">
            <?= $form->field($model, 'trans_time_start')->widget(DateTimePicker::classname(), [
                'options' => ['placeholder' => ''],
                'pluginOptions' => [
                    'autoclose' => true,
                    'todayHighlight' => true,
                    'format' => 'yyyy-mm-dd hh:ii:ss'
                ]
            ]); ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'trans_time_end')->widget(DateTimePicker::classname(), [
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
        <div class="col-sm-2">
            <?= $form->field($model, 'client_id') ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'client_code') ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'read_remark') ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'trade_type')->dropDownList(Yii::t('app','default_select')+Yii::t('app','trade_type')) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'trade_cate')->dropDownList(Yii::t('app','default_select')+Yii::t('app','trade_cate')) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'trans_id') ?>
        </div>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'trans_account') ?>
        </div>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'trans_type')->dropDownList(Yii::t('app','default_select')+Yii::t('app','trans_type')) ?>
        </div>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'trans_amount') ?>
        </div>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'trans_status')->dropDownList(Yii::t('app','default_select')+Yii::t('app','trans_status')) ?>
        </div>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'trans_username') ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app/menu', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app/menu', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
