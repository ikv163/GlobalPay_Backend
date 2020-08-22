<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\TransactionFlow */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="transaction-flow-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'client_id')->textInput() ?>

    <?= $form->field($model, 'client_code')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'trade_type')->textInput() ?>

    <?= $form->field($model, 'trans_id')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'trans_account')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'trans_time')->textInput() ?>

    <?= $form->field($model, 'trans_type')->textInput() ?>

    <?= $form->field($model, 'trans_amount')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'trans_status')->textInput() ?>

    <?= $form->field($model, 'trans_fee')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'before_balance')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'trans_balance')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'trans_remark')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'trans_username')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'read_remark')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'md5_sign')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pick_at')->textInput() ?>

    <?= $form->field($model, 'insert_at')->textInput() ?>

    <?= $form->field($model, 'update_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app/menu', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
