<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\SysBankcard;
use yii\helpers\ArrayHelper;
\components\assets\SelectAsset::register($this);
$this->registerJs("$('.select2').select2();", yii\web\View::POS_END);

/* @var $this yii\web\View */
/* @var $model app\models\SysBankcard */

$this->title = Yii::t('app/menu', 'Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/menu', 'Sys_Bankcard_Management'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

$bankTypes = ArrayHelper::map(Yii::t('app', 'BankTypes'), 'BankTypeCode', 'BankTypeName');

?>
<div class="sys-bankcard-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-2">
            <?= $form->field($model, 'bank_code')->dropDownList($bankTypes, ['class'=>'select2'])->label(Yii::t('app/menu', 'Bank_Name')) ?>
        </div>

        <div class="col-sm-2">
            <?= $form->field($model, 'bankcard_number')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-2">
            <?= $form->field($model, 'bankcard_address')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-2">
            <?= $form->field($model, 'bankcard_owner')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-2">
            <?= $form->field($model, 'card_status')->dropDownList(SysBankcard::$BankCardStatusRel) ?>
        </div>

        <div class="col-sm-2">
            <?= $form->field($model, 'balance')->textInput(['value'=>'0.00']) ?>
        </div>

        <div class="col-sm-2">
            <?= $form->field($model, 'max_balance')->textInput(['value'=>'0.00']) ?>
        </div>

        <div class="col-sm-2">
            <?= $form->field($model, 'card_owner')->dropDownList(\Yii::t('app', 'sys_bankcard_owner')) ?>
        </div>

        <div class="col-sm-2">
            <?= $form->field($model, 'remark')->textInput(['value'=>'']) ?>
        </div>

    </div>

    <div class="row">
        <div class="form-group">
            <?= Html::submitButton(Yii::t('app/menu', 'Save'), ['class' => 'btn btn-success']) ?>
        </div>
    </div>


    <?php ActiveForm::end(); ?>

</div>
