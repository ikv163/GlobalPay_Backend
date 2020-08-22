<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\helpers\ArrayHelper;
use app\models\UserBankcard;

/* @var $this yii\web\View */
/* @var $model app\models\UserBankcard */

$this->title = Yii::t('app/menu','View_User_Bankcard');
$this->params['breadcrumbs'][] = ['label' => Yii::t('app/menu','User_Bankcards'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="user-bankcard-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a(Yii::t('app/menu','Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
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
            'username',
            [
                'attribute' => 'user_type',
                'value' => function($model){
                    return isset(UserBankcard::$UserTypeRel[$model->user_type]) ? UserBankcard::$UserTypeRel[$model->user_type] : '-';
                }
            ],
            [
                'attribute' => 'card_status',
                'value' => function($model){
                    return isset(UserBankcard::$BankCardStatusRel[$model->card_status]) ? UserBankcard::$BankCardStatusRel[$model->card_status] : '-';
                }
            ],
            'insert_at',
            'update_at',
        ],
    ]) ?>

</div>
