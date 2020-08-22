<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\RefundSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/menu', 'Refunds');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="refund-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'name' => 'id',
                'checkboxOptions' => function ($model) {
                    return ['value' => $model->id, "class" => "select_item", 'data-name' => $model->order_id];
                }
            ],
            [
                'attribute' => Yii::t('app/menu', 'all_users'),
                'format' => 'html',
                'value' => function ($model) {
                    return "所属用户名：<b>$model->username</b><br>所属二维码：<b>$model->qr_code</b><br>后台操作者：<b>$model->operator</b>";
                }
            ],
            [
                'attribute' => Yii::t('app/menu', 'all_orderId'),
                'format' => 'html',
                'value' => function ($model) {
                    return "平台订单号：<b>$model->order_id</b><br>商户订单号：<b>$model->mch_order_id</b>";
                }
            ],
            [
                'attribute' => 'photo',
                'format' => 'raw',
                'value' => function ($model) {
                    return "<div class='photos'><img class='imgClick' style='width:120px;cursor: pointer' src='" . $this->params['ApiDomain'] . '/' . $model->photo . "'/></div>";
                }
            ],
            [
                'attribute' => Yii::t('app/menu', 'all_money'),
                'format' => 'html',
                'value' => function ($model) {
                    $feeType = $model->order_type == 1 ? 'alipay_rate' : 'wechat_rate';
                    $fee = bcdiv(bcmul($model->order_amount, $model->$feeType), 100, 2);
                    return "订单金额：<b>$model->order_amount</b><br>返款金额：<b>$model->refund_money</b><br>手&nbsp;&nbsp;续&nbsp;&nbsp;费：<b>$fee</b>";
                }
            ],
            [
                'attribute' => Yii::t('app/menu', 'all_infos'),
                'format' => 'html',
                'value' => function ($model) {
                    $refundStatus = [1 => 'blue', 2 => 'green', 3 => 'red'];
                    return "返款类型：<b>" . Yii::t('app', 'refund_type')[$model->refund_type] . "</b><br>返款状态：<b style='color:" . $refundStatus[$model->refund_status] . "'>" . Yii::t('app', 'refund_status')[$model->refund_status] . "</b><br>订单状态：<b>" . Yii::t('app', 'order_status')[$model->order_status] . "</b><br>结算状态：<b>" . Yii::t('app', 'is_settlement')[$model->is_settlement] . "</b><br>返款备注：<b>$model->remark</b><br>系统备注：<b>$model->system_remark</b>";
                }
            ],
            [
                'attribute' => Yii::t('app/menu', 'all_time'),
                'format' => 'html',
                'value' => function ($model) {
                    return "返款时间：<b>$model->insert_at</b><br>修改时间：<b>$model->update_at</b>";
                }
            ],
            [
                "class" => "yii\grid\ActionColumn",
                "template" => "{ok} {no}",
                "buttons" => [
                    "ok" => function ($url, $model, $key) {
                        if ($model->refund_status == 1) {
                            return '<a class="btn btn-success refundOk" data-id="' . $model->id . '">已到账</a> | ';
                        }
                    },
                    "no" => function ($url, $model, $key) {
                        if ($model->refund_status == 1) {
                            return '<a class="btn btn-success refundNo" data-id="' . $model->id . '">驳回</a>';
                        }
                    }
                ],
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
<div class="row refundContent" style="display: none">
    <div class="col-sm-12">
        返款类型：
        <select id="refundType" class="form-control">
            <option value="">请选择返款类型</option>
            <option value="1">全额返款</option>
            <option value="2">已扣佣金返款</option>
            <option value="3">待确定</option>
        </select>
    </div>
    <div class="col-sm-12">
        备注：
        <input type="text" id="systemRemark" class="form-control" placeholder="非必填项">
    </div>
</div>
<script type="text/javascript">
    $(function () {
        $(document).on('click', '.imgClick', function () {
            layer.photos({
                photos: '.photos',
                shadeClose: true,
                closeBtn: 2,
                anim: 3
            });
        });

        //驳回
        $(document).on('click', '.refundNo', function () {
            var idX = $(this).data('id');
            layer.confirm('确认执行此操作？', function () {
                $.ajax({
                    'url': 'refund-no',
                    'type': 'post',
                    'data': {'id': idX},
                    success: function (res) {
                        return layer.msg(res.msg);
                    },
                    error: function () {
                        return layer.msg('操作异常');
                    }
                });
            });
        });

        //已到账
        $(document).on('click', '.refundOk', function () {
            var idX = $(this).data('id');
            if (!idX) {
                return layer.msg('缺少必要参数');
            }
            layer.open({
                title: '返款审核',
                content: $('.refundContent').html(),
                yes: function (index, layero) {
                    var refundType = $('.layui-layer-content #refundType').val();
                    var systemRemark = $('.layui-layer-content #systemRemark').val();
                    if (refundType == '') {
                        console.log(refundType);
                        alert('请选择返款类型');
                        return false;
                    }
                    $.ajax({
                        'url': 'refund-ok',
                        'type': 'post',
                        'data': {'id': idX, 'refundType': refundType, 'systemRemark': systemRemark},
                        success: function (res) {
                            return layer.msg(res.msg);
                        },
                        error: function () {
                            return layer.msg('操作异常');
                        }
                    });
                }
            });
        });
    })
</script>