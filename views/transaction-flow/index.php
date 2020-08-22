<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/* @var $this yii\web\View */
/* @var $searchModel app\models\TransactionFlowSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/menu', 'Transaction Flows');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transaction-flow-index">

    <h1><?= Html::encode($this->title) ?></h1>
    <?php Pjax::begin(); ?>
    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <button type='button' class='btn btn-success' style="float: right" onclick="exportExcel()">导出Excel</button>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'attribute' => Yii::t('app/menu', 'client_infos'),
                'format' => 'html',
                'value' => function ($model) {
                    return "编号：<b class='c_order_amount'>$model->client_id</b><br>简码：<b class='c_actual_amount'>$model->client_code</b>";
                }
            ],
            [
                'attribute' => 'trade_type',
                'format' => 'html',
                'value' => function ($model) {
                    $color = [1 => '#01aaef', 2 => 'green'];
                    $str = '来源细分：<b style="color:' . $color[$model->trade_cate] . '">' . Yii::t('app', 'trade_cate')[$model->trade_cate] . '</b>';
                    return  '流水来源：'.Yii::t('app', 'trade_type')[$model->trade_type].'<br>'.$str;
                }
            ],
            [
                'attribute' =>'read_remark',
                'format' => 'html',
                'value' => function ($model) {
                    return "交易单号：<b>$model->trans_id</b><br>匹配单号：<b>$model->read_remark</b><br>流水备注：<b>$model->trans_remark</b>";
                }
            ],
            [
                'attribute' => 'trans_account',
                'format' => 'html',
                'value' => function ($model) {
                    return "所属账户：<b class='c_order_amount'>$model->trans_account</b><br>交易姓名：<b class='c_actual_amount'>$model->trans_username</b>";
                }
            ],
            [
                'attribute' => 'trans_status',
                'format' => 'html',
                'value' => function ($model) {
                    $color = [0 => 'gray', 1 => 'orange', 2 => 'green', 3 => 'red'];
                    return '<b style="color:' . $color[$model->trans_status] . '">' . Yii::t('app', 'trans_status')[$model->trans_status] . '</b>';
                }
            ],
            [
                'attribute' => 'trans_type',
                'format' => 'html',
                'value' => function ($model) {
                    $color = [0 => 'red', 1 => 'green'];
                    return '<b style="color:' . $color[$model->trans_type] . '">' . Yii::t('app', 'trans_type')[$model->trans_type] . '</b>';
                }
            ],
            [
                'attribute' => Yii::t('app/menu', 'all_money'),
                'format' => 'html',
                'value' => function ($model) {
                    return "交易金额：<b class='c_order_amount'>$model->trans_amount</b><br>之前金额：<b class='c_actual_amount'>$model->before_balance</b><br>当前金额：<b>$model->trans_balance</b><br>手&nbsp;&nbsp;续&nbsp;&nbsp;费：<b>$model->trans_fee</b>";
                }
            ],
            [
                'attribute' => Yii::t('app/menu', 'all_time'),
                'format' => 'html',
                'value' => function ($model) {
                    return "交易时间：$model->trans_time<br>添加时间：$model->insert_at<br>修改时间：$model->update_at<br>匹配时间：$model->pick_at";
                }
            ],
//            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
<script type="text/javascript">
    function exportExcel() {
        var t = $('#w0').serialize();
        windows = window.open('export?' + t)
    }
</script>