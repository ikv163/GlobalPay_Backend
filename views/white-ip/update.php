<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\WhiteIp */

$this->title = Yii::t('app/menu', 'Update White Ip: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/menu', 'white ip'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app/menu', 'Update');
?>
<div class="white-ip-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
