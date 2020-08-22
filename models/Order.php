<?php

namespace app\models;

use app\common\Common;
use Yii;
use yii\db\Expression;

/**
 * This is the model class for table "order".
 *
 * @property int $id
 * @property string $order_id 订单ID
 * @property string $mch_order_id 商户订单号
 * @property string $username 收款员
 * @property string $qr_code 收款二维码Code
 * @property string $mch_name 商户用户名
 * @property int $order_type 订单收款码类型 1支付宝 2微信
 * @property string $order_fee 订单手续费
 * @property string $order_amount 订单金额
 * @property string $benefit 订单优惠金额
 * @property string $actual_amount 实际订单金额
 * @property string $callback_url 同步回调地址
 * @property string $notify_url 异步回调地址
 * @property int $order_status 订单状态 1未支付 2已支付 3超时 4手动失败 5手动成功
 * @property int $notify_status 通知状态 1未通知 2已通过 3通知失败
 * @property string $expire_time 过期时间
 * @property string $read_remark 被读取标识
 * @property int $is_settlement 是否已结算
 * @property string $insert_at 创建时间
 * @property string $update_at 修改时间
 * @property string $operator 订单操作者
 */
class Order extends \yii\db\ActiveRecord
{
    public $insert_at_start;
    public $insert_at_end;
    public $update_at_start;
    public $update_at_end;
    public $qr_location;
    public $query_team;
    public $refund_status;
    public $refund_type;
    public $remark;


    //定义各订单类型映射
    public static $OrderTypes = array(
        1 => array(
            'type_name' => '支付宝扫码',
            'parent_type' => 0,
        ),
        2 => array(
            'type_name' => '微信扫码',
            'parent_type' => 0,
        ),
        3 => array(
            'type_name' => '云闪付扫码',
            'parent_type' => 0,
        ),
        4 => array(
            'type_name' => '转银行卡',
            'parent_type' => 0,
        ),
        /*100 => array(
            'type_name' => '支付宝红包',
            'parent_type' => 1,
        ),*/
        101 => array(
            'type_name' => '网银转卡',
            'parent_type' => 4,
        ),
        102 => array(
            'type_name' => '支付宝转卡',
            'parent_type' => 4,
        ),
        103 => array(
            'type_name' => '微信转卡',
            'parent_type' => 4,
        ),
        104 => array(
            'type_name' => '手机号转卡',
            'parent_type' => 4,
        ),
        110 => array(
            'type_name' => '支付宝网关',
            'parent_type' => 1,
        ),
    );


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'order';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['order_type', 'order_status', 'notify_status', 'is_settlement', 'refund_status', 'refund_type'], 'integer'],
            [['order_fee', 'order_amount', 'benefit', 'actual_amount'], 'number'],
            [['expire_time', 'insert_at', 'update_at', 'is_settlement', 'qr_location', 'query_team'], 'safe'],
            [['order_id', 'mch_order_id'], 'string', 'max' => 100],
            [['username', 'qr_code', 'operator'], 'string', 'max' => 50],
            [['user_ip'], 'string', 'max' => 30],
            [['mch_name', 'callback_url', 'notify_url', 'read_remark', 'remark'], 'string', 'max' => 255],
            [['order_id'], 'required'],
            [['order_id'], 'unique'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/model', 'ID'),
            'order_id' => Yii::t('app/model', 'Order ID'),
            'mch_order_id' => Yii::t('app/model', 'Mch Order ID'),
            'username' => Yii::t('app/model', 'Username'),
            'qr_code' => Yii::t('app/model', 'Qr Code'),
            'mch_name' => Yii::t('app/model', 'Mch Name'),
            'order_type' => Yii::t('app/model', 'Order Type'),
            'order_fee' => Yii::t('app/model', 'Order Fee'),
            'order_amount' => Yii::t('app/model', 'Order Amount'),
            'benefit' => Yii::t('app/model', 'Benefit'),
            'actual_amount' => Yii::t('app/model', 'Actual Amount'),
            'callback_url' => Yii::t('app/model', 'Callback Url'),
            'notify_url' => Yii::t('app/model', 'Notify Url'),
            'order_status' => Yii::t('app/model', 'Order Status'),
            'notify_status' => Yii::t('app/model', 'Notify Status'),
            'expire_time' => Yii::t('app/model', 'Expire Time'),
            'read_remark' => Yii::t('app/model', 'Read Remark'),
            'insert_at' => Yii::t('app/model', 'Insert At'),
            'update_at' => Yii::t('app/model', 'Update At'),
            'operator' => Yii::t('app/model', 'Operator'),
            'is_settlement' => Yii::t('app/model', 'Is Settlement'),
            'insert_at_start' => Yii::t('app/model', 'Insert At Start'),
            'insert_at_end' => Yii::t('app/model', 'Insert At End'),
            'update_at_start' => Yii::t('app/model', 'Update At Start'),
            'update_at_end' => Yii::t('app/model', 'Update At End'),
            'query_team' => Yii::t('app/model', 'query_team'),
            'refund_status' => Yii::t('app/model', 'Refund Status'),
            'refund_type' => Yii::t('app/model', 'Refund Type'),
            'remark' => Yii::t('app/model', 'remark'),
        ];
    }

    //订单回调
    public static function orderNotify($id)
    {
        try {
            $order = Order::findOne($id);
            if (!in_array($order->order_status, [2, 4, 5])) {
                return '订单未完成，无法回调';
            }

            $merchant = Merchant::find()->where(['mch_name' => $order->mch_name])->one();

            Yii::info(json_encode([$order->toArray(), $merchant->toArray()], 256), 'Order_orderNotify_model');

            $merchant = Merchant::find()->where(['mch_name' => $order->mch_name])->select(['mch_code'])->one();

            $data['mch_order_id'] = $order->mch_order_id;
            $data['mch_code'] = $merchant->mch_code;
            $data['order_type'] = $order->order_type;

            $tempMoney = bcadd($order->actual_amount, $order->benefit, 2);
            if ($order->benefit && ($tempMoney != $order->order_amount)) {
                $data['order_amount'] = $order->actual_amount;
            } elseif ($order->benefit && ($tempMoney == $order->order_amount)) {
                $data['order_amount'] = $order->order_amount;
            } elseif ($order->order_amount != $order->actual_amount) {
                $data['order_amount'] = $order->actual_amount;
            } else {
                $data['order_amount'] = $order->order_amount;
            }

            $data['callback_url'] = $order->callback_url;
            $data['notify_url'] = $order->notify_url;
            $data['order_status'] = $order->order_status;
            $data['id'] = $order->order_id;
            $data['return_date'] = date('Y-m-d H:i:s');

            //密钥
            $sign = Order::validateSign($merchant->mch_code, $data);

            $data['sign'] = $sign['sign'];

            Yii::info(json_encode([$data, $merchant->toArray()], 256), 'Order/orderNotify_data');

            $res = Common::curl($order->notify_url, $data);
            Yii::info($res, 'Order_orderNotify_' . $data['mch_order_id']);
            if ($res == 'success') {
                $order->notify_status = 2;
                $order->update_at = date('Y-m-d H:i:s');
                $order->save();
                return true;
            } else {
                return '发起回调，未收到success信息';
            }
        } catch (\Exception $e) {
            Yii::info($e->getMessage() . '-' . $e->getLine() . '-' . $e->getFile(), 'Order_orderNotify_Bad_' . $data['mch_order_id']);
        }
    }

    /**
     * @param $username
     * @param $money
     * @param $rateType
     * 计算佣金
     */
    public static function incomeCalc($username, $orderId, $rateType)
    {
        $incomeCalc_transaction = Yii::$app->db->beginTransaction();
        $first = Cashier::find()->select(['wechat_rate', 'alipay_rate', 'union_pay_rate', 'bank_card_rate', 'parent_name', 'username'])->where(['username' => $username])->one();
        $order = Order::findOne(['order_id' => $orderId]);

        Yii::info(['cashier' => $first->toArray(), 'order' => $order->toArray()], 'Order_incomeCalc1_' . $orderId);

        if (!$order) {
            return null;
        } elseif ($order->is_settlement == 1) {
            return null;
        }

        if ($order->benefit != 0 && (bcadd($order->actual_amount, $order->benefit, 2) != $order->order_amount)) {
            $money = $order->actual_amount;
        } elseif ($order->benefit != 0 && (bcadd($order->actual_amount, $order->benefit, 2) == $order->order_amount)) {
            $money = $order->order_amount;
        } elseif ($order->order_amount != $order->actual_amount && $order->actual_amount > 0) {
            $money = $order->actual_amount;
        } else {
            $money = $order->order_amount;
        }

        //如果是补单的，结算时需要扣除收款员额度
        if (strpos($order->read_remark, '补单') !== false) {
            if ($order->order_type == 1) {
                $qr_type_amount = 'alipay_amount';
                $financeType = ['type' => 12, 'name' => '补单，支付宝额度减少' . $order->order_id];
            } elseif ($order->order_type == 2) {
                $qr_type_amount = 'wechat_amount';
                $financeType = ['type' => 8, 'name' => '补单，微信额度减少' . $order->order_id];
            } elseif ($order->order_type == 3) {
                $qr_type_amount = 'union_pay_amount';
                $financeType = ['type' => 14, 'name' => '补单，银行卡额度减少' . $order->order_id];
            } elseif ($order->order_type == 4) {
                $qr_type_amount = 'bank_card_amount';
                $financeType = ['type' => 17, 'name' => '补单，银行卡额度减少' . $order->order_id];
            }
            $detailCashierRes = FinanceDetail::financeCalc($order->username, $financeType['type'], ($money * -1), 3, $financeType['name']);
            $cashierResult = Cashier::updateAllCounters([
                $qr_type_amount => ($money * -1),
            ], ['username' => $order->username]);
            if (!$detailCashierRes || !$cashierResult) {
                $incomeCalc_transaction->rollBack();
                return null;
            }
        }

        $type = [
            '1' => 'alipay_rate',
            '2' => 'wechat_rate',
            '3' => 'union_pay_rate',
            '4' => 'bank_card_rate',
        ];

        //商户结算
        $mch_money = bcsub($money, $order->order_fee, 2);

        $res_mch_finance = FinanceDetail::financeCalc($order->mch_name, 2, $mch_money, 2, $order->order_id . '成功，商户余额增加');
        $res_mch_balance = Merchant::updateAllCounters(['balance' => $mch_money], ['mch_name' => $order->mch_name]);
        Yii::info($res_mch_balance . '-' . $res_mch_finance, 'Order_incomeCalc_mch_' . $orderId);
        if (!$res_mch_finance || !$res_mch_balance) {
            $incomeCalc_transaction->rollBack();
            return null;
        }

        $next = null;
        try {
            Yii::info($first->toArray(), 'Order_incomeCalc2_' . $orderId);
            while ($first) {
                $order_type = $type[$rateType];
                if ($next) {
                    $finalRate = bcsub($first->$order_type, $next->$order_type, 2);
                } else {
                    $finalRate = $first->$order_type;
                }
                $temp = bcmul($finalRate, $money, 2);
                Yii::info(json_encode([$finalRate, $money, $temp]), 'IncomeDetail_' . $orderId);
                //计算出来的佣金大于0才进行数据库操作和资金交易明细
                $income = bcdiv($temp, 100, 2);
                if ($income > 0) {
                    //资金交易明细
                    $resF = FinanceDetail::financeCalc($first->username, 2, $income, 3, $order->order_id . '佣金');
                    $resI = Cashier::updateAllCounters(['income' => $income], ['username' => $first->username]);
                    if (!$resF || !$resI) {
                        Yii::info($resF . '-' . $resI, 'Order_incomeCalc3_' . $orderId);
                        $incomeCalc_transaction->rollBack();
                        return null;
                    }
                }

                if ($first->parent_name) {
                    $next = $first;
                    $first = Cashier::find()->select(['wechat_rate', 'alipay_rate', 'union_pay_rate', 'bank_card_rate', 'parent_name', 'username'])->where(['username' => $first->parent_name])->one();
                } else {
                    $first = null;
                }
            }
            $order->is_settlement = 1;
            $order->order_status = 5;
            $order->update_at = date('Y-m-d H:i:s');
            if ($order->actual_amount <= 0) {
                $order->actual_amount = $order->order_amount;
            }
            if ($order->save()) {
                $incomeCalc_transaction->commit();
                return true;
            } else {
                $incomeCalc_transaction->rollBack();
                return null;
            }
        } catch (\Exception $e) {
            $incomeCalc_transaction->rollBack();
            Yii::error(['data' => $first->toArray(), 'msg' => $e->getMessage() . '_' . $e->getLine() . '_' . $e->getFile()], 'Oder_incomeCalc_error_' . $orderId);
            return null;
        }
    }

    /**
     * @param $mch_code
     * @param $data
     * @return string|null
     * 验签
     *
     */
    public static function validateSign($mch_code, $data)
    {
        //通过mch_code查询商户的mch_key
        $merchant = Merchant::find()->where(['mch_code' => $mch_code])->andWhere(['mch_status' => 1])->one();
        if (!$merchant) {
            Yii::error($data, 'validateSign_noMerchant');
            return null;
        }
        unset($data['sign']);
        $signStr = '';
        ksort($data);
        foreach ($data as $k => $v) {
            //参数有空值的，直接验签失败
            if ($v == null) {
                Yii::error($data, 'validateSign_paramsIsNull');
                return null;
            } else {
                $signStr .= $k . '=' . $v . '&';
            }
        }
        //传递过来的所有参数加上mch_key和当天的日期参与加密
        $signStr .= $merchant->mch_key . '&' . date('Ymd');
        Yii::info(['data' => $data, 'signStr' => $signStr], 'validateSign_ok');
        return ['sign' => md5($signStr), 'merchant' => $merchant];
    }

    public static function orderOk($id, $isChangeMoney = 0)
    {
        //开启事务
        $transaction = Yii::$app->db->beginTransaction();
        //获得订单信息
        $order = Order::findOne($id);

        Yii::info($order->toArray(), 'Order_orderOk_' . $order->mch_order_id);

        if ($order == null) {
            $transaction->rollBack();
            return ['msg' => '订单不存在', 'result' => 0];
        }
        if (in_array($order->order_status, [2, 4, 5])) {
            $transaction->rollBack();
            return ['msg' => '订单已完成', 'result' => 0];
        }
        //记录之前的订单状态
        $beforeStatus = $order->order_status;
        //保持最新的状态-手动成功
        $order->order_status = 5;

        //如果当前订单没有实到金额，那么订单金额减去优惠就是实到金额
        if ($order->actual_amount == 0) {
            $order->actual_amount = bcsub($order->order_amount, $order->benefit, 2);
        }

        //根据实到金额重新算下订单的手续费
        $merchant = Merchant::findOne(['mch_name' => $order->mch_name]);
        if ($order->order_type == 1) {
            $rate = $merchant->alipay_rate;
        } else if ($order->order_type == 2) {
            $rate = $merchant->wechat_rate;
        } else if ($order->order_type == 3) {
            $rate = $merchant->union_pay_rate;
        } else if ($order->order_type == 4) {
            $rate = $merchant->bank_card_rate;
        }

        $tempMoney = bcadd($order->actual_amount, $order->benefit, 2);
        if ($order->benefit && ($tempMoney != $order->order_amount)) {
            $nowMoney = $order->actual_amount;
        } elseif ($order->benefit && ($tempMoney == $order->order_amount)) {
            $nowMoney = $order->order_amount;
        } elseif ($order->order_amount != $order->actual_amount) {
            $nowMoney = $order->actual_amount;
        } else {
            $nowMoney = $order->order_amount;
        }
        $order->order_fee = bcdiv(bcmul($nowMoney, $rate), 100, 2);

        //记录具体是哪个后台人员操作的订单
        $order->operator = Yii::$app->user->identity->username;
        $order->update_at = date('Y-m-d H:i:s');
        $orderRes = $order->save();

        Yii::info($orderRes, 'Order_orderOk0_' . $order->mch_order_id);

        //默认操作都是通过的（后面事务判断需要用到）
        $detailMerchantRes = $merchantResult = $detailCashierRes = $cashierResult = $detailMerchantRes_c = $merchantResult_c = $detailCashierRes_c = $cashierResult_c = 2;
        //订单修改成功，才进行后续操作
        if ($orderRes) {
            //记录码的上次收款时间
            $qrcode = QrCode::find()->where(['qr_code' => $order->qr_code])->one();
            $qrcode->last_money_time = $order->insert_at;
            $qrcode->save();

            //如果是超时订单，返回商户提单金额（加），收款员的可收额度（减）
            if ($beforeStatus == 3) {
                //返回用户的可收额度和明细记录
                if ($order->order_type == 1) {
                    $qr_type_amount = 'alipay_amount';
                    $financeType = ['type' => 12, 'name' => '接单，支付宝额度减少' . $order->order_id];
                } elseif ($order->order_type == 2) {
                    $qr_type_amount = 'wechat_amount';
                    $financeType = ['type' => 8, 'name' => '接单，微信额度减少' . $order->order_id];
                } elseif ($order->order_type == 3) {
                    $qr_type_amount = 'union_pay_amount';
                    $financeType = ['type' => 14, 'name' => '接单，微信额度减少' . $order->order_id];
                } elseif ($order->order_type == 4) {
                    $qr_type_amount = 'bank_card_amount';
                    $financeType = ['type' => 17, 'name' => '接单，微信额度减少' . $order->order_id];
                }
                $detailCashierRes = FinanceDetail::financeCalc($order->username, $financeType['type'], ($order->order_amount * -1), 3, $financeType['name']);
                $cashierResult = Cashier::updateAllCounters([
                    $qr_type_amount => ($order->order_amount * -1),
                ], ['username' => $order->username]);
            }

            //如果是修改金额的订单，则需要重新计算商户的额度、收款员的额度
            if ($isChangeMoney) {
                //修改订单金额，最终产生的差额
                $finalMoney = bcsub($order->actual_amount, $order->order_amount, 2);

                if ($beforeStatus == 3) {
                    //如果订单之前的状态是超时，那么就要重新把这笔订单金额记录到商户已提单金额里面
                    $moneyX = $order->actual_amount;
                } else {
                    //如果不是超时订单，那么只需要修改差额即可
                    $moneyX = abs($finalMoney);
                }

                /*
                 * 重新计算（根据差额大于0还是小于0来判断实际操作的金额是负数还是正数）
                 */
                //修改收款员可收额度和明细添加
                if ($order->order_type == 1) {
                    $qr_type_amount = 'alipay_amount';
                    $financeType = ['type' => 12, 'name' => '接单，支付宝额度-稽查'];
                } elseif ($order->order_type == 2) {
                    $qr_type_amount = 'wechat_amount';
                    $financeType = ['type' => 8, 'name' => '接单，微信额度-稽查'];
                } elseif ($order->order_type == 3) {
                    $qr_type_amount = 'union_pay_amount';
                    $financeType = ['type' => 14, 'name' => '接单，微信额度-稽查'];
                } elseif ($order->order_type == 4) {
                    $qr_type_amount = 'bank_card_amount';
                    $financeType = ['type' => 17, 'name' => '接单，微信额度-稽查'];
                }
                $detailCashierRes_c = FinanceDetail::financeCalc($order->username, $financeType['type'], $finalMoney < 0 ? abs($finalMoney) : ($finalMoney * -1), 3, $financeType['name']);
                $cashierResult_c = Cashier::updateAllCounters([
                    $qr_type_amount => ($finalMoney * -1),
                ], ['username' => $order->username]);
            }

            if ($orderRes && $detailMerchantRes && $merchantResult && $detailCashierRes && $cashierResult && $detailMerchantRes_c && $merchantResult_c && $detailCashierRes_c && $cashierResult_c) {
                Yii::info(['订单' => $orderRes, '商户明细' => $detailMerchantRes, '商户' => $merchantResult, '收款员明细' => $detailCashierRes, '收款员' => $cashierResult], 'Order_orderOk2_' . $order->mch_order_id);
                $transaction->commit();
                //回调通知
                Order::orderNotify($id);
                //结算佣金
                Order::incomeCalc($order->username, $order->order_id, $order->order_type);
                //处理Redis统计

                //二维码接单失败笔数
                $qrKey = $order->qr_code . date('YmdHi') . 'success';
                //用户接单失败笔数
                $userKey = $order->username . date('YmdHi') . 'success';
                Yii::$app->redis->setex($qrKey, '86400', 1);
                Yii::$app->redis->setex($userKey, '86400', 1);

                //保存成功则redis记录此二维码已收此金额，指定时间内不再接受此金额
                Common::isQrCodeHasThisMoney($order->qr_code, $order->order_amount, 1, 0);
                //记录二维码今日接单金额
                Common::qrTodayMoney($order->qr_code, 1, $order->order_amount, 1);
                //记录二维码今日接单数量
                Common::qrTodayTimes($order->qr_code, 1, 1, 1);
                //记录收款员今日接单金额
                Common::cashierTodayMoney($order->username, $order->order_type, 1, $order->actual_amount, 1);
                //记录收款员今日接单笔数
                Common::cashierTodayTimes($order->username, $order->order_type, 1, 1, 1);
            } else {
                Yii::info(['订单' => $orderRes, '商户明细' => $detailMerchantRes . '-' . $detailMerchantRes_c, '商户' => $merchantResult . '-' . $merchantResult_c, '收款员明细' => $detailCashierRes . '-' . $detailCashierRes_c, '收款员' => $cashierResult . '-' . $cashierResult_c], 'Order/orderOk3/' . $order->mch_order_id);
                $transaction->rollBack();
                return ['msg' => '操作失败，请联系相关人员', 'result' => 0];
            }
            return ['msg' => '订单已置为成功状态', 'result' => 1];
        } else {
            $transaction->rollBack();
            return ['msg' => '修改订单状态失败', 'result' => 0];
        }
    }


    public static function getQRAllowOrderTypes($qrType, $onlyValue = true, $getAll = false)
    {

        $allowOrderTypes = array();

        foreach (self::$OrderTypes as $k => $v) {

            if ($getAll) {
                $allowOrderTypes[$k] = $v['type_name'];
            } else {
                if ($qrType == $k || (isset($v['parent_type']) && is_numeric($v['parent_type']) && $v['parent_type'] == $qrType)) {
                    if ($onlyValue) {
                        $allowOrderTypes[] = $k;
                    } else {
                        $allowOrderTypes[$k] = $v['type_name'];
                    }
                }
            }
        }

        return $allowOrderTypes;

    }
}
