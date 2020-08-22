<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\CashierSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/menu', 'Cashiers');
$this->params['breadcrumbs'][] = $this->title;
?>
<style type="text/css">
    .widthX {
        width: 140px !important;
        display: inline-block;
        text-align: right;
    }

    .myInput {
        width: 200px !important;
        display: inline-block;
        height: 33px;
    }

    .mySelect {
        height: 33px;
        display: inline-block;
    }
</style>
<div class="cashier-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app/menu', 'Create Cashier'), ['create'], ['class' => 'btn btn-success']) ?>
        保证金剩余额度：￥<?= $totalSecurity ?> | 支付宝剩余额度：￥<?= $totalAlipay ?> | 微信剩余额度：￥<?= $totalWechat ?> |
        云闪付剩余额度：￥<?= $totalUnionPay ?> | 银行卡剩余额度：￥<?= $totalBankCard ?> |
        总共剩余额度：￥<?= $totalAmount ?> | 总收益：￥<?= $totalIncome ?>
    </p>
    <select id="batchUpdate" class="mySelect">
        <option value=''>请选择要修改的字段</option>
        <option value='income'>收益</option>
        <option value='security_money'>保证金</option>
        <option value='wechat_amount'>微信可收额度</option>
        <option value='alipay_amount'>支付宝可收额度</option>
        <option value='union_pay_amount'>云闪付可收额度</option>
        <option value='bank_card_amount'>银行卡可收额度</option>
        <option value='priority'>优先等级(数字大，越优先)</option>
        <option value='canOrder'>是否允许接单(0允许，1不允许)</option>
        <option value='depositLimit'>充值金额限制</option>
        <option value='alipay_rate'>支付宝费率</option>
        <option value='wechat_rate'>微信费率</option>
        <option value='union_pay_rate'>云闪付费率</option>
        <option value='bank_card_rate'>银行卡费率</option>
        <option value='cashier_status'>收款员状态(1启用，0禁用)</option>
    </select>
    <input type="text" class="myInput" placeholder="请填入您要修改的值" id="batchUpdateText"/>
    <button type='button' class='btn btn-success' onclick="batchUpdate()">批量修改</button>
    <button type='button' class='btn btn-success' id="oneBatchUpdate">一键修改</button>
    <button type='button' class='btn btn-success' style="float: right" onclick="exportExcel()">导出Excel</button>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => \yii\grid\CheckboxColumn::className(),
                'checkboxOptions' => function ($model, $key, $index, $column) {
                    return ['value' => $model->id, "class" => "select_item", 'data-name' => $model->username];
                }
            ],
            [
                'attribute' => 'username',
                'format' => 'raw',
                'value' => function ($model) {
                    return '<a style="text-decoration:underline;cursor:pointer" title="点击可查看此收款员的当日收款详情" class="todayOrderDetail" data-username="' . $model->username . '">' . Html::encode($model->username) . '</a>';
                }
            ],
            [
                'attribute' => 'parent_name',
                'format' => 'html',
                'value' => function ($model) {
                    $canOrder = '';
                    if (Yii::$app->redis->get('canOrder' . $model->username) == 0) {
                        $canOrder = '<span style="color: green;">允许接单</span>';
                    } else {
                        $canOrder = '<span style="color: red;">禁止接单</span>';
                    }
                    $first = \app\models\Cashier::getFirstClass($model->username);
                    $str = '';
                    $str .= "直接上级：<b>" . Html::encode($model->parent_name) . "</b><br>";
                    $str .= "一级代理：<b>" . Html::encode($first) . "</b><br>";
                    $str .= "是否接单：<b>$canOrder</b>";
                    return $str;
                }
            ],
            [
                'attribute' => 'cashier_status',
                'format' => 'raw',
                'value' => function ($model) {
                    $status1 = $status0 = '';
                    if ($model->cashier_status == 1) {
                        $status1 = 'selected="selected"';
                        $style = 'style="background:#92c755;color:white"';
                    } else {
                        $status0 = 'selected="selected"';
                        $style = 'style="background:red;color:white"';
                    }
                    return '<select ' . $style . ' class="statusSelect" data-id="' . $model->id . '"><option value="0" ' . $status0 . '>禁用</option><option value="1" ' . $status1 . '>启用</option></select>';
                }
            ],
            [
                'attribute' => Yii::t('app/menu', 'wechatAndAlipay'),
                'format' => 'html',
                'value' => function ($model) {
                    $str = '';
                    $str .= "微&nbsp;&nbsp;&nbsp;&nbsp;信：<b>$model->wechat</b><br>";
                    $str .= "支付宝：<b>$model->alipay</b>";
                    return $str;
                }
            ],
            [
                'attribute' => Yii::t('app/menu', 'all_infos'),
                'format' => 'html',
                'value' => function ($model) {
                    return '代理等级：<b>' . $model->agent_class . '</b><br>优&nbsp;&nbsp;先&nbsp;&nbsp;级：<b>' . $model->priority . '</b><br>备&nbsp;&nbsp;&nbsp;&nbsp;注：<b>' . $model->remark . '</b><br>邀请码：<b>' . $model->invite_code . '</b>';
                }
            ],
            [
                'attribute' => Yii::t('app/menu', 'all_money'),
                'format' => 'html',
                'value' => function ($model) {
                    return '保证金：<b>' . $model->security_money . '</b><br>收&nbsp;&nbsp;&nbsp;&nbsp;益：<b>' . $model->income . '</b>';
                }
            ],
            [
                'attribute' => Yii::t('app/menu', 'all_amount'),
                'format' => 'html',
                'value' => function ($model) {
                    $color = ['1' => '#01aaef', '2' => 'green', '3' => 'red', '4' => 'blue', '5' => 'gray'];
                    $str = '';
                    if ($model->agent_class == 1) {
                        $card = Yii::$app->redis->get('bindDeposit' . $model->username);
                        $card = $card ? $card : '<span style="color: red">未绑定</span>';
                        $str = '<br>充值卡号：<b>' . $card . '</b>';
                    }

                    return '支付宝：<b style="color: ' . $color[1] . '">' . $model->alipay_amount . '</b><br>微&nbsp;&nbsp;&nbsp;&nbsp;信：<b style="color: ' . $color[2] . '">' . $model->wechat_amount . '</b><br>云闪付：<b style="color: ' . $color[3] . '">' . $model->union_pay_amount . '</b><br>银行卡：<b style="color: ' . $color[4] . '">' . $model->bank_card_amount . '</b><br>充值额度：<b style="color: ' . $color[5] . '">' . Yii::$app->redis->get('depositLimit' . $model->username) . '</b>' . $str;
                }
            ],
            [
                'attribute' => Yii::t('app/menu', 'all_rate'),
                'format' => 'html',
                'value' => function ($model) {
                    $color = ['1' => '#01aaef', '2' => 'green', '3' => 'red', '4' => 'gray'];
                    return '支付宝：<b style="color: ' . $color[1] . '">' . $model->alipay_rate . '</b><br>微&nbsp;&nbsp;&nbsp;&nbsp;信：<b style="color: ' . $color[2] . '">' . $model->wechat_rate . '</b><br>云闪付：<b style="color: ' . $color[3] . '">' . $model->union_pay_rate . '</b><br>银行卡：<b style="color: ' . $color[4] . '">' . $model->bank_card_rate . '</b>';
                }
            ],
            [
                'attribute' => Yii::t('app/menu', 'all_time'),
                'format' => 'html',
                'value' => function ($model) {
                    return "注册：$model->insert_at<br>更新：$model->update_at<br>登录：$model->login_at";
                }
            ],
            [
                "class" => "yii\grid\ActionColumn",
                "template" => "{view} | {update}  | {delete}  | {resetPassword}",
                "buttons" => [
                    "delete" => function ($url, $model, $key) {
                        return '<a class="btn btn-success deleteTeam" data-url="' . $url . '">删除团队</a>';
                    },
                    "resetPassword" => function ($url, $model, $key) {
                        return '<a class="btn btn-success restPassword" data-id="' . $model->id . '">重置密码</a>';
                    },
                ],
            ],
        ],
    ]); ?>
</div>

<div class="row passwordX" style="display: none">
    <div class="col-sm-12">
        <div class="form-group">
            <label class="control-label">登录密码</label>
            <input type="text" class="loginPasswordX form-control">
        </div>
    </div>
    <div class="col-sm-12">
        <div class="form-group">
            <label class="control-label">资金密码</label>
            <input type="text" class="securityPasswordX form-control">
        </div>
    </div>
    <div class="col-sm-12">
        <div class="form-group">
            <a class="btn btn-success changePasswordButton">确认修改</a>
        </div>
    </div>
</div>

<script type="text/javascript">
    function exportExcel() {
        var t = $('#w0').serialize();
        windows = window.open('export?' + t)
    }

    //批量修改二维码字段
    function batchUpdate() {
        var columnName = $('#batchUpdate').val();
        if (!columnName) {
            return layer.msg('请先要修改的字段');
        }
        var columnValue = $('#batchUpdateText').val();
        if (!columnValue) {
            return layer.msg('请输入具体的数据');
        }
        var ids = [];
        $('.select_item').each(function () {
            if ($(this).is(':checked')) {
                ids.push($(this).val());
            }
        });
        if (ids.length == 0) {
            return layer.msg('请选择收款员');
        }
        $.ajax({
            url: 'update-column',
            type: 'post',
            data: {'columnName': columnName, 'columnValue': columnValue, 'ids': ids},
            beforeSend: function () {
                layer.msg('处理中...', {
                    icon: 16,
                    shade: [0.1, '#fff'],
                    time: 3000,
                });
            },
            success: function (res) {
                layer.msg(res.msg, {
                    icon: res.result,
                    shade: [0.1, '#fff'],
                    time: 1500,
                    end: function () {
                        //location.reload();
                    }
                });
            },
            error: function () {
                return layer.msg('操作异常，请联系相关人员');
            }
        })
    }

    var id;
    $(function () {
        //修改登录密码和资金密码
        $(document).on('click', '.restPassword', function () {
            id = $(this).data('id');
            layer.open({
                title: '修改密码'
                , content: $('.passwordX').html(),
                btn: ['取消'],
                yes: function (index, layero) {
                    layer.closeAll();
                }
            });
        });
        $(document).on('click', '.changePasswordButton', function () {
            console.log(id);
            $.ajax({
                type: 'POST',
                url: 'change-password',
                data: {
                    'login_password': $('.layui-layer-content .loginPasswordX').val(),
                    'security_password': $('.layui-layer-content .securityPasswordX').val(),
                    'id': id
                },
                beforeSend: function () {
                    layer.msg('处理中...', {
                        icon: 16,
                        shade: [0.1, '#fff'],
                        time: 10000,
                    });
                },
                success: function (data) {
                    var iconX = data.result == 1 ? 1 : 2;
                    layer.msg(data.msg, {icon: iconX});
                },
                error: function () {
                    return layer.msg('操作异常，请联系相关人员');
                }
            });
        });

        //删除个人和Ta的团队
        $(document).on('click', '.deleteTeam', function () {
            var urlX = $(this).data('url');
            layer.confirm('此操作将会删除此收款员和他的团队成员！', function () {
                location.href = urlX;
            });
        });

        //修改状态
        $(document).on('change', '.statusSelect', function () {
            var statusX = $(this).val();
            var id = $(this).data('id');
            var now = $(this);
            $.ajax({
                'type': 'POST',
                'url': '/cashier/change-status',
                'data': {'id': id, 'statusX': statusX},
                beforeSend: function () {
                    layer.msg('处理中...', {
                        icon: 16,
                        shade: [0.1, '#fff'],
                        time: 10000,
                    });
                },
                success: function (res) {
                    if (res.result == 1) {
                        if (statusX == 1) {
                            now.css('background', '#92c755');
                        } else {
                            now.css('background', 'red');
                        }
                        now.css('color', 'white');
                    }
                    return layer.msg(res.msg);
                },
                error: function () {
                    return layer.msg('操作异常，请联系相关人员');
                }
            });
        });

        //查看收款员当天收单详情
        $(document).on('click', '.todayOrderDetail', function () {
            var username = $(this).data('username');
            if (!username) {
                return layer.msg('用户名不能为空');
            }
            $.ajax({
                'type': 'POST',
                'url': 'today-orders-detail',
                'data': {'username': username},
                beforeSend: function () {
                    layer.msg('查询中...', {
                        icon: 16,
                        shade: [0.1, '#fff'],
                        time: 10000,
                    });
                },
                success: function (res) {
                    if (res.result) {
                        var txt = '';
                        $.each(res.data, function (k, v) {
                            if (k.indexOf('次数') != -1) {
                                txt += '<b class="widthX">' + k + '：</b>' + v + ' 次<br>';
                            } else {
                                txt += '<b class="widthX">' + k + '：</b>' + v + ' 元<br>';
                            }
                        });
                        layer.open({
                            title: '今日接单详情',
                            content: txt,
                        })
                    } else {
                        return layer.msg(res.msg);
                    }
                },
                error: function () {
                    return layer.msg('操作异常，请联系相关人员');
                }
            });
        });

        //一键修改收款员字段
        $(document).on('click', '#oneBatchUpdate', function () {
            var batchUpdate = $('#batchUpdate').val();
            if (!batchUpdate) {
                return layer.msg('请先选择要修改的字段');
            }
            var batchUpdateText = $('#batchUpdateText').val();
            if (!batchUpdateText) {
                return layer.msg('请先选择要修改的字段值');
            }
            var t = $('#w0').serializeArray();
            $.merge(t, [{"name": "CashierSearch[columnName]", "value": batchUpdate}, {
                "name": "CashierSearch[columnValue]",
                "value": batchUpdateText
            }]);
            layer.confirm('此操作将会应用到当前条件下所有收款员， 确认？', function () {
                $.ajax({
                    url: 'fast-change-column',
                    type: 'post',
                    data: t,
                    beforeSend: function () {
                        layer.msg('处理中...', {
                            icon: 16,
                            shade: [0.1, '#fff'],
                            time: 3000,
                        });
                    },
                    success: function (res) {
                        console.log(res);
                        layer.msg(res.msg, {
                            icon: res.result,
                            shade: [0.1, '#fff'],
                            time: 1500,
                            end: function () {
                                //location.reload();
                            }
                        });
                    },
                    error: function () {
                        return layer.msg('操作异常，请联系相关人员');
                    }
                })
            })
        });
    })
</script>