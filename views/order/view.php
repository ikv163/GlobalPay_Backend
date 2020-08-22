<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Order */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/menu', 'Orders'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="order-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app/menu', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a(Yii::t('app/menu', 'Delete'), ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => Yii::t('app/menu', 'Are you sure you want to delete this item?'),
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'order_id',
            'mch_order_id',
            'username',
            'qr_code',
            'mch_name',
            'order_type',
            [
                'attribute' => 'order_type',
                'value' => function($model) {
                    $statusArr = Yii::t('app', 'order_type');
                    return isset($statusArr[$model->order_type]) ? $statusArr[$model->order_type] : $model->order_type;
                }
            ],
            'order_fee',
            'order_amount',
            'benefit',
            'actual_amount',
            'callback_url:url',
            'notify_url:url',
            [
                'attribute' => 'order_status',
                'value' => function($model) {
                    $statusArr = Yii::t('app', 'order_status');
                    return isset($statusArr[$model->order_status]) ? $statusArr[$model->order_status] : $model->order_status;
                }
            ],
            [
                'attribute' => 'notify_status',
                'value' => function($model) {
                    $statusArr = Yii::t('app', 'notify_status');
                    return isset($statusArr[$model->notify_status]) ? $statusArr[$model->notify_status] : $model->notify_status;
                }
            ],
            'expire_time',
            'read_remark',
            'insert_at',
            'update_at',
            'operator',
        ],
    ]) ?>

</div>
