<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\datetime\DateTimePicker;
use app\models\FinanceDetail;

/* @var $this yii\web\View */
/* @var $model app\models\FinanceDetailSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="finance-detail-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-sm-2">
            <?= $form->field($model, 'username')->label(Yii::t('app/menu', 'Username')) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'query_team')->dropDownList(Yii::t('app', 'default_select') + Yii::t('app', 'query_team')) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'user_type')->dropDownList(array(''=>'所有用户类型')+FinanceDetail::$UserTypeRel)->label(Yii::t('app/menu', 'User_Type')) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'finance_type')->dropDownList(array(''=>'所有交易类型')+FinanceDetail::$FinanceTypeRel) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'c_amount_range')->label(Yii::t('app/menu', 'C_Amount_Range'))->textInput(['placeholder'=>'请输入查询的金额范围, 如: 1-100']) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'b_amount_range')->label(Yii::t('app/menu', 'B_Amount_Range'))->textInput(['placeholder'=>'请输入查询的金额范围, 如: 1-100']) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'a_amount_range')->label(Yii::t('app/menu', 'A_Amount_Range'))->textInput(['placeholder'=>'请输入查询的金额范围, 如: 1-100']) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'begintime')->label(Yii::t('app/menu','begintime'))->widget(DateTimePicker::classname(), [
                'options' => ['placeholder' => ''],
                'pluginOptions' => [
                    'autoclose' => true,
                    'todayHighlight' => true,
                    'format' => 'yyyy-mm-dd hh:ii:ss'
                ]
            ]);  ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'endtime')->label(Yii::t('app/menu','endtime'))->widget(DateTimePicker::classname(), [
                'options' => ['placeholder' => ''],
                'pluginOptions' => [
                    'autoclose' => true,
                    'todayHighlight' => true,
                    'format' => 'yyyy-mm-dd hh:ii:ss'
                ]
            ]);  ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'remark') ?>
        </div>

    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app/menu','Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app/menu','Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
