<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model app\models\PayChannelSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pay-channel-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>
    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'channel_name') ?>
        </div>
        <div class="col-sm-3">
            <?php echo $form->field($model, 'pay_type')->dropDownList(Yii::t('app', 'default_select') + Yii::t('app', 'bank_card_pay_type')) ?>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton('搜索', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('重置', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
