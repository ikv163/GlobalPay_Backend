<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;
use app\models\SysBankcard;
/* @var $this yii\web\View */
/* @var $model app\models\SysBankcard */

$this->title = Yii::t('app/menu','View_User_Bankcard');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/menu', 'Sys_Bankcard_Management'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="sys-bankcard-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app/menu', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'bankcard_number',
            'bankcard_owner',
            [
                'attribute' => Yii::t('app/menu', 'Bank_Name'),
                'value' => function($model){
                    $configBankTypes = Yii::t('app', 'BankTypes');
                    $configBankTypes = $configBankTypes ? ArrayHelper::map($configBankTypes, 'BankTypeCode', 'BankTypeName') : array();
                    return $configBankTypes && isset($configBankTypes[$model->bank_code]) ? $configBankTypes[$model->bank_code] : '-';
                }
            ],
            'bankcard_address',
            [
                'attribute' => 'card_status',
                'value' => function($model){
                    return isset(SysBankcard::$BankCardStatusRel[$model->card_status]) ? SysBankcard::$BankCardStatusRel[$model->card_status] : '-';
                }
            ],
            'balance',
            'max_balance',
            [
                'attribute' => 'card_owner',
                'format' => 'raw',
                'value' => function($model){
                    return isset($model->card_owner) && is_numeric($model->card_owner) && $model->card_owner > 0 && intval($model->card_owner) == $model->card_owner && isset(\Yii::t('app', 'sys_bankcard_owner')[$model->card_owner]) ? \Yii::t('app', 'sys_bankcard_owner')[$model->card_owner] : '未设置';
                }
            ],
            'remark',

            'insert_at',
            'update_at',
        ],
    ]) ?>

</div>
