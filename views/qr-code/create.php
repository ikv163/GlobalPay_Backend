<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model app\models\QrCode */

$this->title = Yii::t('app/menu', 'Create Qr Code');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/menu', 'Qr Codes'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="qr-code-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
        'cashiers' => $cashiers,
    ]) ?>

</div>
