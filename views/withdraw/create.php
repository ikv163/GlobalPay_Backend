<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\Withdraw */

$this->title = Yii::t('app/menu','Create');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/menu','Withdraw_Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="withdraw-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <div class="deposit-form">

        <?php $form = ActiveForm::begin(); ?>

        <?= $form->field($model, 'out_withdraw_id')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'username')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'withdraw_money')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'withdraw_bankcard_number')->textInput(['maxlength' => true])->label(Yii::t('app/menu', 'Withdraw_Bankcard_Number')) ?>

        <?= $form->field($model, 'withdraw_remark')->textInput(['maxlength' => true]) ?>

        <?= $form->field($model, 'system_remark')->textInput(['maxlength' => true]) ?>

        <div class="form-group">
            <?= Html::submitButton(Yii::t('app/menu','Save'), ['class' => 'btn btn-success']) ?>
        </div>

        <?php ActiveForm::end(); ?>

    </div>

</div>
