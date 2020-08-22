<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\OrderSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/menu', 'Orders');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="order-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php echo $this->render('_search', ['model' => $searchModel, 'merchants' => $merchants]); ?>
    <button type='button' class='btn btn-success' style="float: right" onclick="exportExcel()">导出Excel</button>
    <button type='button' class='btn btn-success' style="float: right;margin-right: 5px;" onclick="refundDetail()">
        返款详情
    </button>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => Yii::t('app/menu', 'all_orderId'),
                'format' => 'raw',
                'value' => function ($model) {
                    $color = '';
                    if (strpos($model->read_remark, '掉单') !== false) {
                        $color = 'red';
                    } elseif (strpos($model->read_remark, '补单') !== false) {
                        $color = '#f308d6';
                    }
                    return "<span style='color:" . $color . "'>平台订单号：<b>$model->order_id</b><br>商户订单号：<b>$model->mch_order_id</b></span>";
                }
            ],
            [
                'attribute' => Yii::t('app/menu', 'all_infos'),
                'format' => 'raw',
                'value' => function ($model) {
                    $first = \app\models\Cashier::getFirstClass($model->username);
                    $first = !$first ? $model->username : $first;
                    return "所&nbsp;&nbsp;属&nbsp;&nbsp;人：<b>" . Html::encode($model->username) . "</b><br>一级代理：<b>" . Html::encode($first) . "</b><br>所属商户：<b>" . Html::encode($model->mch_name) . "</b><br>操&nbsp;&nbsp;作&nbsp;&nbsp;人：<b>" . Html::encode($model->operator) . "</b>";
                }
            ],
            [
                'attribute' => 'qr_code',
                'format' => 'raw',
                'value' => function ($model) {
                    $location = \itbdw\Ip\IpLocation::getLocation($model->user_ip);
                    if ($location) {
                        $location = $location['province'] . $location['city'];
                    } else {
                        $location = '无法识别';
                    }
                    $location = str_replace('市', '', $location);

                    return "二&nbsp;&nbsp;&nbsp;&nbsp;维&nbsp;&nbsp;&nbsp;&nbsp;码：<b>" . Html::encode($model->qr_code) . "</b><br>二维码所属：<b>" . Html::encode($model->qr_location) . "</b><br>订&nbsp;&nbsp;单&nbsp;&nbsp;所属：<b>$location(" . $model->user_ip . ")</b>";
                }
            ],
            [
                'attribute' => Yii::t('app/menu', 'all_money'),
                'format' => 'html',
                'value' => function ($model) {
                    return "订单金额：<b class='c_order_amount'>$model->order_amount</b><br>优惠金额：<b>$model->benefit</b><br>实到金额：<b class='c_actual_amount'>$model->actual_amount</b><br>手&nbsp;&nbsp;续&nbsp;&nbsp;费：<b>$model->order_fee</b>";
                }
            ],
            [
                'attribute' => Yii::t('app/menu', 'all_status'),
                'format' => 'html',
                'value' => function ($model) {
                    $color_order = ['1' => 'gray', '2' => 'green', '3' => '#f18194', '4' => 'red', '5' => '#297ec7'];
                    $color_notify = ['1' => 'gray', '2' => 'green', '3' => 'red'];
                    $color_settlement = ['0' => 'gray', '1' => 'green'];
                    $color = ['1' => '#01aaef', '2' => 'green', '3' => 'red', '4' => 'gray'];
                    return '订单类型：<b style="color: ' . $color[$model->order_type] . '">' . Yii::t('app', 'order_type')[$model->order_type] . '</b><br>' . "订单状态：<b style='color: " . $color_order[$model->order_status] . "'>" . Yii::t('app', 'order_status')[$model->order_status] . "</b><br>通知状态：<b style='color: " . $color_notify[$model->notify_status] . "'>" . Yii::t('app', 'notify_status')[$model->notify_status] . "</b><br>结算状态：<b style='color: " . $color_settlement[$model->is_settlement] . "'>" . Yii::t('app', 'is_settlement')[$model->is_settlement] . "</b><br>访问状态：<b>" . $model->read_remark . "</b>";
                }
            ],
            [
                'attribute' => Yii::t('app/menu', 'all_time'),
                'format' => 'html',
                'value' => function ($model) {
                    return "添加时间：$model->insert_at<br>修改时间：$model->update_at<br>过期时间：$model->expire_time";
                }
            ],
            [
                "class" => "yii\grid\ActionColumn",
                'headerOptions' => [
                    'width' => '200',
                ],
                "template" => "{view} {newdata} {orderOk} {delayOrder} {orderNotify} {orderIncome} {changeMoney}",
                "buttons" => [
                    "view" => function ($url, $model, $key) {
                        if (in_array($model->order_status, [2, 3, 4, 5])) {
                            return '<a class="btn btn-success" href="/order/view?id=' . $model->id . '">详情</a>';
                        }
                    },
                    "newdata" => function ($url, $model, $key) {
                        if (in_array($model->order_status, [2, 3, 4, 5])) {
                            return '<a class="btn btn-success" data-delay="1"  href="/order/newdata?id=' . $model->id . '">补单</a>';
                        }
                    },
                    "orderOk" => function ($url, $model, $key) {
                        if (in_array($model->order_status, [1, 3])) {
                            return '<a class="btn btn-success orderOk" data-delay="1" data-id="' . $model->id . '">成功</a>';
                        }
                    },
                    "delayOrder" => function ($url, $model, $key) {
                        if (in_array($model->order_status, [1, 3])) {
                            return '<a class="btn btn-success orderOk" data-delay="2" data-id="' . $model->id . '">掉单</a>';
                        }
                    },
                    "orderNotify" => function ($url, $model, $key) {
                        if (in_array($model->notify_status, [1, 2, 3]) && in_array($model->order_status, [2, 4, 5])) {
                            return '<a class="btn btn-success orderNotify" data-id="' . $model->id . '">回调</a>';
                        }
                    },
                    "orderIncome" => function ($url, $model, $key) {
                        if ($model->is_settlement == 0) {
                            return '<a class="btn btn-success orderIncome" data-id="' . $model->id . '">结算</a>';
                        }
                    },
                    "changeMoney" => function ($url, $model, $key) {
                        if (in_array($model->order_status, [1, 3])) {
                            return '<a class="btn btn-success changeMoney" data-id="' . $model->id . '" title="如果发现订单金额不符，可修改金额后，重新结算">稽查</a>';
                        }
                    },
                ],
            ],
        ],
    ]); ?>
</div>
<style>
    .btn-success {
        margin-top: 0.5rem;
    }
</style>
<script type="text/javascript">
    function exportExcel() {
        var t = $('#w0').serialize();
        windows = window.open('export?' + t)
    }

    //返款详情
    function refundDetail() {
        var t = $('#w0').serialize();
        $.ajax({
            'url': 'refund-detail?' + t,
            'type': 'get',
            beforeSend: function () {
                layer.msg('查询中...', {
                    icon: 16,
                    shade: [0.1, '#fff'],
                    time: 10000,
                });
            },
            success: function (res) {
                if (res.result == 0) {
                    return layer.msg(res.msg);
                }
                var htmlX = '<div id="refundContents">';
                var moneyX = 0;
                $.each(res.data, function (k, v) {
                    htmlX += '<p>订单ID：<b>' + v.order_id + '</b> | 返款金额：<b>￥' + v.order_amount + '</b> | 所属人：<b>' + v.username + '</b> | 所属二维码：<b>' + v.qr_code + '</b></p>';
                    moneyX += parseFloat(v.actual_amount);
                });
                htmlX += '</div>';
                layer.open({
                    title: '未返款总额预计：￥' + moneyX + '，已返款总额预计：￥' + (parseFloat($('#successMoney').text()) - moneyX),
                    area: ['auto', '400px'],
                    content: htmlX,
                })
                ;
            },
            error: function () {
                return '--';
            }
        })
    }

    //统计
    function summary() {
        var t = $('#w0').serialize();
        $.ajax({
            'url': 'summary?' + t,
            'type': 'get',
            success: function (res) {
                $('#allMoney').text(res.allMoney);
                $('#successMoney').text(res.successMoney);
                $('#successOrders').text(res.successOrders);
                $('#allOrders').text(res.allOrders);
                $('#qrNumbers').text(res.qrNumbers);
                if (res.successOrders > 0) {
                    $('#successRate').text((res.successOrders / res.allOrders * 100).toFixed(3) + '%');
                } else {
                    $('#successRate').text('0%');
                }
            },
            error: function () {
                return '--';
            }
        })
    }

    $(function () {
        //当页统计
        var order_amount = 0;
        var actual_amount = 0;
        $('.c_order_amount').each(function () {
            order_amount += parseFloat($(this).text());
        });
        $('.c_actual_amount').each(function () {
            actual_amount += parseFloat($(this).text());
        });
        var beforeText = '<div>';
        $('.summary').append(beforeText + '总计金额：￥<b id="allMoney">--</b>，总成功金额：￥<b id="successMoney">--</b>，总订单数：<b id="allOrders">--</b>，成功订单数：<b id="successOrders">--</b>，总成功率：<b id="successRate">--</b>，接单二维码个数：<b id="qrNumbers">--</b><br>' + '当页总金额：￥<b>' + order_amount + '</b>，当页成功金额：￥<b>' + actual_amount + '</b></div>');
        summary();

        //稽查
        $(document).on('click', '.changeMoney', function () {
            var orderId = $(this).data('id');
            layer.prompt({
                title: '请输入订单实到金额'
            }, function (value, index) {
                if (value > 0) {
                    $.ajax({
                        'type': 'POST',
                        'url': 'change-money',
                        'data': {'id': orderId, 'money': value},
                        beforeSend: function () {
                            layer.msg('稽查处理中...', {
                                icon: 16,
                                shade: [0.1, '#fff'],
                                time: 10000,
                            });
                        },
                        success: function (res) {
                            iconRes = res.result == 1 ? 1 : 2;
                            layer.msg(res.msg, {
                                icon: iconRes,
                                shade: [0.1, '#fff'],
                                time: 1500,
                                end: function () {
                                    location.reload();
                                }
                            });
                        },
                        error: function () {
                            return layer.msg('操作异常，请联系相关人员');
                        }
                    });
                } else {
                    return layer.msg('金额必须大于0');
                }
            });
        });

        //订单成功
        $(document).on('click', '.orderOk', function () {
            var orderId = $(this).data('id');
            var delay = $(this).data('delay');
            layer.confirm('确定成功？', function () {
                if (orderId.length == 0) {
                    return layer.msg('无法获取订单ID');
                }
                $.ajax({
                    'type': 'POST',
                    'url': 'order-ok',
                    'data': {'id': orderId, 'delay': delay},
                    beforeSend: function () {
                        layer.msg('订单处理中...', {
                            icon: 16,
                            shade: [0.1, '#fff'],
                            time: 10000,
                        });
                    },
                    success: function (res) {
                        iconRes = res.result == 1 ? 1 : 2;
                        layer.msg(res.msg, {
                            icon: iconRes,
                            shade: [0.1, '#fff'],
                            time: 1500,
                        });
                    },
                    error: function () {
                        return layer.msg('操作异常，请联系相关人员');
                    }
                });
            })
        });

        //订单回调
        $(document).on('click', '.orderNotify', function () {
            var orderId = $(this).data('id');
            layer.confirm('确定回调？', function () {
                if (orderId.length == 0) {
                    return layer.msg('无法获取订单ID');
                }
                $.ajax({
                    'type': 'POST',
                    'url': 'order-notify',
                    'data': {'id': orderId},
                    beforeSend: function () {
                        layer.msg('回调中...', {
                            icon: 16,
                            shade: [0.1, '#fff'],
                            time: 10000,
                        });
                    },
                    success: function (res) {
                        iconRes = res.result == 1 ? 1 : 2;
                        layer.msg(res.msg, {
                            icon: iconRes,
                            shade: [0.1, '#fff'],
                            time: 1500,
                        });
                    },
                    error: function () {
                        return layer.msg('操作异常，请联系相关人员');
                    }
                });
            })
        });

        //订单结算
        $(document).on('click', '.orderIncome', function () {
            var orderId = $(this).data('id');
            layer.confirm('确定结算？', function () {
                if (orderId.length == 0) {
                    return layer.msg('无法获取订单ID');
                }
                $.ajax({
                    'type': 'POST',
                    'url': 'order-income',
                    'data': {'id': orderId},
                    beforeSend: function () {
                        layer.msg('结算中...', {
                            icon: 16,
                            shade: [0.1, '#fff'],
                            time: 10000,
                        });
                    },
                    success: function (res) {
                        iconRes = res.result == 1 ? 1 : 2;
                        layer.msg(res.msg, {
                            icon: iconRes,
                            shade: [0.1, '#fff'],
                            time: 1500,
                        });
                    },
                    error: function () {
                        return layer.msg('操作异常，请联系相关人员');
                    }
                });
            })
        });
    })
</script>