<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\PayChannel */

$this->title = '添加渠道';
$this->params['breadcrumbs'][] = ['label' => '渠道管理', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="pay-channel-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
