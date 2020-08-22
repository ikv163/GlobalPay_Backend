<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\SysBankcard;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SysBankcardSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/menu', 'Sys_Bankcard_Management');
$this->params['breadcrumbs'][] = $this->title;
?>

<style>
    .firstCashier .select2-selection{
        margin-top:-5px;
    }
</style>

<div class="sys-bankcard-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <div style="display: inline-block">
            <?= Html::a(Yii::t('app/menu', 'Create'), ['create'], ['class' => 'btn btn-success']) ?> |
        </div>

        <!--<select class="firstCashier" style="width:120px; height:32px; line-height:32px;">
            <option value="">请选择一级代理</option>
            <?php
/*            foreach ($firstCashier as $v) {
                echo '<option value="' . $v->username . '">' . $v->username . '</option>';
            }
            */?>
        </select>
        <a class="btn btn-success bindDeposit">绑定充值卡</a>-->


        <div  class="firstCashier_div" style="display:inline-block;position:relative;top:6px;">
            <?= \kartik\select2\Select2::widget([
                'name'=>'firstCashier',
                'id'=>'firstCashier',
                'data'=>[''=>'请选择一级代理'] + app\models\Cashier::getAllFirstLevelAgent(1),
                'pluginOptions' => [
                    'allowClear' => true,
                ],
            ]);
            ?>
        </div>
        <a class="btn btn-success bindDeposit">绑定充值卡</a>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'name' => 'id',
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    return ['value' => $model->bankcard_number, "class" => "select_bank"];
                },
                'headerOptions' => [
                    'width' => '30',
                ],
            ],
            'bankcard_number',
            'bankcard_owner',
            [
                'attribute' => Yii::t('app/menu', 'Bank_Name'),
                'value' => function ($model) {
                    $configBankTypes = Yii::t('app', 'BankTypes');
                    $configBankTypes = $configBankTypes ? ArrayHelper::map($configBankTypes, 'BankTypeCode', 'BankTypeName') : array();
                    return $configBankTypes && isset($configBankTypes[$model->bank_code]) ? $configBankTypes[$model->bank_code] : '-';
                }
            ],
            'bankcard_address',
            'balance',
            'max_balance',
            [
                'attribute' => 'card_owner',
                'format' => 'raw',
                'value' => function($model){
                    return isset($model->card_owner) && is_numeric($model->card_owner) && $model->card_owner > 0 && intval($model->card_owner) == $model->card_owner && isset(\Yii::t('app', 'sys_bankcard_owner')[$model->card_owner]) ? \Yii::t('app', 'sys_bankcard_owner')[$model->card_owner] : '未设置';
                }
            ],

            [
                'attribute' => 'card_status',
                'format' => 'html',
                'value' => function ($model) {
                    switch ($model->card_status) {
                        case 0:
                        case 1:
                            //启用
                            $color = 'green';
                            break;
                        case 2:
                            //禁用
                            $color = 'orange';
                            break;

                        case 9:
                            //删除
                            $color = 'red';
                            break;
                        default:
                            $color = '';
                            break;
                    }
                    return isset(SysBankcard::$BankCardStatusRel[$model->card_status]) ? "<b style='color:{$color}'>" . SysBankcard::$BankCardStatusRel[$model->card_status] . "</b>" : '-';
                }
            ],
            'insert_at',
            'remark',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app/menu', 'options'),
            ],

        ],
    ]); ?>
</div>
<script type="text/javascript">
    $(function () {
        $('.bindDeposit').click(function () {
            var username = $('#firstCashier').val();
            var bankId = '';

            $.each($('.select_bank:checked'), function (k, v) {
                if (bankId != '') {
                    return layer.msg('请选择一张银行卡即可');
                }
                bankId = $(v).val();
            });

            if (username.length == 0 || bankId.length == 0) {
                return layer.msg('请选择一级代码并选择一张银行卡进行绑定');
            }
            $.ajax({
                url: 'bind-deposit',
                type: 'post',
                data: {username: username, bankId: bankId},
                success: function (res) {
                    return layer.msg(res.msg);
                },
                error: function () {
                    return layer.msg('操作异常');
                }
            })
        });
    })
</script>
