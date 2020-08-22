<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Deposit;
use app\models\SysBankcard;

/* @var $this yii\web\View */
/* @var $searchModel app\models\DepositSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/menu', 'Deposit_Orders');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="deposit-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="form-group">
        <?= Html::dropDownList('new_status', null, Deposit::$OrderStatusRel, ['class' => 'new_status', 'style' => 'width:120px; height:32px; line-height:32px;']) ?>
        <?= Html::button(Yii::t('app/menu', 'Batch_Change_Status'), ['class' => 'btn btn-success batch-change-status']) ?>
    </div>
    <button type='button' class='btn btn-success' style="float: right" onclick="exportExcel()">导出Excel</button>
    <div>
        创建订单：<?= $statusReadyCount ?> 笔，金额：<?= $statusReady ?> 元 | 处理中订单：<?= $statusDoingCount ?> 笔，金额：<?= $statusDoing ?> 元 | 成功订单：<?= $statusSuccessCount ?> 笔，金额：<?= $statusSuccess ?> 元 | 失败订单：<?= $statusFailedCount ?> 笔，金额：<?= $statusFailed ?> 元 | 驳回订单：<?= $statusRejectCount ?> 笔，金额：<?= $statusReject ?> 元
    </div>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'name' => 'id',
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    return ['data-order-id' => $model->id, 'data-order-status' => $model->deposit_status, "class" => "select_deposit_order"];
                },
                'headerOptions' => [
                    'width' => '30',
                ],
            ],
            'out_deposit_id',
            'system_deposit_id',

            //收款卡信息集合
            [
                'attribute' => Yii::t('app/menu', 'receiving_card_info'),
                'format' => 'raw',
                'value' => function ($model) {
                    if($model->handle_type == 2 && $model->system_bankcard_id > 0){
                        $sysCardInfo = SysBankcard::find()->where(['id'=>$model->system_bankcard_id])->asArray()->one();
                        $receivingCardNumber = is_array($sysCardInfo) && $sysCardInfo && isset($sysCardInfo['bankcard_number']) && $sysCardInfo['bankcard_number'] ? $sysCardInfo['bankcard_number'] : '';
                        $receivingCardOwnerName = is_array($sysCardInfo) && $sysCardInfo && isset($sysCardInfo['bankcard_owner']) && $sysCardInfo['bankcard_owner'] ? $sysCardInfo['bankcard_owner'] : '';
                        $cardSource = isset($sysCardInfo['card_owner']) && isset(\Yii::t('app', 'sys_bankcard_owner')[$sysCardInfo['card_owner']]) ? \Yii::t('app', 'sys_bankcard_owner')[$sysCardInfo['card_owner']] : '';

                    }else{
                        $receivingCardNumber = isset($model->third_bank_account) && $model->third_bank_account ? $model->third_bank_account : '';
                        $receivingCardOwnerName = '';
                        $cardSource = '自动充值渠道';
                    }

                    return '卡  号：'.$receivingCardNumber.'<br/>持卡人：'.$receivingCardOwnerName.'<br/>卡来源：'.$cardSource;
                }
            ],

            [
                'attribute' => Yii::t('app/menu', 'all_infos'),
                'format' => 'raw',
                'value' => function ($model) {
                    $first = \app\models\Cashier::getFirstClass($model->username);
                    $first = !$first ? $model->username : $first;
                    return "所&nbsp;&nbsp;属&nbsp;&nbsp;人：<b>".Html::encode($model->username)."</b><br>一级代理：<b>".Html::encode($first)."</b>";
                }
            ],
            'deposit_money',
            [
                'attribute' => 'deposit_status',
                'format' => 'html',
                'value' => function ($model) {
                    switch ($model->deposit_status) {
                        case 0:
                        case 1:
                            //创建、处理中
                            $color = '';
                            break;
                        case 2:
                            //成功
                            $color = 'green';
                            break;

                        case 3:
                            //失败
                            $color = 'orange';
                            break;
                        case 4:
                            //驳回
                            $color = 'orange';
                            break;
                    }

                    return isset(Deposit::$OrderStatusRel[$model->deposit_status]) ? "<b style='color:{$color}'>" . Deposit::$OrderStatusRel[$model->deposit_status] . "</b>" : '-';
                }
            ],
            'deposit_remark',
            'system_remark',
            'insert_at',
            'update_at',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app/menu', 'options'),
                'template' => '{view} {update} {succeed} {failed}',
                "buttons" => [
                    'succeed' => function ($url, $model, $key) {
                        if (isset($model->deposit_status) && in_array($model->deposit_status, array(Deposit::$OrderStatusInit, Deposit::$OrderStatusProcessing))) {
                            return '<a href="/deposit/setorderstatus?new_status=' . Deposit::$OrderStatusSucceed . '&id=' . $model->id . '" class="btn btn-success">成功</a>';
//                            return Html::button("成功",['class'=>'btn btn-success btn-set-order', 'data-deposit-id'=>$model->id, 'data-old-status'=>$model->deposit_status, 'data-new-status'=>Deposit::$OrderStatusSucceed]);
                        }
                    },
                    'failed' => function ($url, $model, $key) {
                        if (isset($model->deposit_status) && in_array($model->deposit_status, array(Deposit::$OrderStatusInit, Deposit::$OrderStatusProcessing))) {
                            return '<a href="/deposit/setorderstatus?new_status=' . Deposit::$OrderStatusFailed . '&id=' . $model->id . '" class="btn btn-danger">失败</a>';
//                            return Html::button("失败", ['class' => 'btn btn-danger btn-set-order', 'data-deposit-id' => $model->id, 'data-old-status' => $model->deposit_status, 'data-new-status' => Deposit::$OrderStatusFailed]);
                        }
                    },
                ]
            ],
        ],
    ]); ?>
</div>

<script type="text/javascript">
    function exportExcel() {
        var t = $('#w0').serialize();
        windows = window.open('export?' + t)
    }

    $(document).delegate('.btn-set-order', 'click', function () {
        var now = $(this);
        layer.confirm('确认执行此操作？', function () {
            var id = now.data('deposit-id'),
                oldStatus = now.data('old-status'),
                newStatus = now.data('new-status');

            console.log(id + '--' + oldStatus + '--' + newStatus);

            if (isNaN(id) || id < 1) {
                layer.msg('参数错误_1');
                return false;
            }
            if ($.inArray(oldStatus, [0, 1]) < 0) {
                layer.msg('参数错误_2');
                return false;
            }
            if ($.inArray(newStatus, [2, 3]) < 0) {
                layer.msg('参数错误_3');
                return false;
            }

            var orders = [
                    {'order_id': id, 'old_status': oldStatus},
                ],
                newStatus = newStatus;

            changeOrderStatus(orders, newStatus);
        })
    });


    $(document).delegate('.batch-change-status', 'click', function () {
        var orders = [],
            newStatus = $('.new_status').val();

        //console.log(newStatus);
        //console.log($.inArray(newStatus, ['0','1','2','3','4']));

        if ($.inArray(parseInt(newStatus), [0, 1, 2, 3, 4]) < 0) {
            layer.msg('参数错误_1');
            return false;
        }

        $.each($('.select_deposit_order:checked'), function (k, v) {
            var oldStatus = $(v).data('order-status'),
                id = $(v).data('order-id');
            //console.log(oldStatus);
            //console.log(id);

            if (isNaN(parseInt(id)) || id < 1) {
                return true;
            }
            if ($.inArray(oldStatus, [0, 1, 2, 3, 4]) < 0) {
                return true;
            }


            var tmp = {'order_id': id, 'old_status': oldStatus};
            orders.push(tmp);
        });

        if (parseInt(newStatus) == newStatus && newStatus > -1 && orders.length > 0) {
            changeOrderStatus(orders, newStatus);
        } else {
            layer.msg('请选择订单!');
        }
        return false;
    });


    function changeOrderStatus(orders, newStatus) {
        $.post('/deposit/setorderstatus', {'orders': orders, 'new_status': newStatus}, function (res) {
            layer.msg(res.msg);
            if (res.result == 1) {
                setTimeout(function () {
                    window.location.reload();
                }, 1200)
            }
        }, 'json')
    }

</script>



