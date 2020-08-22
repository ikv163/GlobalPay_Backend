<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Withdraw;
use app\models\UserBankcard;


/* @var $this yii\web\View */
/* @var $searchModel app\models\WithdrawSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/menu', 'Withdraw_Orders');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="withdraw-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <div class="form-group">
        <?= Html::dropDownList('new_status', null, Withdraw::$OrderStatusRel, ['class' => 'new_status', 'style' => 'width:120px; height:32px; line-height:32px;']) ?>
        <?= Html::button(Yii::t('app/menu', 'Batch_Change_Status'), ['class' => 'btn btn-success batch-change-status']) ?>
    </div>
    <button type='button' class='btn btn-success' style="float: right" onclick="exportExcel()">导出Excel</button>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'name' => 'id',
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    return ['data-order-id' => $model->id, 'data-order-status' => $model->withdraw_status, "class" => "select_withdraw_order",];
                },
                'headerOptions' => [
                    'width' => '30',
                ],
            ],
            'username',
            [
                'attribute' => 'user_type',
                'value' => function ($model) {
                    return isset($model->user_type) && isset(Withdraw::$UserTypeRel[$model->user_type]) ? Withdraw::$UserTypeRel[$model->user_type] : '-';
                }
            ],
            [
                'attribute' => Yii::t('app/menu', 'all_orderId'),
                'format' => 'html',
                'value' => function ($model) {
                    return "平台订单号：<b>$model->system_withdraw_id</b><br>商户订单号：<b>$model->out_withdraw_id</b>";
                }
            ],
            'withdraw_money',
            'handling_fee',
            [
                'attribute' => 'bankcard_id',
                'format'=>'raw',
                'value' => function ($model) {
                    $bankCard = UserBankcard::find()->where(array('id' => $model->bankcard_id))->one();
                    return "持&nbsp;&nbsp;卡&nbsp;&nbsp;人：<b>$bankCard->bankcard_owner</b><br>银行卡号：<b>$bankCard->bankcard_number</b><br>银行类型：<b>".Yii::t('app','bank_code')[$bankCard->bank_code]."</b><br>银行地址：<b>$bankCard->bankcard_address</b>";
                }
            ],
            [
                'attribute' => 'withdraw_status',
                'format' => 'html',
                'value' => function ($model) {
                    $color = [0 => 'black', 1 => 'black', 2 => 'green', 3 => 'red', 4 => 'orange'];
                    return "<b style='color:{$color[$model->withdraw_status]}'>" . Withdraw::$OrderStatusRel[$model->withdraw_status] . "</b>";
                }
            ],
            [
                'attribute' => Yii::t('app/menu', 'all_infos'),
                'format' => 'html',
                'value' => function ($model) {
                    return "用户备注：$model->withdraw_remark<br>系统备注：$model->system_remark";
                }
            ],
            [
                'attribute' => Yii::t('app/menu', 'all_time'),
                'format' => 'html',
                'value' => function ($model) {
                    return "添加时间：$model->insert_at<br>修改时间：$model->update_at";
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app/menu', 'options'),
                'template' => '{view} {update} {succeed} {failed} {auto-withdraw}',
                "buttons" => [
                    'succeed' => function ($url, $model, $key) {
                        if (isset($model->withdraw_status) && in_array($model->withdraw_status, array(Withdraw::$OrderStatusInit, Withdraw::$OrderStatusProcessing))) {
                            return Html::button("成功", ['class' => 'btn btn-success btn-set-order', 'data-withdraw-id' => $model->id, 'data-old-status' => $model->withdraw_status, 'data-new-status' => Withdraw::$OrderStatusSucceed]);
                        }
                    },
                    'failed' => function ($url, $model, $key) {
                        if (isset($model->withdraw_status) && in_array($model->withdraw_status, array(Withdraw::$OrderStatusInit, Withdraw::$OrderStatusProcessing))) {
                            return Html::button("失败", ['class' => 'btn btn-danger btn-set-order', 'data-withdraw-id' => $model->id, 'data-old-status' => $model->withdraw_status, 'data-new-status' => Withdraw::$OrderStatusFailed]);
                        }
                    },

                    'auto-withdraw' => function($url, $model, $key){
                        if (isset($model->out_withdraw_id) && !$model->out_withdraw_id && isset($model->withdraw_status) && in_array($model->withdraw_status, array(Withdraw::$OrderStatusInit, Withdraw::$OrderStatusProcessing))) {
                            return Html::button("自动出款", ['class' => 'btn btn-primary btn-auto-withdraw', 'data-withdraw-id' => $model->id, 'data-withdraw-no'=>$model->system_withdraw_id]);
                        }
                    }
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
            var id = now.data('withdraw-id'),
                oldStatus = now.data('old-status'),
                newStatus = now.data('new-status');

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

        if ($.inArray(parseInt(newStatus), [0, 1, 2, 3, 4]) < 0) {
            layer.msg('参数错误_1');
            return false;
        }

        $.each($('.select_withdraw_order:checked'), function (k, v) {
            var oldStatus = $(v).data('order-status'),
                id = $(v).data('order-id');

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
        $.post('/withdraw/setorderstatus', {'orders': orders, 'new_status': newStatus}, function (res) {
            layer.msg(res.msg);
            if (res.result == 1) {
                setTimeout(function () {
                    window.location.reload();
                }, 1200)
            }
        }, 'json')
    }


    //提款订单提交到typay， 进行自动处理
    $(document).delegate('.btn-auto-withdraw', 'click', function () {
        var now = $(this);
        layer.confirm('确认执行此操作？', function () {
            var id = now.data('withdraw-id'),
                withdrawNo = now.data('withdraw-no');

            if (isNaN(id) || id < 1) {
                layer.msg('参数错误_1');
                return false;
            }
            if (!withdrawNo) {
                layer.msg('参数错误_2');
                return false;
            }

            $.post('/withdraw/auto-withdraw', {'id': id, 'withdraw_no': withdrawNo}, function (res) {
                layer.msg(res.msg);
                if (res.result == 1) {
                    setTimeout(function () {
                        window.location.reload();
                    }, 1500)
                }
            }, 'json')


        })
    });


</script>
