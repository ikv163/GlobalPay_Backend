<?php

use app\common\DES;
use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model app\models\Cashier */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/menu', 'Cashiers'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="cashier-view">

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
            'login_password',
            [
                'attribute' => 'pay_password',
                'value' => function ($model) {
                    $des = new DES(Yii::$app->params['password'], 'DES-CBC', DES::OUTPUT_BASE64);
                    return $des->decrypt($model->pay_password);
                }
            ],
            'income',
            'security_money',
            'wechat_rate',
            'alipay_rate',
            'wechat_amount',
            'alipay_amount',
            'parent_name',
            'wechat',
            'alipay',
            'telephone',
            'agent_class',
            [
                'attribute' => 'cashier_status',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->cashier_status == 1) {
                        $html = '<span style="color: green">启用</span>';
                    } else {
                        $html = '<span style="color: red">禁用</span>';
                    }
                    return $html;
                }
            ],
            'insert_at',
            'update_at',
            'login_at',
            'remark',
        ],
    ]) ?>

</div>
