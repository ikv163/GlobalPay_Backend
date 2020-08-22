<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\TransactionFlow */

$this->title = Yii::t('app/menu', 'Create Transaction Flow');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/menu', 'Transaction Flows'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="transaction-flow-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
