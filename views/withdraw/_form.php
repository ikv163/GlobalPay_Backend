<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Withdraw */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="withdraw-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'system_withdraw_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'out_withdraw_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'user_type')->textInput() ?>

    <?= $form->field($model, 'withdraw_money')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'bankcard_id')->textInput() ?>

    <?= $form->field($model, 'withdraw_status')->textInput() ?>

    <?= $form->field($model, 'withdraw_remark')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'system_remark')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'insert_at')->textInput() ?>

    <?= $form->field($model, 'update_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
