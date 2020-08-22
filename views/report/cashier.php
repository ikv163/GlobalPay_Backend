<?php

use yii\helpers\Html;
use yii\grid\GridView;
use app\models\Report;


/* @var $this yii\web\View */
/* @var $searchModel app\models\ReportSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('app/menu', 'Cashier_Report');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="report-index">

    <h1><?= Html::encode($this->title) ?></h1>

    <?php echo $this->render('_search', ['model' => $searchModel, 'action' => 'cashier']); ?>
    <button type="button" class="btn btn-success calcReport">统计报表</button>
    <button type="button" class="btn btn-success export">导出一级代理报表</button>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'columns' => [
            'username',
            [
                'attribute' => 'datas',
                'format' => 'raw',
                'value' => function ($model) {
                    $datas = json_decode($model->datas, 1);
                    $str = '<div class="calcResult">';
                    $translate = [
                        'totalNumbersAlipay' => '总笔数[支付宝]',
                        'totalSuccessNumbersAlipay' => '成功笔数[支付宝]',
                        'totalSuccessRateAlipay' => '成功率[支付宝]',
                        'totalAmountAlipay' => '总金额[支付宝]',
                        'totalSuccessAmountAlipay' => '成功金额[支付宝]',
                        'lostOrderRateAlipay' => '掉单率[支付宝]',
                        'lostOrdersAlipay' => '掉单笔数[支付宝]',
                        'editMoneyOrdersAlipay' => '修改金额笔数[支付宝]',
                        'grossProfitAlipay' => '毛利润[支付宝]',
                        'netProfitAlipay' => '纯利润[支付宝]',
                        'firstCashierFeeAlipay' => '一级代理费用[支付宝]',
                        'totalNumbersWechat' => '总笔数[微信]',
                        'totalSuccessNumbersWechat' => '成功笔数[微信]',
                        'totalSuccessRateWechat' => '成功率[微信]',
                        'totalAmountWechat' => '总金额[微信]',
                        'totalSuccessAmountWechat' => '成功金额[微信]',
                        'lostOrderRateWechat' => '掉单率[微信]',
                        'lostOrdersWechat' => '掉单笔数[微信]',
                        'editMoneyOrdersWechat' => '修改金额笔数[微信]',
                        'grossProfitWechat' => '毛利润[微信]',
                        'netProfitWechat' => '纯利润[微信]',
                        'firstCashierFeeWechat' => '一级代理费用[微信]',
                        'totalNumbersUnionPay' => '总笔数[云闪付]',
                        'totalSuccessNumbersUnionPay' => '成功笔数[云闪付]',
                        'totalSuccessRateUnionPay' => '成功率[云闪付]',
                        'totalAmountUnionPay' => '总金额[云闪付]',
                        'totalSuccessAmountUnionPay' => '成功金额[云闪付]',
                        'lostOrderRateUnionPay' => '掉单率[云闪付]',
                        'lostOrdersUnionPay' => '掉单笔数[云闪付]',
                        'editMoneyOrdersUnionPay' => '修改金额笔数[云闪付]',
                        'grossProfitUnionPay' => '毛利润[云闪付]',
                        'netProfitUnionPay' => '纯利润[云闪付]',
                        'firstCashierFeeUnionPay' => '一级代理费用[云闪付]',

                        'totalNumbersBankCard' => '总笔数[银行卡]',
                        'totalSuccessNumbersBankCard' => '成功笔数[银行卡]',
                        'totalSuccessRateBankCard' => '成功率[银行卡]',
                        'totalAmountBankCard' => '总金额[银行卡]',
                        'totalSuccessAmountBankCard' => '成功金额[银行卡]',
                        'lostOrderRateBankCard' => '掉单率[银行卡]',
                        'lostOrdersBankCard' => '掉单笔数[银行卡]',
                        'editMoneyOrdersBankCard' => '修改金额笔数[银行卡]',
                        'grossProfitBankCard' => '毛利润[银行卡]',
                        'netProfitBankCard' => '纯利润[银行卡]',
                        'firstCashierFeeBankCard' => '一级代理费用[银行卡]',

                        'totalNumbersTotal' => '总笔数[总计]',
                        'totalSuccessNumbersTotal' => '成功笔数[总计]',
                        'totalSuccessRateTotal' => '成功率[总计]',
                        'totalAmountTotal' => '总金额[总计]',
                        'totalSuccessAmountTotal' => '成功金额[总计]',
                        'lostOrderRateTotal' => '掉单率[总计]',
                        'lostOrdersTotal' => '掉单笔数[总计]',
                        'editMoneyOrdersTotal' => '修改金额笔数[总计]',
                        'grossProfitTotal' => '毛利润[总计]',
                        'netProfitTotal' => '纯利润[总计]',
                        'firstCashierFeeTotal' => '一级代理费用[总计]',
                        'result' => '统计结果',
                    ];
                    $br = 0;
                    foreach ($datas as $k => $v) {
                        if ($br <= 10) {
                            $color = '#01aaef';
                        } elseif ($br > 10 && $br <= 21) {
                            $color = 'green';
                        } elseif ($br > 21 && $br <= 32) {
                            $color = 'red';
                        } elseif ($br > 32 && $br <= 43) {
                            $color = 'gray';
                        } else {
                            $color = 'blue';
                        }
                        if ($br == 10 || $br == 21 || $br == 32 || $br == 43) {
                            $str = $str . '<p style=color:' . $color . '><b>' . $translate[$k] . '</b>：' . $v . '</p><br>';
                            $br++;
                        } else {
                            $str = $str . '<p style=color:' . $color . '><b>' . $translate[$k] . '</b>：' . $v . '</p>';
                            $br++;
                        }
                    }
                    return $str . '</div>';
                }
            ],
            [
                'attribute' => 'finance_date',
                'value' => function ($model) {
                    return $model->finance_date;
                }
            ],
        ],
    ]); ?>
</div>
<style type="text/css">
    .calcResult p {
        display: inline-block;
        padding: 0.3rem;
        width: 200px;
        margin-left: 1rem;
        cursor: pointer;
    }
</style>
<script type="text/javascript">
    $(function () {
        $(document).on('click', '.calcReport', function () {
            var theTime = $('#reportsearch-begintime').val();
            $.ajax({
                type: 'post',
                url: 'cashier-data',
                data: {'date': theTime},
                beforeSend: function () {
                    layer.msg('统计中...', {
                        icon: 16,
                        shade: [0.1, '#fff'],
                        time: 30000,
                    });
                },
                success: function (res) {
                    layer.msg(res.msg, {
                        shade: [0.1, '#fff'],
                        time: 5000,
                    });
                    $('.searchButton').click();
                },
                error: function () {
                    return layer.msg('操作异常，请联系相关人员');
                }
            })
        });

        $(document).on('click', '.export', function () {
            var t = $('.report-search #w0').serialize();
            windows = window.open('/report/export-cashier?' + t);
        });
    })
</script>
