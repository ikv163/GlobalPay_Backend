<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use app\models\QrCode;

/* @var $this yii\web\View */
/* @var $searchModel app\models\QrCodeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/menu', 'Qr Codes');
$this->params['breadcrumbs'][] = $this->title;
?>
<style type="text/css">
    .myInput {
        width: 200px !important;
        display: inline-block;
        height: 33px;
    }

    .mySelect {
        height: 33px;
        display: inline-block;
    }

    /*#new_cashier_div span{*/
        /*display: inline-block !important;*/
    /*}*/
    /*#new_cashier_div .select2-selection{*/
        /*width:275px;*/
    /*}*/
</style>
<div class="qr-code-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app/menu', 'Create Qr Code'), ['create'], ['class' => 'btn btn-success']) ?> |
        <select id="qr_status" class="mySelect">
            <?php
            $temp = Yii::t('app', 'qr_status');
            echo '<option value>请选择</option>';
            foreach ($temp as $k => $v) {
                echo "<option value=$k>$v</option>";
            }
            ?>
        </select>
        <button type='button' class='btn btn-success' onclick="updateStatus()">批量修改状态</button>
        <button type='button' class='btn btn-success fastChangeStatus'>一键修改状态</button>
        |
        <input type="text" class="myInput" id="qrLocationX" title="省会城市填省+市级名称（如：广东广州），否则只需填写市级名称（如：深圳）"
               placeholder="省会城市填省+市级名称（如：广东广州），否则只需填写市级名称（如：深圳）"/>
        <button type='button' class='btn btn-success' onclick="updateLocation()">设置所在地</button>
        |
        <select id="batchUpdate" class="mySelect">
            <option value=''>请选择要修改的字段</option>
            <option value='per_min_amount'>每笔最小金额</option>
            <option value='per_max_amount'>每笔最大金额</option>
            <option value='per_day_amount'>每日可收总额</option>
            <option value='per_day_orders'>每日总收笔数</option>
            <option value='priority'>优先等级（越大越优先）</option>
            <option value='allow_order_type'>允许接单类型</option>
        </select>
        <input type="text" class="myInput" placeholder="请填入您要修改的值" id="batchUpdateText"/>
        <button type='button' class='btn btn-success' onclick="batchUpdate()">批量修改</button>
        <button type='button' class='btn btn-success' id="oneBatchUpdate">一键修改</button>
    </p>


    <div  id="new_cashier_div" style="display:inline-block; width:42%">
        <?= \kartik\select2\Select2::widget([
            'name'=>'new_cashier',
            'id'=>'new_cashier',
            'data'=>[''=>'请选择目标收款员'] + app\models\Cashier::getAllCashier(),
            'pluginOptions' => [
                'allowClear' => true,
            ],
        ]);
        ?>
    </div>
    <span class='btn btn-success' id="moveQrCodes" style="display: inline-block;margin:0 0 10px 0;">迁移二维码</span>

    <br/>

    <button type='button' class='btn btn-success' style="float: right" onclick="exportExcel()">导出Excel</button>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'name' => 'id',
                'checkboxOptions' => function ($model) {
                    return ['value' => $model->id, "class" => "select_item", 'data-name' => $model->qr_code, 'data-username'=>$model->username];
                }
            ],
            [
                'attribute' => Yii::t('app/menu', 'AllNames'),
                'format' => 'html',
                'value' => function ($model) {
                    $first = \app\models\Cashier::getFirstClass($model->username);
                    $str = null;
                    if ($model->qr_type == 3 || $model->qr_type == 4) {

                        $bankName = $model->bank_code && isset(Yii::t('app', 'bank_code')[$model->bank_code]) && Yii::t('app', 'bank_code')[$model->bank_code] ? Yii::t('app', 'bank_code')[$model->bank_code] : '';

                        $str .= "所属人：<b>$model->username</b><br>简&nbsp;&nbsp;&nbsp;&nbsp;码：<b>$model->qr_code</b><br>账&nbsp;&nbsp;&nbsp;&nbsp;号：<b>$model->qr_account</b><br>银行卡号：<b>$model->bank_card_number</b><br>银行类型：<b>" . $bankName . "</b><br>真实姓名：<b>$model->real_name</b><br>一级代理：<b>$first</b>";
                    } else {
                        $str .= "所属人：<b>$model->username</b><br>简&nbsp;&nbsp;&nbsp;&nbsp;码：<b>$model->qr_code</b><br>账&nbsp;&nbsp;&nbsp;&nbsp;号：<b>$model->qr_account</b><br>昵&nbsp;&nbsp;&nbsp;&nbsp;称：<b>$model->qr_nickname</b><br>一级代理：<b>$first</b>";
                    }

                    return $str;
                }
            ],
            [
                'attribute' => 'qr_address',
                'format' => 'raw',
                'value' => function ($model) {
                    return "<div style='width: 150px;overflow: hidden'><a class='viewQrAddress btn btn-success' title='" . $model->qr_address . "'>查看二维码</a><br>" . $model->qr_address . '</div>';
                }
            ],
            [
                'attribute' => Yii::t('app/menu', 'today_order_detail'),
                'format' => 'raw',
                'value' => function ($model) {
                    //return '<a class="btn btn-success todayOrderDetail" data-username="' . $model->username . '" data-qrcode="' . $model->qr_code . '">接单详情</a>';
                    $data = QrCode::getQrCodeDailyStatistics($model->qr_code);
                    $styleMoney = '';
                    $styleTimes = '';
                    if ($data['total_success_money'] >= $model->per_day_amount) {
                        $styleMoney = 'color:red;font-weight:bold';
                    }
                    if ($data['total_success_order'] >= $model->per_day_orders) {
                        $styleTimes = 'color:red;font-weight:bold';
                    }
                    $str = '';
                    $str .= "总&nbsp;&nbsp;金&nbsp;&nbsp;额 : {$data['total_money']} 元<br/>";
                    $str .= "<span style='" . $styleMoney . "'>成功金额 : {$data['total_success_money']} 元<br/></span>";
                    $str .= "总&nbsp;&nbsp;次&nbsp;&nbsp;数 : {$data['total_order']} 次<br/>";
                    $str .= "<span style='" . $styleTimes . "'>成功次数 : {$data['total_success_order']} 次<br/></span>";
                    $str .= "成&nbsp;&nbsp;功&nbsp;&nbsp;率 : {$data['success_rate']}<br/>";
                    $str .= "预计收益 : {$data['income']} 元<br/>";
                    return $str;
                }
            ],
            [
                'attribute' => Yii::t('app/menu', 'PerDayLimit'),
                'format' => 'html',
                'value' => function ($model) {
                    $str = null;
                    $str .= "总金额：$model->per_day_amount<br>";
                    $str .= "总笔数：$model->per_day_orders";
                    return $str;
                }
            ],
            [
                'attribute' => Yii::t('app/menu', 'PerMaxMinAmount'),
                'format' => 'html',
                'value' => function ($model) {
                    $str = null;
                    $str .= "最大：$model->per_max_amount<br>";
                    $str .= "最小：$model->per_min_amount";
                    return $str;
                }
            ],
            [
                'attribute' => Yii::t('app/menu', 'qr_infos'),
                'format' => 'html',
                'value' => function ($model) {
                    $colorType = ['1' => '#01aaef', '2' => 'green', '3' => 'red', '4' => '#b54e4e'];
                    $colorStatus = ['0' => 'blue', '1' => '#313533', '2' => 'green', '9' => 'gray'];
                    $str = '类型：<b style="color: ' . $colorType[$model->qr_type] . '">' . Yii::t('app', 'qr_type')[$model->qr_type] . '</b><br>状态：<b style="color: ' . $colorStatus[$model->qr_status] . '">' . Yii::t('app', 'qr_status')[$model->qr_status] . '</b>';

                    $allowOrderTypeName = isset($model->allow_order_type) && isset(\Yii::t('app','qr_allow_order_types')[$model->allow_order_type]) ? \Yii::t('app','qr_allow_order_types')[$model->allow_order_type] : '未设置';

                    $str .= '<br/>允许接单类型：'.$allowOrderTypeName;

                    return $str;

                }
            ],
            [
                'attribute' => Yii::t('app/menu', 'LastMoneyCodeTime'),
                'format' => 'html',
                'value' => function ($model) {
                    $str = null;
                    $str .= "收款：$model->last_money_time<br>";
                    $str .= "出码：$model->last_code_time<br>";
                    $str .= "UID：$model->alipay_uid";
                    return $str;
                }
            ],
            [
                'attribute' => Yii::t('app/menu', 'all_infos'),
                'format' => 'html',
                'value' => function ($model) {
                    $str = '<br>';
                    if ($model->qr_type == 3) {
                        $str .= '开户行：<b>' . $model->bank_address . '</b>';
                    }
                    return '控制权：<b>' . $model->control . '</b><br>优&nbsp;&nbsp;&nbsp;&nbsp;先：<b>' . $model->priority . '</b><br>所在地：<b>' . $model->qr_location . '</b><br>迁移到：<b>' . Yii::$app->redis->get($model->qr_code . '_redis') . '</b>' . $str;
                }
            ],
            [
                'attribute' => Yii::t('app/menu', 'all_time'),
                'format' => 'html',
                'value' => function ($model) {
                    return "添加：$model->insert_at<br>修改：$model->update_at";
                }
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
<div id="qrcode" style="display: none;padding: 1rem">
</div>


<div id="allow_order_type_details" style="display:none;width:300px; font-size:16px; padding: 10px 10px;">
    <p>支付宝扫码：1</p>
    <p>支付宝红包：100</p>
    <p>支付宝网关：110</p>
    <p>微信扫码：2</p>
    <p>云闪付扫码：3</p>
    <p>网银转卡：101</p>
    <p>支付宝转卡：102</p>
    <p>微信转卡：103</p>
    <p>手机号转卡：104</p>

    <p style="color:red;font-size:12px; margin-top:15px">
        *批量修改允许接单类型时， 填写对应的数字即可
    </p>
</div>


<script type='text/javascript' src='/js/jquery.js'></script>
<script type="text/javascript">
    function exportExcel() {
        var t = $('#w0').serialize();
        windows = window.open('export?' + t)
    }

    //批量修改状态
    function updateStatus() {
        var statusX = $('#qr_status').val();
        if (!statusX) {
            return layer.msg('请先选择状态');
        }
        var ids = [];
        $('.select_item').each(function () {
            if ($(this).is(':checked')) {
                ids.push($(this).val());
            }
        });
        if (ids.length == 0) {
            return layer.msg('请选择二维码');
        }

        $.ajax({
            url: 'update-status',
            type: 'post',
            data: {'statusX': statusX, 'ids': ids},
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
                });
            },
            error: function () {
                return layer.msg('操作异常，请联系相关人员');
            }
        })
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
            return layer.msg('请选择二维码');
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
                });
            },
            error: function () {
                return layer.msg('操作异常，请联系相关人员');
            }
        })
    }

    //一键修改二维码字段
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
        $.merge(t, [{"name": "QrCodeSearch[columnName]", "value": batchUpdate}, {
            "name": "QrCodeSearch[columnValue]",
            "value": batchUpdateText
        }]);
        layer.confirm('此操作将会应用到当前条件下所有二维码， 确认？', function () {
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
                    });
                },
                error: function () {
                    return layer.msg('操作异常，请联系相关人员');
                }
            })
        })
    });

    //批量修改二维码所在地
    function updateLocation() {
        var locationX = $('#qrLocationX').val();
        var names = [];
        $('.select_item').each(function () {
            if ($(this).is(':checked')) {
                names.push($(this).data('name'));
            }
        });
        if (names.length == 0) {
            return layer.msg('请选择二维码');
        }

        $.ajax({
            url: 'update-location',
            type: 'post',
            data: {'locationX': locationX, 'names': names},
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
                });
            },
            error: function () {
                return layer.msg('操作异常，请联系相关人员');
            }
        })
    }

    $(function () {
        //查看收款员当天收单详情
        $(document).on('click', '.todayOrderDetail', function () {
            var now = $(this);
            var username = $(this).data('username');
            var qrcode = $(this).data('qrcode');
            if (!username) {
                return layer.msg('用户名不能为空');
            }
            if (!qrcode) {
                return layer.msg('二维码简码不能为空');
            }
            $.ajax({
                'type': 'POST',
                'url': 'today-qr-detail',
                'data': {'username': username, 'qrcode': qrcode},
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
                            txt += k + '：<b class="widthX">' + v + '</b> <br>';
                        });
                        now.parent().empty().append(txt);
                    } else {
                        return layer.msg(res.msg);
                    }
                    layer.closeAll();
                },
                error: function () {
                    return layer.msg('操作异常，请联系相关人员');
                }
            });
        });
    })
</script>

<script type="text/javascript" src="/js/jquery.qrcode.js"></script>
<script type="text/javascript">
    var j = jQuery.noConflict();
    j(function () {
        //查看二维码
        j(document).on('click', '.viewQrAddress', function () {
            var urlX = j(this).attr('title');
            j('#qrcode').empty();
            j('#qrcode').qrcode(urlX);
            layer.open({
                type: 1,
                shadeClose: true,
                content: $('#qrcode'),
                end: function () {
                    j('#qrcode').hide();
                    layer.closeAll();
                }
            });
        })
    })
</script>

<script type="text/javascript">
    //一键修改状态
    $(document).delegate('.fastChangeStatus', 'click', function () {
        var statusX = $('#qr_status').val();
        if (!statusX) {
            return layer.msg('请先选择状态');
        }
        var t = $('#w0').serializeArray();

        layer.confirm('此操作将会应用到所有二维码， 确认？', function () {
            //将新状态作为隐藏域加到form中
            var obj = "<input type='hidden' id='qrcodesearch-statusX' name='QrCodeSearch[statusX]' value='" + statusX + "'>";
            $('#w0 .row').append(obj);
            $.ajax({
                url: 'fastchangestatus',
                type: 'post',
                data: $('#w0').serialize(),
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
                    });
                },
                error: function () {
                    return layer.msg('操作异常，请联系相关人员');
                }
            })
        })
    });


    //迁移二维码
    $(document).delegate('#moveQrCodes','click',function(){
        var newCashierName = $('#new_cashier').val(),
            names = [];
        $('.select_item').each(function () {
            if ($(this).is(':checked')) {
                names.push({'qr_code':$(this).data('name'), 'username':$(this).data('username')});
            }
        });

        //console.log(newCashierName);
        //console.log(names);return false;


        if (names.length == 0) {
            return layer.msg('请选择二维码');
        }

        $.ajax({
            url: 'move-qrcodes',
            type: 'post',
            data: {'new_cashier_name': newCashierName, 'qr_codes': names},
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
                });

                setTimeout(function(){
                    if(res.result == 1){
                        window.location.reload();
                    }
                },800)

            },
            error: function () {
                return layer.msg('操作异常，请联系相关人员');
            }
        })
    });


    //批量修改字段 ， 选择"允许接单类型"时， 弹出提求框， 提示各订单类型, 便于填写值
    $(document).delegate('.mySelect', 'change', function(){
        if($(this).val() == 'allow_order_type'){
            layer.open({
                type: 1,
                shadeClose: true,
                title: '接单类型明细',
                content: $('#allow_order_type_details'),
                end: function () {
                    layer.closeAll();
                    $('#allow_order_type_details').hide();
                }
            });
        }
    });


</script>