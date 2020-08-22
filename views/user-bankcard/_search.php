<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\UserBankcard;
use yii\helpers\ArrayHelper;
use kartik\datetime\DateTimePicker;

\components\assets\SelectAsset::register($this);
$this->registerJs("$('.select2').select2();", yii\web\View::POS_END);

/* @var $this yii\web\View */
/* @var $model app\models\UserBankcardSearch */
/* @var $form yii\widgets\ActiveForm */

$bankTypes = ArrayHelper::map(Yii::t('app', 'BankTypes'), 'BankTypeCode', 'BankTypeName');

?>
<style type="text/css">
    .select2 {
        width: 260px !important;
    }
</style>
<div class="user-bankcard-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-sm-2">
            <?= $form->field($model, 'bankcard_number') ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'bankcard_owner') ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'bankcard_address') ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'username') ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'card_status')->dropDownList(array('' => '所有状态') + UserBankcard::$BankCardStatusRel) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'user_type')->dropDownList(array('' => '所有用户类型') + UserBankcard::$UserTypeRel) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-sm-2">
            <?= $form->field($model, 'bank_code')->dropDownList(array('' => '所有银行') + $bankTypes, ['class' => 'select2'])->label(Yii::t('app/menu', 'Bank_Name')) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'begintime')->label(Yii::t('app/menu', 'begintime'))->widget(DateTimePicker::classname(), [
                'options' => ['placeholder' => ''],
                'pluginOptions' => [
                    'autoclose' => true,
                    'todayHighlight' => true,
                    'format' => 'yyyy-mm-dd hh:ii:ss'
                ]
            ]); ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'endtime')->label(Yii::t('app/menu', 'endtime'))->widget(DateTimePicker::classname(), [
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
        <?= Html::resetButton(Yii::t('app/menu', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
