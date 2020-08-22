<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Withdraw;

/* @var $this yii\web\View */
/* @var $model app\models\Withdraw */

$this->title = Yii::t('app/menu', 'Update_Withdraw_Order');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/menu','Withdraw_Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app/menu','Update');
?>
<div class="withdraw-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'system_withdraw_id')->textInput(['maxlength' => true, 'readonly'=>true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'out_withdraw_id')->textInput(['maxlength' => true, 'readonly'=>true]) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'readonly'=>true]) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'withdraw_money')->textInput(['maxlength' => true, 'readonly'=>true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'withdraw_bankcard_number')->textInput(['maxlength' => true, 'readonly'=>true])->label(Yii::t('app/menu', 'Withdraw_Bankcard_Number')) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'withdraw_remark')->textInput(['maxlength' => true, 'readonly'=>true]) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'system_remark')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'withdraw_status')->dropDownList(Withdraw::$OrderStatusRel) ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app/menu','Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>



<script type="text/javascript">
    $(function(){
        //消息提示
        <?php
        if (isset($msg) && $msg != null){
        ?>
        return layer.alert(<?php echo "'$msg'";?>);
        <?php
        }
        ?>
    })
</script>
