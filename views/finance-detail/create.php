<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\FinanceDetail;

/* @var $this yii\web\View */
/* @var $model app\models\FinanceDetail */

$this->title = Yii::t('app/menu','Create_Finance_Detail');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/menu','Finance_Details'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="finance-detail-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'user_type')->dropDownList(array(''=>'所有用户类型')+FinanceDetail::$UserTypeRel)->label(Yii::t('app/menu', 'User_Type')) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'finance_type')->dropDownList(array(''=>'所有交易类型')+FinanceDetail::$FinanceTypeRel) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'change_amount')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'before_amount')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'after_amount')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app/menu','Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
