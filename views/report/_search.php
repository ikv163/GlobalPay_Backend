<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\datetime\DateTimePicker;

/* @var $this yii\web\View */
/* @var $model app\models\ReportSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="report-search">

    <?php $form = ActiveForm::begin([
        'action' => [$action],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-sm-2">
            <?= $form->field($model, 'username')->textInput(['placeholder' => '请输入用户名']) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'begintime')->label(Yii::t('app/menu', 'begintime'))->widget(DateTimePicker::classname(), [
                'options' => ['placeholder' => '', 'readonly' => true],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                    'minView' => 2,
                ]
            ]); ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'endtime')->label(Yii::t('app/menu', 'endtime'))->widget(DateTimePicker::classname(), [
                'options' => ['placeholder' => '', 'readonly' => true],
                'pluginOptions' => [
                    'autoclose' => true,
                    'format' => 'yyyy-mm-dd',
                    'minView' => 2, //只选择到日
                ]
            ]); ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app/menu', 'Search'), ['class' => 'btn btn-primary searchButton']) ?>
        <?= Html::resetButton(Yii::t('app/menu', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>
    <?php ActiveForm::end(); ?>
</div>
