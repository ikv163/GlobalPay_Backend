<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\FinanceDetail;

/* @var $this yii\web\View */
/* @var $searchModel app\models\FinanceDetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/menu','Finance_Details');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="finance-detail-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>
    <button type='button' class='btn btn-success' style="float: right" onclick="exportExcel()">导出Excel</button>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'username',
            [
                'attribute'=>'user_type',
                'value' => function($model){
                    return isset(FinanceDetail::$UserTypeRel[$model->user_type]) ? FinanceDetail::$UserTypeRel[$model->user_type] : '-';
                }
            ],
            'change_amount',
            'before_amount',
            'after_amount',
            [
                'attribute'=>'finance_type',
                'value' => function($model){
                    return isset(FinanceDetail::$FinanceTypeRel[$model->finance_type]) ? FinanceDetail::$FinanceTypeRel[$model->finance_type] : '-';
                }
            ],
            'remark',
            'insert_at',
        ],
    ]); ?>
</div>
<script type="text/javascript">
    function exportExcel() {
        var t = $('#w0').serialize();
        windows= window.open('export?'+t)
    }
</script>
