<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use app\models\Deposit;

/* @var $this yii\web\View */
/* @var $model app\models\Deposit */

$this->title = Yii::t('app/menu', 'View_Deposit_Order');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/menu','Deposit_Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="deposit-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app/menu','Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'system_deposit_id',
            'out_deposit_id',
            'username',
            'deposit_money',
            [
                'attribute'=>'deposit_status',
                'format' => 'html',
                'value' => function($model){
                    return isset(Deposit::$OrderStatusRel[$model->deposit_status]) ? Deposit::$OrderStatusRel[$model->deposit_status] : '-';
                }
            ],
            'deposit_remark',
            'system_remark',
            'insert_at',
            'update_at',
        ],
    ]) ?>

</div>
