<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\SystemConfigSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="system-config-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'config_name') ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'config_code') ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'config_value') ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'config_status')->dropDownList(Yii::t('app', 'default_select') + Yii::t('app', 'system_config_status')) ?>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('app/menu', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('app/menu', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
