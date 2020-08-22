<?php

use app\common\DES;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Merchant */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/menu', 'Merchants'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="merchant-view">

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
            'mch_name',
            'mch_code',
            'mch_key',
            'mch_status',
            'available_money',
            'used_money',
            'balance',
            [
                'attribute' => 'pay_password',
                'value' => function ($model) {
                    if (isset($model->pay_password) && $model->pay_password != null) {
                        $des = new DES(Yii::$app->params['password'], 'DES-CBC', DES::OUTPUT_BASE64);
                        $model->pay_password = $des->decrypt($model->pay_password);
                    }
                    return $model->pay_password;
                }
            ],
            'telephone',
            'wechat_rate',
            'alipay_rate',
            'insert_at',
            'update_at',
            'remark',
        ],
    ]) ?>

</div>
