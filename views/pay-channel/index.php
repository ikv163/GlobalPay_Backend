<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\PayChannelSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = '支付渠道';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pay-channel-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('添加渠道', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'options' => [ 'style' => 'table-layout:fixed;' ],
        'columns' => [
            [
                'attribute' => '渠道信息',
                'format' => 'html',
                'headerOptions' => [
                    'width' => '210',
                ],
                'value' => function ($model) {
                    $statusColor = [0 => 'red', 1 => 'green'];
                    return '渠道名称：<b>'.$model->channel_name.'</b><br>支付类型：<b>'.Yii::t('app', 'bank_card_pay_type')[$model->pay_type].'</b><br>渠道状态：<b style="color:' . $statusColor[$model->channel_status] . '">' . Yii::t('app', 'channel_status')[$model->channel_status] . '</b><br>'."添加时间：<b>$model->insert_at</b><br>修改时间：<b>$model->update_at</b>";
                }
            ],
            [
                'attribute' => '金额集合',
                'headerOptions' => [
                    'width' => '160',
                ],
                'format' => 'html',
                'value' => function ($model) {
                    return '每笔最大：<b>' . $model->per_max_amount. '</b><br>每笔最小：<b>' . $model->per_min_amount. '</b>';
                }
            ],
            [
                'attribute' => 'user_level',
                'contentOptions' => ['style' => 'width:160px;word-wrap:break-word; word-break:break-all;'],
            ],
            [
                'attribute' => 'credit_level',
                'contentOptions' => ['style' => 'width:200px;word-wrap:break-word; word-break:break-all;'],
            ],
            [
                'attribute' => '银行卡集合',
                'format' => 'raw',
                'value' => function ($model) {
                    $all = \app\models\PayChannelRelation::find()->where(['channel_id' => $model->id])->select(['qr_code'])->all();
                    $str = '<div class="channelContent">';
                    foreach ($all as $v) {
                        $str .= '<b>' . $v->qr_code . '</b> | ';
                    }
                    $str .= '</div><a data-id="' . $model->id . '" class="btn btn-success updateChannel" href="javascript:;">编辑</a>';
                    return $str;
                }
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
<div id="cardContent">

</div>
<style type="text/css">
    #cardContent {
        display: none;
        padding: 1rem;
    }

    #cardContent .selectCard {
        margin-right: 0.5rem;
        cursor: pointer;
    }
</style>
<script type="text/javascript">
    $(function () {
        $('.updateChannel').click(function () {
            var now = $(this);
            var idX = now.data('id');
            $.ajax({
                url: 'get-bank-card',
                type: 'post',
                data: {'id': idX},
                beforeSend: function () {
                    layer.msg('处理中...', {
                        icon: 16,
                        shade: [0.1, '#fff'],
                        time: 3000,
                    });
                },
                success: function (res) {
                    if (res.data.length != 0) {
                        $('#cardContent').empty();

                        $('#cardContent').append("<div style='color:orangered;font-size:18px;height:30px; line-height:30px;'><input type='checkbox' id='selectAll' style='margin:0 5px 0 0;' />全选</div>");

                        $.each(res.data, function (k, v) {
                            var temp = 'btn-warning';
                            if (now.prev('.channelContent').children().length > 0) {
                                now.prev('.channelContent').find('b').each(function () {
                                    if ($(this).text() == v['qr_code']) {
                                        temp = 'btn-success';
                                        return false;
                                    }
                                });
                            }
                            $('#cardContent').append('<a class="btn ' + temp + ' selectCard" href="javascript:;">' + v['qr_code'] + '</a>');
                        });

                        layer.open({
                            type: 1
                            , title: "银行卡列表--目前有 <b>"+res.data.length+"</b> 张银行卡"
                            , closeBtn: false
                            , area: ['70%', '70%']
                            , id: 'LAY_layuipro'
                            , btn: ['确认', '取消']
                            , btnAlign: 'c'
                            , moveType: 1
                            , content: $('#cardContent')
                            , yes: function (index, layero) {
                                var arr = [];
                                $('#cardContent').find('.btn-success').each(function () {
                                    arr.push($(this).text());
                                });
                                $.ajax({
                                    'type': 'POST',
                                    'url': 'set-channel-bank',
                                    'data': {'id': idX, 'bankcards': arr},
                                    beforeSend: function () {
                                        layer.msg('处理中...', {
                                            icon: 16,
                                            shade: [0.1, '#fff'],
                                            time: 10000,
                                        });
                                    },
                                    success: function (res) {
                                        layer.msg(res.msg, {
                                            shade: [0.1, '#fff'],
                                            time: 2000,
                                            end: function () {
                                                window.location.reload();
                                            }
                                        });
                                    },
                                    error: function () {
                                        return layer.msg('操作异常，请联系相关人员');
                                    }
                                });
                            }
                            , end: function () {
                                $('#cardContent').hide();
                            }
                        });
                    } else {
                        layer.alert('未查询到符合要求的银行卡');
                    }
                }
                ,
                error: function () {
                    return layer.msg('操作异常，请联系相关人员');
                }
            })
        });
        $(document).on('click', '.selectCard', function () {
            if ($(this).hasClass('btn-warning')) {
                $(this).removeClass('btn-warning');
                $(this).addClass('btn-success');
            } else {
                $(this).removeClass('btn-success');
                $(this).addClass('btn-warning');
            }
        });

        //银行卡全选/全不选
        $(document).on('click', '#selectAll', function(res){
            var chk = $(this).prop('checked');
            if(chk){
                $('.selectCard').addClass('btn-success').removeClass('btn-warning');
            }else{
                $('.selectCard').addClass('btn-warning').removeClass('btn-success');
            }
        });
    })
</script>
