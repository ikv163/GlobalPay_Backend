<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\UserBankcard;
use yii\helpers\ArrayHelper;

/* @var $this yii\web\View */
/* @var $searchModel app\models\UserBankcardSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/menu','User_Bankcards');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="user-bankcard-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php  echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('app/menu','Create'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
    <button type='button' class='btn btn-success' style="float: right" onclick="exportExcel()">导出Excel</button>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            [
                'class' => 'yii\grid\CheckboxColumn',
                'name' => 'id',
                'checkboxOptions' => function($model, $key, $index, $column) {
                    return ['value' => $model->id, "class"=>"select_bank"];
                },
                'headerOptions' => [
                    'width' => '30',
                ],
            ],
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
                'format' => 'html',
                'value' => function($model){

                    switch($model->card_status){
                        case 0:
                        case 1:
                            //启用
                            $color = 'green';
                            break;
                        case 2:
                            //禁用
                            $color = 'orange';
                            break;

                        case 9:
                            //删除
                            $color = 'red';
                            break;
                    }
                    return isset(UserBankcard::$BankCardStatusRel[$model->card_status]) ? "<b style='color:{$color}'>".UserBankcard::$BankCardStatusRel[$model->card_status]."</b>" : '-';
                }
            ],
            'insert_at',
            [
                'class' => 'yii\grid\ActionColumn',
                'header' => Yii::t('app/menu', 'options'),
            ],
        ],
    ]); ?>


</div>
<script type="text/javascript">
    function exportExcel() {
        var t = $('#w0').serialize();
        windows= window.open('export?'+t)
    }
</script>
