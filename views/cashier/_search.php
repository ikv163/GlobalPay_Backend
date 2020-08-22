<?php

use yii\helpers\Html;
use kartik\datetime\DateTimePicker;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\CashierSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cashier-search">

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
            <?= $form->field($model, 'invite_code') ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'income') ?>
        </div>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'security_money') ?>
        </div>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'wechat_rate') ?>
        </div>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'alipay_rate') ?>
        </div>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'wechat_amount') ?>
        </div>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'alipay_amount') ?>
        </div>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'parent_name') ?>
        </div>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'wechat') ?>
        </div>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'alipay') ?>
        </div>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'telephone') ?>
        </div>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'agent_class') ?>
        </div>
        <div class="col-sm-2">
            <?php echo $form->field($model, 'cashier_status')->dropDownList(Yii::t('app', 'default_select') + Yii::t('app', 'cashier_status')) ?>
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
            <?php echo $form->field($model, 'remark') ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app/menu', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app/menu', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
