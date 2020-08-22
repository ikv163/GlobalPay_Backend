<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SysBankcard */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sys-bankcard-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'bankcard_number')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bankcard_owner')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bank_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bankcard_address')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'balance')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'card_status')->textInput() ?>

    <?= $form->field($model, 'insert_at')->textInput() ?>

    <?= $form->field($model, 'update_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
