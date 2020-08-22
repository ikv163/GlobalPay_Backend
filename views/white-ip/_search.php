<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\WhiteIpSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="white-ip-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-sm-4">
            <?= $form->field($model, 'user_ip') ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'ip_remark') ?>
        </div>
        <div class="col-sm-4">
            <?= $form->field($model, 'ip_status')->dropDownList(Yii::t('app', 'default_select') + Yii::t('app', 'ip_status')) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('app/menu', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app/menu', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
