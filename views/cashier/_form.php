<?php

use yii\helpers\Html;
use yii\web\View;
use yii\widgets\ActiveForm;
\components\assets\SelectAsset::register($this);
$this->registerJs("$('.select2').select2();", yii\web\View::POS_END);

/* @var $this yii\web\View */
/* @var $model app\models\Cashier */
/* @var $form yii\widgets\ActiveForm */
?>
<style type="text/css">
    .col-sm-3 {
        height: 75px !important;
    }
</style>
<div class="cashier-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-sm-3">
            <?php
            if ($model->id) {
                $username_disabled = true;
                $parentname_disabled = true;
            } else {
                $username_disabled = false;
                $parentname_disabled = false;
            }
            ?>
            <?= $form->field($model, 'username')->textInput(['maxlength' => true, 'disabled' => $username_disabled]); ?>
        </div>
        <?php
        if (!$model->id) {
            ?>
            <div class="col-sm-3">
                <?= $form->field($model, 'login_password')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-sm-3">
                <?= $form->field($model, 'pay_password')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-sm-3">
                <?= $form->field($model, 'agent_class')->textInput(['placeholder' => '数字越小，等级越高', 'disabled' => $parentname_disabled, 'value' => 1]) ?>
            </div>
            <?php
        }
        ?>
        <div class="col-sm-3">
            <?= $form->field($model, 'security_money')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'wechat_amount')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'alipay_amount')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'union_pay_amount')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'bank_card_amount')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'wechat_rate')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'alipay_rate')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'union_pay_rate')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'bank_card_rate')->textInput(['maxlength' => true]) ?>
        </div>

        <div class="col-sm-3">
            <?= $form->field($model, 'wechat')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'alipay')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'priority')->textInput(['maxlength' => true,'']) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'telephone')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'cashier_status')->dropDownList(Yii::t('app', 'default_select') + Yii::t('app', 'cashier_status')) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'remark')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-sm-3">
            <?= $form->field($model, 'parent_name')->widget(\kartik\select2\Select2::className(), [
                'data' => $cashiers,
                'options' => ['placeholder' => '请选择'],
                'pluginOptions' => [
                    'allowClear' => true
                ],
            ]); ?>
        </div>
    </div>
    <div class="form-group">
        <?= Html::submitButton(Yii::t('app/menu', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<?= $this->render('/layouts/msg', [
    'msg'=>$msg,
]) ?>
<script type="text/javascript">
    $(function () {
        //上级变动，代理等级跟随变动
        $('#cashier-parent_name').change(function () {
            if ($(this).find('option:selected').val().length <= 0) {
                $('#cashier-agent_class').val(1);
                return;
            }
            var temp = $(this).find('option:selected').text();
            var firstPosition = temp.indexOf('代理');
            var lastStr = temp.substring(firstPosition + 2);
            if ((/^(\+|-)?\d+$/.test(lastStr)) && lastStr > 0) {
                $('#cashier-agent_class').val((parseInt(lastStr) + 1));
            }
        });

        //判断代理等级是否正确
        $('#cashier-agent_class').keyup(function () {
            var valueX = $(this).val();
            if (!((/^(\+|-)?\d+$/.test(valueX)) && valueX > 0)) {
                return layer.alert('用户代理等级必须是大于0的正整数！');
            }
        })
    })
</script>