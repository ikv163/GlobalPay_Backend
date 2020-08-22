<?php

namespace app\controllers;

use app\models\Cashier;
use app\models\Merchant;
use app\models\Order;
use app\models\Report;
use app\models\ReportSearch;
use Yii;
use yii\filters\VerbFilter;
use yii\web\Controller;

/**
 * ReportController implements the CRUD actions for Report model.
 */
class ReportController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
        ];
    }

    //平台日收益报表
    public function actionPlatform()
    {
        $searchModel = new ReportSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Report::$ReportTypePlatform);

        return $this->render('platform', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    //平台日收益报表--产生数据
    public function actionPlatformData()
    {
        try {

            Yii::$app->response->format = 'json';
            $date = $_POST['date'];
            if (!$date) {
                return ['msg' => '请选取要统计的日期', 'result' => 0];
            }
            $report = Report::find()->where(['user_type' => 1, 'finance_date' => $date])->one();

            if (!$report) {
                $report = new Report();
                $report->username = '全民平台';
                $report->user_type = 1;
                $report->finance_date = $date;
            }
            //总数据
            $datas = [];
            //要统计的订单类型
            $orderType = [1 => 'Alipay', 2 => 'Wechat', 3 => 'UnionPay', 4 => 'BankCard'];

            foreach ($orderType as $k => $v) {
                $orders = Order::find()->where(['>=', 'insert_at', $date])->andWhere(['<=', 'insert_at', date('Y-m-d', strtotime($date) + 86400)])->andWhere(['=', 'order_type', $k])->asArray()->all();

                $allStutas = array_count_values(array_column($orders, 'order_status'));
                $allStutas[2] = isset($allStutas[2]) ? $allStutas[2] : 0;
                $allStutas[5] = isset($allStutas[5]) ? $allStutas[5] : 0;

                //总笔数
                $datas['totalNumbers' . $v] = count($orders);
                //总成功笔数
                $datas['totalSuccessNumbers' . $v] = bcadd($allStutas[2], $allStutas[5], 0);
                //成功率
                if ($datas['totalNumbers' . $v] == 0) {
                    $datas['totalSuccessRate' . $v] = '0%';
                } else {
                    $datas['totalSuccessRate' . $v] = bcmul($datas['totalSuccessNumbers' . $v] / $datas['totalNumbers' . $v], 100, 2) . '%';
                }
                //总金额
                $datas['totalAmount' . $v] = array_sum(array_column($orders, 'order_amount'));
                //成功金额
                $datas['totalSuccessAmount' . $v] = 0;
                //掉单笔数
                $datas['lostOrders' . $v] = 0;
                //掉单率
                $datas['lostOrderRate' . $v] = 0;
                //修改金额笔数
                $datas['editMoneyOrders' . $v] = 0;
                //毛利润
                $datas['grossProfit' . $v] = 0;
                //纯利润
                $datas['netProfit' . $v] = 0;
                //一级代理费用
                $datas['firstCashierFee' . $v] = 0;

                foreach ($orders as $order_k => $order_v) {
                    //成功总额
                    if (in_array($order_v['order_status'], [2, 5])) {
                        $datas['totalSuccessAmount' . $v] = bcadd($datas['totalSuccessAmount' . $v], $order_v['actual_amount'], 2);
                        $datas['grossProfit' . $v] = bcadd($datas['grossProfit' . $v], $order_v['order_fee'], 3);

                        $firstCashier = Cashier::getFirstClassInfos($order_v['username']);
                        if ($firstCashier) {
                            $fee = 0;
                            if ($order_v['order_type'] == 1) {
                                $fee = $firstCashier['alipay_rate'];
                            } elseif ($order_v['order_type'] == 2) {
                                $fee = $firstCashier['wechat_rate'];
                            } elseif ($order_v['order_type'] == 3) {
                                $fee = $firstCashier['union_pay_rate'];
                            } elseif ($order_v['order_type'] == 4) {
                                $fee = $firstCashier['bank_card_rate'];
                            }
                            $datas['firstCashierFee' . $v] = bcadd($datas['firstCashierFee' . $v], bcdiv($order_v['actual_amount'] * $fee, 100, 2), 3);
                        }
                    }
                    if (strpos($order_v['read_remark'], '掉单') !== false) {
                        $datas['lostOrders' . $v] = $datas['lostOrders' . $v] + 1;
                    }
                    if ($order_v['actual_amount'] != $order_v['order_amount'] && $order_v['actual_amount'] > 0) {
                        $datas['editMoneyOrders' . $v] = $datas['editMoneyOrders' . $v] + 1;
                    }
                }
                if ($datas['totalSuccessNumbers' . $v] == 0) {
                    $datas['lostOrderRate' . $v] = '0%';
                } else {
                    $datas['lostOrderRate' . $v] = bcmul($datas['lostOrders' . $v] / $datas['totalSuccessNumbers' . $v], 100, 2) . '%';
                }
                $datas['netProfit' . $v] = bcsub($datas['grossProfit' . $v], $datas['firstCashierFee' . $v], 2);
                $datas['firstCashierFee' . $v] = $datas['firstCashierFee' . $v];
            }

            //总笔数
            $datas['totalNumbersTotal'] = $datas['totalNumbersAlipay'] + $datas['totalNumbersWechat'] + $datas['totalNumbersUnionPay'] + $datas['totalNumbersBankCard'];
            //总成功笔数
            $datas['totalSuccessNumbersTotal'] = $datas['totalSuccessNumbersAlipay'] + $datas['totalSuccessNumbersWechat'] + $datas['totalSuccessNumbersUnionPay'] + $datas['totalSuccessNumbersBankCard'];
            //成功率
            if ($datas['totalNumbersTotal'] == 0) {
                $datas['totalSuccessRateTotal'] = '0%';
            } else {
                $datas['totalSuccessRateTotal'] = bcmul($datas['totalSuccessNumbersTotal'] / $datas['totalNumbersTotal'], 100, 2) . '%';
            }
            //总金额
            $datas['totalAmountTotal'] = bcadd($datas['totalAmountAlipay'], ($datas['totalAmountWechat'] + $datas['totalAmountUnionPay'] + $datas['totalAmountBankCard']), 2);
            //成功金额
            $datas['totalSuccessAmountTotal'] = bcadd($datas['totalSuccessAmountAlipay'], ($datas['totalSuccessAmountWechat'] + $datas['totalSuccessAmountUnionPay'] + $datas['totalSuccessAmountBankCard']), 2);
            //掉单笔数
            $datas['lostOrdersTotal'] = $datas['lostOrdersAlipay'] + $datas['lostOrdersWechat'] + $datas['lostOrdersUnionPay'] + $datas['lostOrdersBankCard'];
            //掉单率
            if ($datas['totalSuccessNumbersTotal'] == 0) {
                $datas['lostOrderRateTotal'] = '0%';
            } else {
                $datas['lostOrderRateTotal'] = bcmul($datas['lostOrdersTotal'] / $datas['totalSuccessNumbersTotal'], 100, 2) . '%';
            }
            //修改金额笔数
            $datas['editMoneyOrdersTotal'] = $datas['editMoneyOrdersAlipay'] + $datas['editMoneyOrdersWechat'] + $datas['editMoneyOrdersBankCard'] + $datas['editMoneyOrdersUnionPay'];
            //毛利润
            $datas['grossProfitTotal'] = bcadd($datas['grossProfitAlipay'], ($datas['grossProfitWechat'] + $datas['grossProfitUnionPay'] + $datas['grossProfitBankCard']), 3);
            //纯利润
            $datas['netProfitTotal'] = bcadd($datas['netProfitAlipay'], ($datas['netProfitWechat'] + $datas['netProfitBankCard'] + $datas['netProfitUnionPay']), 3);
            //一级代理费用
            $datas['firstCashierFeeTotal'] = bcadd($datas['firstCashierFeeAlipay'], ($datas['firstCashierFeeWechat'] + $datas['firstCashierFeeBankCard'] + $datas['firstCashierFeeUnionPay']), 3);

            $report->datas = json_encode($datas, 256);
            if ($report->save()) {
                return ['msg' => '数据统计成功', 'result' => 1];
            } else {
                return ['msg' => json_encode($report->getFirstErrors(), 256), 'result' => 0];
            }
        } catch (\Exception $e) {
            return ['msg' => $e->getMessage(), 'result' => 0];
        }
    }

    /*
     * 导出平台报表
     */
    public function actionExportPlatform()
    {
        $searchModel = new ReportSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Report::$ReportTypePlatform);

        Header("Content-Type:application/vnd.ms-excel;charset=UTF-8");
        Header("Content-Disposition:attachment;filename=平台报表" . date('YmdHi') . ".xls");
        $title = [
            'Username' => ['name' => 'username', 'isChange' => 0],

            'totalNumbersTotal' => ['name' => 'totalNumbersTotal', 'isChange' => 0],
            'totalSuccessNumbersTotal' => ['name' => 'totalSuccessNumbersTotal', 'isChange' => 0],
            'totalSuccessRateTotal' => ['name' => 'totalSuccessRateTotal', 'isChange' => 0],
            'totalAmountTotal' => ['name' => 'totalAmountTotal', 'isChange' => 0],
            'totalSuccessAmountTotal' => ['name' => 'totalSuccessAmountTotal', 'isChange' => 0],
            'lostOrderRateTotal' => ['name' => 'lostOrderRateTotal', 'isChange' => 0],
            'lostOrdersTotal' => ['name' => 'lostOrdersTotal', 'isChange' => 0],
            'editMoneyOrdersTotal' => ['name' => 'editMoneyOrdersTotal', 'isChange' => 0],
            'grossProfitTotal' => ['name' => 'grossProfitTotal', 'isChange' => 0],
            'firstCashierFeeTotal' => ['name' => 'firstCashierFeeTotal', 'isChange' => 0],
            'netProfitTotal' => ['name' => 'netProfitTotal', 'isChange' => 0],

            'totalNumbersWechat' => ['name' => 'totalNumbersWechat', 'isChange' => 0],
            'totalSuccessNumbersWechat' => ['name' => 'totalSuccessNumbersWechat', 'isChange' => 0],
            'totalSuccessRateWechat' => ['name' => 'totalSuccessRateWechat', 'isChange' => 0],
            'totalAmountWechat' => ['name' => 'totalAmountWechat', 'isChange' => 0],
            'totalSuccessAmountWechat' => ['name' => 'totalSuccessAmountWechat', 'isChange' => 0],
            'lostOrderRateWechat' => ['name' => 'lostOrderRateWechat', 'isChange' => 0],
            'lostOrdersWechat' => ['name' => 'lostOrdersWechat', 'isChange' => 0],
            'editMoneyOrdersWechat' => ['name' => 'editMoneyOrdersWechat', 'isChange' => 0],
            'grossProfitWechat' => ['name' => 'grossProfitWechat', 'isChange' => 0],
            'firstCashierFeeWechat' => ['name' => 'firstCashierFeeWechat', 'isChange' => 0],
            'netProfitWechat' => ['name' => 'netProfitWechat', 'isChange' => 0],

            'totalNumbersUnionPay' => ['name' => 'totalNumbersUnionPay', 'isChange' => 0],
            'totalSuccessNumbersUnionPay' => ['name' => 'totalSuccessNumbersUnionPay', 'isChange' => 0],
            'totalSuccessRateUnionPay' => ['name' => 'totalSuccessRateUnionPay', 'isChange' => 0],
            'totalAmountUnionPay' => ['name' => 'totalAmountUnionPay', 'isChange' => 0],
            'totalSuccessAmountUnionPay' => ['name' => 'totalSuccessAmountUnionPay', 'isChange' => 0],
            'lostOrderRateUnionPay' => ['name' => 'lostOrderRateUnionPay', 'isChange' => 0],
            'lostOrdersUnionPay' => ['name' => 'lostOrdersUnionPay', 'isChange' => 0],
            'editMoneyOrdersUnionPay' => ['name' => 'editMoneyOrdersUnionPay', 'isChange' => 0],
            'grossProfitUnionPay' => ['name' => 'grossProfitUnionPay', 'isChange' => 0],
            'firstCashierFeeUnionPay' => ['name' => 'firstCashierFeeUnionPay', 'isChange' => 0],
            'netProfitUnionPay' => ['name' => 'netProfitUnionPay', 'isChange' => 0],

            'totalNumbersBankCard' => ['name' => 'totalNumbersBankCard', 'isChange' => 0],
            'totalSuccessNumbersBankCard' => ['name' => 'totalSuccessNumbersBankCard', 'isChange' => 0],
            'totalSuccessRateBankCard' => ['name' => 'totalSuccessRateBankCard', 'isChange' => 0],
            'totalAmountBankCard' => ['name' => 'totalAmountBankCard', 'isChange' => 0],
            'totalSuccessAmountBankCard' => ['name' => 'totalSuccessAmountBankCard', 'isChange' => 0],
            'lostOrderRateBankCard' => ['name' => 'lostOrderRateBankCard', 'isChange' => 0],
            'lostOrdersBankCard' => ['name' => 'lostOrdersBankCard', 'isChange' => 0],
            'editMoneyOrdersBankCard' => ['name' => 'editMoneyOrdersBankCard', 'isChange' => 0],
            'grossProfitBankCard' => ['name' => 'grossProfitBankCard', 'isChange' => 0],
            'firstCashierFeeBankCard' => ['name' => 'firstCashierFeeBankCard', 'isChange' => 0],
            'netProfitBankCard' => ['name' => 'netProfitBankCard', 'isChange' => 0],

            'totalNumbersAlipay' => ['name' => 'totalNumbersAlipay', 'isChange' => 0],
            'totalSuccessNumbersAlipay' => ['name' => 'totalSuccessNumbersAlipay', 'isChange' => 0],
            'totalSuccessRateAlipay' => ['name' => 'totalSuccessRateAlipay', 'isChange' => 0],
            'totalAmountAlipay' => ['name' => 'totalAmountAlipay', 'isChange' => 0],
            'totalSuccessAmountAlipay' => ['name' => 'totalSuccessAmountAlipay', 'isChange' => 0],
            'lostOrderRateAlipay' => ['name' => 'lostOrderRateAlipay', 'isChange' => 0],
            'lostOrdersAlipay' => ['name' => 'lostOrdersAlipay', 'isChange' => 0],
            'editMoneyOrdersAlipay' => ['name' => 'editMoneyOrdersAlipay', 'isChange' => 0],
            'grossProfitAlipay' => ['name' => 'grossProfitAlipay', 'isChange' => 0],
            'firstCashierFeeAlipay' => ['name' => 'firstCashierFeeAlipay', 'isChange' => 0],
            'netProfitAlipay' => ['name' => 'netProfitAlipay', 'isChange' => 0],

            'Finance_Date' => ['name' => 'finance_date', 'isChange' => 0],
        ];

        $header = '';
        foreach ($title as $k => $v) {
            if ($k == 'Finance_Date') {
                $flag = "\t\n";
            } else {
                $flag = "\t";
            }
            $header .= Yii::t('app/model', $k) . $flag;
        }
        echo mb_convert_encoding($header, 'GBK', 'utf-8');
        foreach ($dataProvider->query->batch(5000) as $values) {
            foreach ($values as $model) {
                foreach ($title as $kk => $vv) {
                    $temp = isset($vv['other']) ? $vv['other'] : $vv['name'];
                    if (in_array($kk, ['Username', 'Finance_Date'])) {
                        if ($kk == 'Finance_Date') {
                            $flag = "\t\n";
                        } else {
                            $flag = "\t";
                        }
                        echo mb_convert_encoding($model->$temp, 'GBK', 'UTF-8') . $flag;
                    } else {
                        $datas = json_decode($model->datas, 1);
                        if (!isset($datas['result'])) {
                            echo mb_convert_encoding($datas[$temp], 'GBK', 'UTF-8') . $flag;
                        } else {
                            echo mb_convert_encoding('', 'GBK', 'UTF-8') . $flag;
                        }
                    }
                }
            }
        }
        exit();
    }

    //商户日收益报表
    public function actionMerchant()
    {
        $searchModel = new ReportSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Report::$ReportTypeMerchant);

        return $this->render('merchant', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    //商户报表--产生数据
    public function actionMerchantData()
    {
        try {

            Yii::$app->response->format = 'json';
            $date = $_POST['date'];
            if (!$date) {
                return ['msg' => '请选取要统计的日期', 'result' => 0];
            }
            $merchants = Merchant::find()->all();

            $str = '';
            foreach ($merchants as $merchant_k => $merchant_v) {
                $report = Report::find()->where(['user_type' => 2, 'finance_date' => $date, 'username' => $merchant_v->mch_name])->one();
                if (!$report) {
                    $report = new Report();
                    $report->username = $merchant_v->mch_name;
                    $report->user_type = 2;
                    $report->finance_date = $date;
                }
                //总数据
                $datas = [];
                //要统计的订单类型
                $orderType = [1 => 'Alipay', 2 => 'Wechat', 3 => 'UnionPay', 4 => 'BankCard'];

                foreach ($orderType as $k => $v) {
                    $orders = Order::find()->where(['>=', 'insert_at', $date])->andWhere(['<=', 'insert_at', date('Y-m-d', strtotime($date) + 86400)])->andWhere(['=', 'order_type', $k])->andWhere(['=', 'mch_name', $merchant_v->mch_name])->asArray()->all();
                    $allStutas = array_count_values(array_column($orders, 'order_status'));
                    $allStutas[2] = isset($allStutas[2]) ? $allStutas[2] : 0;
                    $allStutas[5] = isset($allStutas[5]) ? $allStutas[5] : 0;

                    //总笔数
                    $datas['totalNumbers' . $v] = count($orders);
                    //总成功笔数
                    $datas['totalSuccessNumbers' . $v] = bcadd($allStutas[2], $allStutas[5], 0);
                    //成功率
                    if ($datas['totalNumbers' . $v] == 0) {
                        $datas['totalSuccessRate' . $v] = '0%';
                    } else {
                        $datas['totalSuccessRate' . $v] = bcmul($datas['totalSuccessNumbers' . $v] / $datas['totalNumbers' . $v], 100, 2) . '%';
                    }
                    //总金额
                    $datas['totalAmount' . $v] = array_sum(array_column($orders, 'order_amount'));
                    //成功金额
                    $datas['totalSuccessAmount' . $v] = 0;
                    //掉单笔数
                    $datas['lostOrders' . $v] = 0;
                    //掉单率
                    $datas['lostOrderRate' . $v] = 0;
                    //修改金额笔数
                    $datas['editMoneyOrders' . $v] = 0;
                    //毛利润
                    $datas['grossProfit' . $v] = 0;
                    //纯利润
                    $datas['netProfit' . $v] = 0;
                    //一级代理费用
                    $datas['firstCashierFee' . $v] = 0;

                    foreach ($orders as $order_k => $order_v) {
                        //成功总额
                        if (in_array($order_v['order_status'], [2, 5])) {
                            $datas['totalSuccessAmount' . $v] = bcadd($datas['totalSuccessAmount' . $v], $order_v['actual_amount'], 2);
                            $datas['grossProfit' . $v] = bcadd($datas['grossProfit' . $v], $order_v['order_fee'], 3);

                            $firstCashier = Cashier::getFirstClassInfos($order_v['username']);
                            if ($firstCashier) {
                                $fee = 0;
                                if ($order_v['order_type'] == 1) {
                                    $fee = $firstCashier['alipay_rate'];
                                } elseif ($order_v['order_type'] == 2) {
                                    $fee = $firstCashier['wechat_rate'];
                                } elseif ($order_v['order_type'] == 3) {
                                    $fee = $firstCashier['union_pay_rate'];
                                } elseif ($order_v['order_type'] == 4) {
                                    $fee = $firstCashier['bank_card_rate'];
                                }
                                $datas['firstCashierFee' . $v] = bcadd($datas['firstCashierFee' . $v], bcdiv($order_v['actual_amount'] * $fee, 100, 2), 3);
                            }
                        }
                        if (strpos($order_v['read_remark'], '掉单') !== false) {
                            $datas['lostOrders' . $v] = $datas['lostOrders' . $v] + 1;
                        }
                        if ($order_v['actual_amount'] != $order_v['order_amount'] && $order_v['actual_amount'] > 0) {
                            $datas['editMoneyOrders' . $v] = $datas['editMoneyOrders' . $v] + 1;
                        }
                    }
                    if ($datas['totalSuccessNumbers' . $v] == 0) {
                        $datas['lostOrderRate' . $v] = '0%';
                    } else {
                        $datas['lostOrderRate' . $v] = bcmul($datas['lostOrders' . $v] / $datas['totalSuccessNumbers' . $v], 100, 2) . '%';
                    }
                    $datas['netProfit' . $v] = bcsub($datas['grossProfit' . $v], $datas['firstCashierFee' . $v], 2);
                    $datas['firstCashierFee' . $v] = $datas['firstCashierFee' . $v];
                }

                //总笔数
                $datas['totalNumbersTotal'] = $datas['totalNumbersAlipay'] + $datas['totalNumbersWechat'] + $datas['totalNumbersUnionPay'] + $datas['totalNumbersBankCard'];
                //总成功笔数
                $datas['totalSuccessNumbersTotal'] = $datas['totalSuccessNumbersAlipay'] + $datas['totalSuccessNumbersWechat'] + $datas['totalSuccessNumbersUnionPay'] + $datas['totalSuccessNumbersBankCard'];
                //成功率
                if ($datas['totalNumbersTotal'] == 0) {
                    $datas['totalSuccessRateTotal'] = '0%';
                } else {
                    $datas['totalSuccessRateTotal'] = bcmul($datas['totalSuccessNumbersTotal'] / $datas['totalNumbersTotal'], 100, 2) . '%';
                }
                //总金额
                $datas['totalAmountTotal'] = bcadd($datas['totalAmountAlipay'], ($datas['totalAmountWechat'] + $datas['totalAmountBankCard'] + $datas['totalAmountUnionPay']), 2);
                //成功金额
                $datas['totalSuccessAmountTotal'] = bcadd($datas['totalSuccessAmountAlipay'], ($datas['totalSuccessAmountWechat'] + $datas['totalSuccessAmountBankCard'] + $datas['totalSuccessAmountUnionPay']), 2);
                //掉单笔数
                $datas['lostOrdersTotal'] = $datas['lostOrdersAlipay'] + $datas['lostOrdersWechat'] + $datas['lostOrdersUnionPay'] + $datas['lostOrdersBankCard'];
                //掉单率
                if ($datas['totalSuccessNumbersTotal'] == 0) {
                    $datas['lostOrderRateTotal'] = '0%';
                } else {
                    $datas['lostOrderRateTotal'] = bcmul($datas['lostOrdersTotal'] / $datas['totalSuccessNumbersTotal'], 100, 2) . '%';
                }
                //修改金额笔数
                $datas['editMoneyOrdersTotal'] = $datas['editMoneyOrdersAlipay'] + $datas['editMoneyOrdersWechat'] + $datas['editMoneyOrdersBankCard'] + $datas['editMoneyOrdersUnionPay'];
                //毛利润
                $datas['grossProfitTotal'] = bcadd($datas['grossProfitAlipay'], ($datas['grossProfitWechat'] + $datas['grossProfitUnionPay'] + $datas['grossProfitBankCard']), 3);
                //纯利润
                $datas['netProfitTotal'] = bcadd($datas['netProfitAlipay'], ($datas['netProfitWechat'] + $datas['netProfitBankCard'] + $datas['netProfitUnionPay']), 3);
                //一级代理费用
                $datas['firstCashierFeeTotal'] = bcadd($datas['firstCashierFeeAlipay'], ($datas['firstCashierFeeWechat'] + $datas['firstCashierFeeUnionPay'] + $datas['firstCashierFeeBankCard']), 3);
                $report->datas = json_encode($datas, 256);
                if ($report->save()) {
                    $str = $str . $report->username . '统计成功<br>';
                } else {
                    $str = $str . $report->username . '统计失败【' . $report->getFirstError() . '】<br>';
                }
            }
            return ['msg' => $str, 'result' => 1];
        } catch (\Exception $e) {
            return ['msg' => $e->getMessage(), 'result' => 0];
        }
    }

    /*
     * 导出商户报表
     */
    public function actionExportMerchant()
    {
        $searchModel = new ReportSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Report::$ReportTypeMerchant);

        Header("Content-Type:application/vnd.ms-excel;charset=UTF-8");
        Header("Content-Disposition:attachment;filename=商户报表" . date('YmdHi') . ".xls");
        $title = [
            'Username' => ['name' => 'username', 'isChange' => 0],

            'totalNumbersTotal' => ['name' => 'totalNumbersTotal', 'isChange' => 0],
            'totalSuccessNumbersTotal' => ['name' => 'totalSuccessNumbersTotal', 'isChange' => 0],
            'totalSuccessRateTotal' => ['name' => 'totalSuccessRateTotal', 'isChange' => 0],
            'totalAmountTotal' => ['name' => 'totalAmountTotal', 'isChange' => 0],
            'totalSuccessAmountTotal' => ['name' => 'totalSuccessAmountTotal', 'isChange' => 0],
            'lostOrderRateTotal' => ['name' => 'lostOrderRateTotal', 'isChange' => 0],
            'lostOrdersTotal' => ['name' => 'lostOrdersTotal', 'isChange' => 0],
            'editMoneyOrdersTotal' => ['name' => 'editMoneyOrdersTotal', 'isChange' => 0],
            'grossProfitTotal' => ['name' => 'grossProfitTotal', 'isChange' => 0],
            'firstCashierFeeTotal' => ['name' => 'firstCashierFeeTotal', 'isChange' => 0],
            'netProfitTotal' => ['name' => 'netProfitTotal', 'isChange' => 0],

            'totalNumbersWechat' => ['name' => 'totalNumbersWechat', 'isChange' => 0],
            'totalSuccessNumbersWechat' => ['name' => 'totalSuccessNumbersWechat', 'isChange' => 0],
            'totalSuccessRateWechat' => ['name' => 'totalSuccessRateWechat', 'isChange' => 0],
            'totalAmountWechat' => ['name' => 'totalAmountWechat', 'isChange' => 0],
            'totalSuccessAmountWechat' => ['name' => 'totalSuccessAmountWechat', 'isChange' => 0],
            'lostOrderRateWechat' => ['name' => 'lostOrderRateWechat', 'isChange' => 0],
            'lostOrdersWechat' => ['name' => 'lostOrdersWechat', 'isChange' => 0],
            'editMoneyOrdersWechat' => ['name' => 'editMoneyOrdersWechat', 'isChange' => 0],
            'grossProfitWechat' => ['name' => 'grossProfitWechat', 'isChange' => 0],
            'firstCashierFeeWechat' => ['name' => 'firstCashierFeeWechat', 'isChange' => 0],
            'netProfitWechat' => ['name' => 'netProfitWechat', 'isChange' => 0],

            'totalNumbersUnionPay' => ['name' => 'totalNumbersUnionPay', 'isChange' => 0],
            'totalSuccessNumbersUnionPay' => ['name' => 'totalSuccessNumbersUnionPay', 'isChange' => 0],
            'totalSuccessRateUnionPay' => ['name' => 'totalSuccessRateUnionPay', 'isChange' => 0],
            'totalAmountUnionPay' => ['name' => 'totalAmountUnionPay', 'isChange' => 0],
            'totalSuccessAmountUnionPay' => ['name' => 'totalSuccessAmountUnionPay', 'isChange' => 0],
            'lostOrderRateUnionPay' => ['name' => 'lostOrderRateUnionPay', 'isChange' => 0],
            'lostOrdersUnionPay' => ['name' => 'lostOrdersUnionPay', 'isChange' => 0],
            'editMoneyOrdersUnionPay' => ['name' => 'editMoneyOrdersUnionPay', 'isChange' => 0],
            'grossProfitUnionPay' => ['name' => 'grossProfitUnionPay', 'isChange' => 0],
            'firstCashierFeeUnionPay' => ['name' => 'firstCashierFeeUnionPay', 'isChange' => 0],
            'netProfitUnionPay' => ['name' => 'netProfitUnionPay', 'isChange' => 0],

            'totalNumbersBankCard' => ['name' => 'totalNumbersBankCard', 'isChange' => 0],
            'totalSuccessNumbersBankCard' => ['name' => 'totalSuccessNumbersBankCard', 'isChange' => 0],
            'totalSuccessRateBankCard' => ['name' => 'totalSuccessRateBankCard', 'isChange' => 0],
            'totalAmountBankCard' => ['name' => 'totalAmountBankCard', 'isChange' => 0],
            'totalSuccessAmountBankCard' => ['name' => 'totalSuccessAmountBankCard', 'isChange' => 0],
            'lostOrderRateBankCard' => ['name' => 'lostOrderRateBankCard', 'isChange' => 0],
            'lostOrdersBankCard' => ['name' => 'lostOrdersBankCard', 'isChange' => 0],
            'editMoneyOrdersBankCard' => ['name' => 'editMoneyOrdersBankCard', 'isChange' => 0],
            'grossProfitBankCard' => ['name' => 'grossProfitBankCard', 'isChange' => 0],
            'firstCashierFeeBankCard' => ['name' => 'firstCashierFeeBankCard', 'isChange' => 0],
            'netProfitBankCard' => ['name' => 'netProfitBankCard', 'isChange' => 0],

            'totalNumbersAlipay' => ['name' => 'totalNumbersAlipay', 'isChange' => 0],
            'totalSuccessNumbersAlipay' => ['name' => 'totalSuccessNumbersAlipay', 'isChange' => 0],
            'totalSuccessRateAlipay' => ['name' => 'totalSuccessRateAlipay', 'isChange' => 0],
            'totalAmountAlipay' => ['name' => 'totalAmountAlipay', 'isChange' => 0],
            'totalSuccessAmountAlipay' => ['name' => 'totalSuccessAmountAlipay', 'isChange' => 0],
            'lostOrderRateAlipay' => ['name' => 'lostOrderRateAlipay', 'isChange' => 0],
            'lostOrdersAlipay' => ['name' => 'lostOrdersAlipay', 'isChange' => 0],
            'editMoneyOrdersAlipay' => ['name' => 'editMoneyOrdersAlipay', 'isChange' => 0],
            'grossProfitAlipay' => ['name' => 'grossProfitAlipay', 'isChange' => 0],
            'firstCashierFeeAlipay' => ['name' => 'firstCashierFeeAlipay', 'isChange' => 0],
            'netProfitAlipay' => ['name' => 'netProfitAlipay', 'isChange' => 0],

            'Finance_Date' => ['name' => 'finance_date', 'isChange' => 0],
        ];

        $header = '';
        foreach ($title as $k => $v) {
            if ($k == 'Finance_Date') {
                $flag = "\t\n";
            } else {
                $flag = "\t";
            }
            $header .= Yii::t('app/model', $k) . $flag;
        }
        echo mb_convert_encoding($header, 'GBK', 'utf-8');
        foreach ($dataProvider->query->batch(5000) as $values) {
            foreach ($values as $model) {
                foreach ($title as $kk => $vv) {
                    $temp = isset($vv['other']) ? $vv['other'] : $vv['name'];
                    if (in_array($kk, ['Username', 'Finance_Date'])) {
                        if ($kk == 'Finance_Date') {
                            $flag = "\t\n";
                        } else {
                            $flag = "\t";
                        }
                        echo mb_convert_encoding($model->$temp, 'GBK', 'UTF-8') . $flag;
                    } else {
                        $datas = json_decode($model->datas, 1);
                        if (!isset($datas['result'])) {
                            echo mb_convert_encoding($datas[$temp], 'GBK', 'UTF-8') . $flag;
                        } else {
                            echo mb_convert_encoding('', 'GBK', 'UTF-8') . $flag;
                        }
                    }
                }
            }
        }
        exit();
    }

    //一级代理日收益报表
    public function actionCashier()
    {
        $searchModel = new ReportSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Report::$ReportTypeCashier);

        return $this->render('cashier', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    //一级代理报表--产生数据
    public function actionCashierData()
    {
        try {

            Yii::$app->response->format = 'json';
            $date = $_POST['date'];
            if (!$date) {
                return ['msg' => '请选取要统计的日期', 'result' => 0];
            }
            $cashiers = Cashier::find()->where(['=', 'agent_class', 1])->andWhere(['<', 'cashier_status', 2])->all();

            $str = '';
            foreach ($cashiers as $cashier_k => $cashier_v) {
                $team = Cashier::calcTeam(['username' => $cashier_v->username]);
                $teams = array_column($team, 'username');
                array_push($teams, $cashier_v->username);
                $report = Report::find()->where(['user_type' => 3, 'finance_date' => $date, 'username' => $cashier_v->username])->one();
                if (!$report) {
                    $report = new Report();
                    $report->username = $cashier_v->username;
                    $report->user_type = 3;
                    $report->finance_date = $date;
                }
                //总数据
                $datas = [];
                //要统计的订单类型
                $orderType = [1 => 'Alipay', 2 => 'Wechat', 3 => 'UnionPay', 4 => 'BankCard'];

                foreach ($orderType as $k => $v) {
                    $orders = Order::find()->where(['>=', 'insert_at', $date])->andWhere(['<=', 'insert_at', date('Y-m-d', strtotime($date) + 86400)])->andWhere(['=', 'order_type', $k])->andWhere(['in', 'username', $teams])->asArray()->all();
                    $allStutas = array_count_values(array_column($orders, 'order_status'));
                    $allStutas[2] = isset($allStutas[2]) ? $allStutas[2] : 0;
                    $allStutas[5] = isset($allStutas[5]) ? $allStutas[5] : 0;

                    //总笔数
                    $datas['totalNumbers' . $v] = count($orders);
                    //总成功笔数
                    $datas['totalSuccessNumbers' . $v] = bcadd($allStutas[2], $allStutas[5], 0);
                    //成功率
                    if ($datas['totalNumbers' . $v] == 0) {
                        $datas['totalSuccessRate' . $v] = '0%';
                    } else {
                        $datas['totalSuccessRate' . $v] = bcmul($datas['totalSuccessNumbers' . $v] / $datas['totalNumbers' . $v], 100, 2) . '%';
                    }
                    //总金额
                    $datas['totalAmount' . $v] = array_sum(array_column($orders, 'order_amount'));
                    //成功金额
                    $datas['totalSuccessAmount' . $v] = 0;
                    //掉单笔数
                    $datas['lostOrders' . $v] = 0;
                    //掉单率
                    $datas['lostOrderRate' . $v] = 0;
                    //修改金额笔数
                    $datas['editMoneyOrders' . $v] = 0;
                    //毛利润
                    $datas['grossProfit' . $v] = 0;
                    //纯利润
                    $datas['netProfit' . $v] = 0;
                    //一级代理费用
                    $datas['firstCashierFee' . $v] = 0;

                    foreach ($orders as $order_k => $order_v) {
                        //成功总额
                        if (in_array($order_v['order_status'], [2, 5])) {
                            $datas['totalSuccessAmount' . $v] = bcadd($datas['totalSuccessAmount' . $v], $order_v['actual_amount'], 2);
                            $datas['grossProfit' . $v] = bcadd($datas['grossProfit' . $v], $order_v['order_fee'], 3);

                            $firstCashier = Cashier::getFirstClassInfos($order_v['username']);
                            if ($firstCashier) {
                                $fee = 0;
                                if ($order_v['order_type'] == 1) {
                                    $fee = $firstCashier['alipay_rate'];
                                } else if ($order_v['order_type'] == 2) {
                                    $fee = $firstCashier['wechat_rate'];
                                } else if ($order_v['order_type'] == 3) {
                                    $fee = $firstCashier['union_pay_rate'];
                                } else if ($order_v['order_type'] == 4) {
                                    $fee = $firstCashier['bank_card_rate'];
                                }
                                $datas['firstCashierFee' . $v] = bcadd($datas['firstCashierFee' . $v], bcdiv($order_v['actual_amount'] * $fee, 100, 2), 3);
                            }
                        }
                        if (strpos($order_v['read_remark'], '掉单') !== false) {
                            $datas['lostOrders' . $v] = $datas['lostOrders' . $v] + 1;
                        }
                        if ($order_v['actual_amount'] != $order_v['order_amount'] && $order_v['actual_amount'] > 0) {
                            $datas['editMoneyOrders' . $v] = $datas['editMoneyOrders' . $v] + 1;
                        }
                    }
                    if ($datas['totalSuccessNumbers' . $v] == 0) {
                        $datas['lostOrderRate' . $v] = '0%';
                    } else {
                        $datas['lostOrderRate' . $v] = bcmul($datas['lostOrders' . $v] / $datas['totalSuccessNumbers' . $v], 100, 2) . '%';
                    }
                    $datas['netProfit' . $v] = bcsub($datas['grossProfit' . $v], $datas['firstCashierFee' . $v], 2);
                    $datas['firstCashierFee' . $v] = $datas['firstCashierFee' . $v];
                }

                //总笔数
                $datas['totalNumbersTotal'] = $datas['totalNumbersAlipay'] + $datas['totalNumbersWechat'] + $datas['totalNumbersUnionPay'] + $datas['totalNumbersBankCard'];
                //总成功笔数
                $datas['totalSuccessNumbersTotal'] = $datas['totalSuccessNumbersAlipay'] + $datas['totalSuccessNumbersWechat'] + $datas['totalSuccessNumbersUnionPay'] + $datas['totalSuccessNumbersBankCard'];
                //成功率
                if ($datas['totalNumbersTotal'] == 0) {
                    $datas['totalSuccessRateTotal'] = '0%';
                } else {
                    $datas['totalSuccessRateTotal'] = bcmul($datas['totalSuccessNumbersTotal'] / $datas['totalNumbersTotal'], 100, 2) . '%';
                }
                //总金额
                $datas['totalAmountTotal'] = bcadd($datas['totalAmountAlipay'], ($datas['totalAmountWechat'] + $datas['totalAmountUnionPay'] + $datas['totalAmountBankCard']), 2);
                //成功金额
                $datas['totalSuccessAmountTotal'] = bcadd($datas['totalSuccessAmountAlipay'], ($datas['totalSuccessAmountWechat'] + $datas['totalSuccessAmountUnionPay'] + $datas['totalSuccessAmountBankCard']), 2);
                //掉单笔数
                $datas['lostOrdersTotal'] = $datas['lostOrdersAlipay'] + $datas['lostOrdersWechat'] + $datas['lostOrdersUnionPay'] + $datas['lostOrdersBankCard'];
                //掉单率
                if ($datas['totalSuccessNumbersTotal'] == 0) {
                    $datas['lostOrderRateTotal'] = '0%';
                } else {
                    $datas['lostOrderRateTotal'] = bcmul($datas['lostOrdersTotal'] / $datas['totalSuccessNumbersTotal'], 100, 2) . '%';
                }
                //修改金额笔数
                $datas['editMoneyOrdersTotal'] = $datas['editMoneyOrdersAlipay'] + $datas['editMoneyOrdersWechat'] + $datas['editMoneyOrdersUnionPay'] + $datas['editMoneyOrdersBankCard'];
                //毛利润
                $datas['grossProfitTotal'] = bcadd($datas['grossProfitAlipay'], ($datas['grossProfitWechat'] + $datas['grossProfitUnionPay'] + $datas['grossProfitBankCard']), 3);
                //纯利润
                $datas['netProfitTotal'] = bcadd($datas['netProfitAlipay'], ($datas['netProfitWechat'] + $datas['netProfitUnionPay'] + $datas['netProfitBankCard']), 3);
                //一级代理费用
                $datas['firstCashierFeeTotal'] = bcadd($datas['firstCashierFeeAlipay'], ($datas['firstCashierFeeWechat'] + $datas['firstCashierFeeUnionPay'] + $datas['firstCashierFeeBankCard']), 3);
                $report->datas = json_encode($datas, 256);
                if ($report->save()) {
                    $str = $str . $report->username . '统计成功<br>';
                } else {
                    $str = $str . $report->username . '统计失败【' . $report->getFirstError() . '】<br>';
                }
            }
            return ['msg' => $str, 'result' => 0];
        } catch (\Exception $e) {
            return ['msg' => $e->getMessage(), 'result' => 0];
        }

    }

    /*
     * 导出一级代理报表
     */
    public function actionExportCashier()
    {
        $searchModel = new ReportSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams, Report::$ReportTypeCashier);

        Header("Content-Type:application/vnd.ms-excel;charset=UTF-8");
        Header("Content-Disposition:attachment;filename=收款员报表" . date('YmdHi') . ".xls");
        $title = [
            'Username' => ['name' => 'username', 'isChange' => 0],

            'totalNumbersTotal' => ['name' => 'totalNumbersTotal', 'isChange' => 0],
            'totalSuccessNumbersTotal' => ['name' => 'totalSuccessNumbersTotal', 'isChange' => 0],
            'totalSuccessRateTotal' => ['name' => 'totalSuccessRateTotal', 'isChange' => 0],
            'totalAmountTotal' => ['name' => 'totalAmountTotal', 'isChange' => 0],
            'totalSuccessAmountTotal' => ['name' => 'totalSuccessAmountTotal', 'isChange' => 0],
            'lostOrderRateTotal' => ['name' => 'lostOrderRateTotal', 'isChange' => 0],
            'lostOrdersTotal' => ['name' => 'lostOrdersTotal', 'isChange' => 0],
            'editMoneyOrdersTotal' => ['name' => 'editMoneyOrdersTotal', 'isChange' => 0],
            'grossProfitTotal' => ['name' => 'grossProfitTotal', 'isChange' => 0],
            'firstCashierFeeTotal' => ['name' => 'firstCashierFeeTotal', 'isChange' => 0],
            'netProfitTotal' => ['name' => 'netProfitTotal', 'isChange' => 0],

            'totalNumbersWechat' => ['name' => 'totalNumbersWechat', 'isChange' => 0],
            'totalSuccessNumbersWechat' => ['name' => 'totalSuccessNumbersWechat', 'isChange' => 0],
            'totalSuccessRateWechat' => ['name' => 'totalSuccessRateWechat', 'isChange' => 0],
            'totalAmountWechat' => ['name' => 'totalAmountWechat', 'isChange' => 0],
            'totalSuccessAmountWechat' => ['name' => 'totalSuccessAmountWechat', 'isChange' => 0],
            'lostOrderRateWechat' => ['name' => 'lostOrderRateWechat', 'isChange' => 0],
            'lostOrdersWechat' => ['name' => 'lostOrdersWechat', 'isChange' => 0],
            'editMoneyOrdersWechat' => ['name' => 'editMoneyOrdersWechat', 'isChange' => 0],
            'grossProfitWechat' => ['name' => 'grossProfitWechat', 'isChange' => 0],
            'firstCashierFeeWechat' => ['name' => 'firstCashierFeeWechat', 'isChange' => 0],
            'netProfitWechat' => ['name' => 'netProfitWechat', 'isChange' => 0],

            'totalNumbersAlipay' => ['name' => 'totalNumbersAlipay', 'isChange' => 0],
            'totalSuccessNumbersAlipay' => ['name' => 'totalSuccessNumbersAlipay', 'isChange' => 0],
            'totalSuccessRateAlipay' => ['name' => 'totalSuccessRateAlipay', 'isChange' => 0],
            'totalAmountAlipay' => ['name' => 'totalAmountAlipay', 'isChange' => 0],
            'totalSuccessAmountAlipay' => ['name' => 'totalSuccessAmountAlipay', 'isChange' => 0],
            'lostOrderRateAlipay' => ['name' => 'lostOrderRateAlipay', 'isChange' => 0],
            'lostOrdersAlipay' => ['name' => 'lostOrdersAlipay', 'isChange' => 0],
            'editMoneyOrdersAlipay' => ['name' => 'editMoneyOrdersAlipay', 'isChange' => 0],
            'grossProfitAlipay' => ['name' => 'grossProfitAlipay', 'isChange' => 0],
            'firstCashierFeeAlipay' => ['name' => 'firstCashierFeeAlipay', 'isChange' => 0],
            'netProfitAlipay' => ['name' => 'netProfitAlipay', 'isChange' => 0],

            'totalNumbersUnionPay' => ['name' => 'totalNumbersUnionPay', 'isChange' => 0],
            'totalSuccessNumbersUnionPay' => ['name' => 'totalSuccessNumbersUnionPay', 'isChange' => 0],
            'totalSuccessRateUnionPay' => ['name' => 'totalSuccessRateUnionPay', 'isChange' => 0],
            'totalAmountUnionPay' => ['name' => 'totalAmountUnionPay', 'isChange' => 0],
            'totalSuccessAmountUnionPay' => ['name' => 'totalSuccessAmountUnionPay', 'isChange' => 0],
            'lostOrderRateUnionPay' => ['name' => 'lostOrderRateUnionPay', 'isChange' => 0],
            'lostOrdersUnionPay' => ['name' => 'lostOrdersUnionPay', 'isChange' => 0],
            'editMoneyOrdersUnionPay' => ['name' => 'editMoneyOrdersUnionPay', 'isChange' => 0],
            'grossProfitUnionPay' => ['name' => 'grossProfitUnionPay', 'isChange' => 0],
            'firstCashierFeeUnionPay' => ['name' => 'firstCashierFeeUnionPay', 'isChange' => 0],
            'netProfitUnionPay' => ['name' => 'netProfitUnionPay', 'isChange' => 0],

            'totalNumbersBankCard' => ['name' => 'totalNumbersBankCard', 'isChange' => 0],
            'totalSuccessNumbersBankCard' => ['name' => 'totalSuccessNumbersBankCard', 'isChange' => 0],
            'totalSuccessRateBankCard' => ['name' => 'totalSuccessRateBankCard', 'isChange' => 0],
            'totalAmountBankCard' => ['name' => 'totalAmountBankCard', 'isChange' => 0],
            'totalSuccessAmountBankCard' => ['name' => 'totalSuccessAmountBankCard', 'isChange' => 0],
            'lostOrderRateBankCard' => ['name' => 'lostOrderRateBankCard', 'isChange' => 0],
            'lostOrdersBankCard' => ['name' => 'lostOrdersBankCard', 'isChange' => 0],
            'editMoneyOrdersBankCard' => ['name' => 'editMoneyOrdersBankCard', 'isChange' => 0],
            'grossProfitBankCard' => ['name' => 'grossProfitBankCard', 'isChange' => 0],
            'firstCashierFeeBankCard' => ['name' => 'firstCashierFeeBankCard', 'isChange' => 0],
            'netProfitBankCard' => ['name' => 'netProfitBankCard', 'isChange' => 0],

            'Finance_Date' => ['name' => 'finance_date', 'isChange' => 0],
        ];

        $header = '';
        foreach ($title as $k => $v) {
            if ($k == 'Finance_Date') {
                $flag = "\t\n";
            } else {
                $flag = "\t";
            }
            $header .= Yii::t('app/model', $k) . $flag;
        }
        echo mb_convert_encoding($header, 'GBK', 'utf-8');
        foreach ($dataProvider->query->batch(5000) as $values) {
            foreach ($values as $model) {
                foreach ($title as $kk => $vv) {
                    $temp = isset($vv['other']) ? $vv['other'] : $vv['name'];
                    if (in_array($kk, ['Username', 'Finance_Date'])) {
                        if ($kk == 'Finance_Date') {
                            $flag = "\t\n";
                        } else {
                            $flag = "\t";
                        }
                        echo mb_convert_encoding($model->$temp, 'GBK', 'UTF-8') . $flag;
                    } else {
                        $datas = json_decode($model->datas, 1);
                        if (!isset($datas['result'])) {
                            echo mb_convert_encoding($datas[$temp], 'GBK', 'UTF-8') . $flag;
                        } else {
                            echo mb_convert_encoding('', 'GBK', 'UTF-8') . $flag;
                        }
                    }
                }
            }
        }
        exit();
    }
}
