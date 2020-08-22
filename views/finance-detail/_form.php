<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\FinanceDetail */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="finance-detail-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'change_amount')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'before_amount')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'after_amount')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'user_type')->textInput() ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'finance_type')->textInput() ?>

    <?= $form->field($model, 'insert_at')->textInput() ?>

    <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
