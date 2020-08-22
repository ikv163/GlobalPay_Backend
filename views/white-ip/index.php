<?php

use yii\helpers\Html;
use yii\grid\GridView;

/* @var $this yii\web\View */
/* @var $searchModel app\models\WhiteIpSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/menu', 'white ip');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="white-ip-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app/menu', 'Create White Ip'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'id',
            'user_ip',
            'ip_remark',
            [
                'attribute' => 'ip_status',
                'format' => 'html',
                'value' => function ($model) {
                    $color = ['0' => 'red', '1' => 'green', '2' => 'gray'];
                    return '<b style="color: ' . $color[$model->ip_status] . '">' . Yii::t('app', 'ip_status')[$model->ip_status] . '</b>';
                },
            ],
            'insert_at',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>


</div>
