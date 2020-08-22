<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\QrCode */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/menu', 'Qr Codes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="qr-code-view">

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
            'username',
            'qr_code',
            'qr_address',
            'qr_nickname',
            'qr_account',
            'per_max_amount',
            'per_min_amount',
            'per_day_amount',
            'per_day_orders',
            [
                'attribute' => 'qr_type',
                'value' => function($model){
                    $qrTypeArr = Yii::t('app', 'qr_type');
                    return isset($qrTypeArr[$model->qr_type]) ? $qrTypeArr[$model->qr_type] : $model->qr_type;
                }
            ],
            [
                'attribute' => 'qr_status',
                'value' => function($model){
                    $qrTypeArr = Yii::t('app', 'qr_status');
                    return isset($qrTypeArr[$model->qr_status]) ? $qrTypeArr[$model->qr_status] : $model->qr_status;
                }
            ],
            'priority',
            [
                'attribute' => 'allow_order_type',
                'value' => function($model){
                    $allowOrderTypes = Yii::t('app', 'qr_allow_order_types');
                    return isset($allowOrderTypes[$model->allow_order_type]) ? $allowOrderTypes[$model->allow_order_type] : $model->allow_order_type;
                }
            ],
            'last_money_time',
            'control',
            [
                'attribute' => 'is_shopowner',
                'value' => function($model){
                    $qrTypeArr = Yii::t('app', 'is_shopowner');
                    return isset($qrTypeArr[$model->is_shopowner]) ? $qrTypeArr[$model->is_shopowner] : $model->is_shopowner;
                }
            ],
            'qr_relation',
            'insert_at',
            'update_at',
        ],
    ]) ?>

</div>
