<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use app\models\Deposit;

/* @var $this yii\web\View */
/* @var $model app\models\Deposit */

$this->title = Yii::t('app/menu', 'Update_Deposit_Order');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/menu', 'Deposit_Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app/menu','Update');
?>
<div class="deposit-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'system_deposit_id')->textInput(['maxlength' => true, 'readonly'=>true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'out_deposit_id')->textInput(['maxlength' => true, 'readonly'=>true]) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'readonly'=>true]) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'deposit_money')->textInput(['maxlength' => true, 'readonly'=>true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-sm-3">
            <?= $form->field($model, 'deposit_remark')->textInput(['maxlength' => true, 'readonly'=>true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'system_remark')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-2">
            <?= $form->field($model, 'deposit_status')->dropDownList(Deposit::$OrderStatusRel) ?>
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
