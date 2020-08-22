<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Deposit */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="deposit-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'system_deposit_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'out_deposit_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'deposit_money')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'deposit_status')->textInput() ?>

    <?= $form->field($model, 'deposit_remark')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'system_remark')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'insert_at')->textInput() ?>

    <?= $form->field($model, 'update_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
