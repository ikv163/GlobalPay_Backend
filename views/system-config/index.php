<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\SystemConfigSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/menu', 'System Configs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="system-config-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <div class="row">
        <div class="col-sm-2">
            <?= Html::a(Yii::t('app/menu', 'Create System Config'), ['create'], ['class' => 'btn btn-success']) ?>
        </div>
        <div id="manualRedis">
            <input type="text" id="redisKey" placeholder="Redis键"> ==>
            <input type="text" id="redisValue" placeholder="Redis值"> -
            <input type="text" id="redisTimedout" placeholder="过期时间">
            <button class="btn btn-success btn-labeled redisQuery" data-type="2"><i class="fa fa-plus btn-label"></i>查询
            </button>
            <button class="btn btn-success btn-labeled redisQuery" data-type="1"><i class="fa fa-plus btn-label"></i>设置
            </button>
        </div>
    </div>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'config_name',
            'config_code',
            'config_value',
            [
                'attribute' => 'config_status',
                'format' => 'html',
                'headerOptions' => [
                    'width' => '80',
                    'style' => 'text-align:center',
                ],
                'contentOptions' => [
                    'style' => 'text-align:center',
                ],
                'value' => function ($model) {
                    $color = ['0' => 'red', '1' => 'green', '2' => 'gray'];
                    return '<b style="color: ' . $color[$model->config_status] . '">' . Yii::t('app', 'system_config_status')[$model->config_status] . '</b>';
                },
            ],
            [
                'attribute' => 'remark',
                'value' => function ($model) {
                    return $model->remark != null ? $model->remark : '--';
                },
            ],
            [
                'attribute' => Yii::t('app/menu', 'all_time'),
                'format' => 'html',
                'headerOptions' => [
                    'width' => '180',
                ],
                'value' => function ($model) {
                    return '添加：' . $model->insert_at . '<br>修改：' . $model->update_at;
                },
            ],
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

    <style type="text/css">
        #manualRedis {
            display: inline-block;
            text-align: right;
            float: right;
            padding-bottom: 0.5rem;
        }

        #manualRedis input {
            width: 180px;
            height: 33px;
            padding: 1rem;
        }
    </style>
    <script type="text/javascript">
        $(function () {
            layer.tips('Redis手动查询/设置', '#manualRedis');
            //手动查询/设置Redis
            $(document).on('click', '.redisQuery', function () {
                var queryString = $('#redisKey').val();
                var xType = $(this).data('type');
                var redisTimedout = $('#redisTimedout').val();
                var redisValue = $('#redisValue').val();

                if (!queryString) {
                    return layer.msg('请输入Redis键');
                }
                $.ajax({
                    url: '/system-config/redis-query',
                    type: 'post',
                    data: {
                        'queryString': queryString,
                        'xType': xType,
                        'redisTimedout': redisTimedout,
                        'redisValue': redisValue
                    },
                    success: function (res) {
                        if (!res.res) {
                            if (xType == 1) {
                                return layer.msg('设置失败');
                            } else if (xType == 2) {
                                $('#redisValue').val('');
                                $('#redisTimedout').val('');
                                return layer.msg('未查询到相关信息');
                            }
                        } else {
                            if (xType == 1) {
                                return layer.msg('设置成功');
                            } else if (xType == 2) {
                                $('#redisValue').val(res.res);
                                $('#redisTimedout').val(res.time);
                                return layer.msg('查询成功');
                            }
                        }
                    },
                    error: function () {
                        $('#redisValue').val('');
                        return layer.msg('查询时发生异常');
                    }
                })
            })
        })
    </script>
</div>
