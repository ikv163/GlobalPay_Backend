<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PayChannel */

$this->title = '更新支付渠道: ' . $model->id;
$this->params['breadcrumbs'][] = ['label' => '支付渠道管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = '更新';
?>
<div class="pay-channel-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
