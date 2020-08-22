<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\Refund */

$this->title = Yii::t('app/menu', 'Update Refund: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/menu', 'Refunds'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('app/menu', 'Update');
?>
<div class="refund-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
