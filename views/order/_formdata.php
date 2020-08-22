<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;
/* @var $this yii\web\View */
/* @var $model app\models\Order */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="order-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-sm-2">
        <?= $form->field($model, 'mch_order_id')->textInput(['maxlength' => true,'readonly'=> true]) ?>
        </div>
        <div class="col-sm-2">
        <?= $form->field($model, 'username')->textInput(['maxlength' => true,'readonly'=> true]) ?>
        </div>
        <div class="col-sm-2">
        <?= $form->field($model, 'qr_code')->textInput(['maxlength' => true,'readonly'=> true]) ?>
        </div>
        <div class="col-sm-2">
        <?= $form->field($model, 'mch_name')->textInput(['maxlength' => true,'readonly'=> true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-2">
        <?= $form->field($model, 'order_type')->dropDownList( Yii::t('app', 'order_type')) ?>
        </div>
        <div class="col-sm-2">
        <?= $form->field($model, 'order_amount')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-2">
        <?= $form->field($model, 'remark')->textInput(['maxlength' => true])?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'insert_at')->widget(DateTimePicker::classname(), [
                'options' => ['placeholder' => '','value'=>date('Y-m-d H:i:s')],
                'pluginOptions' => [
                    'autoclose' => true,
                    'todayHighlight' => true,
                    'format' => 'yyyy-mm-dd hh:ii:ss'
                ]
            ]); ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'update_at')->widget(DateTimePicker::classname(), [
                'options' => ['placeholder' => '','value'=>date('Y-m-d H:i:s')],

                'pluginOptions' => [
                    'autoclose' => true,
                    'todayHighlight' => true,
                    'format' => 'yyyy-mm-dd hh:ii:ss'
                ]
            ]); ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app/menu', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
