<?php

namespace app\models;

use app\common\Common;
use Faker\Provider\PhoneNumber;
use Yii;
use yii\validators\NumberValidator;

/**
 * This is the model class for table "cashier".
 *
 * @property int $id
 * @property string $username 用户名
 * @property string $login_password 登录密码
 * @property string $pay_password 资金密码
 * @property string $income 收益
 * @property string $security_money 保证金
 * @property string $wechat_rate 微信费率
 * @property string $alipay_rate 支付宝费率
 * @property string $wechat_amount 微信可收额度
 * @property string $alipay_amount 支付宝可收额度
 * @property string $parent_name 上级用户名
 * @property string $wechat 个人微信
 * @property string $alipay 个人支付宝
 * @property string $telephone 手机号码
 * @property int $agent_class 等级
 * @property int $cashier_status 收款员状态 0禁用 1启用
 * @property string $insert_at 添加时间
 * @property string $update_at 修改时间
 * @property string $login_at 上次登录时间
 * @property string $remark 备注
 */
class Cashier extends \yii\db\ActiveRecord
{
    public $insert_at_start;
    public $insert_at_end;
    public $query_team;

    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'cashier';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['agent_class', 'username', 'login_password', 'alipay_rate', 'wechat_rate', 'cashier_status', 'wechat_amount', 'alipay_amount', 'security_money'], 'required'],
            [['income', 'security_money', 'wechat_rate', 'alipay_rate', 'union_pay_rate', 'bank_card_rate', 'wechat_amount', 'alipay_amount', 'union_pay_amount', 'bank_card_amount'], 'number'],
            [['agent_class', 'cashier_status', 'priority'], 'integer'],
            [['insert_at', 'update_at', 'login_at', 'query_team'], 'safe'],
            [['username', 'parent_name', 'wechat', 'alipay'], 'string', 'max' => 50],
            [['login_password', 'pay_password', 'remark'], 'string', 'max' => 255],
            [['telephone'], 'string', 'max' => 20],
            [['username'], 'unique'],
            ['agent_class', 'integer', 'integerOnly' => true, 'min' => 1],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function attributeLabels()
    {
        return [
            'id' => Yii::t('app/model', 'ID'),
            'username' => Yii::t('app/model', 'Username'),
            'login_password' => Yii::t('app/model', 'Login Password'),
            'pay_password' => Yii::t('app/model', 'Pay Password'),
            'income' => Yii::t('app/model', 'Income'),
            'security_money' => Yii::t('app/model', 'Security Money'),
            'wechat_rate' => Yii::t('app/model', 'Wechat Rate'),
            'alipay_rate' => Yii::t('app/model', 'Alipay Rate'),
            'wechat_amount' => Yii::t('app/model', 'Wechat Amount'),
            'alipay_amount' => Yii::t('app/model', 'Alipay Amount'),
            'parent_name' => Yii::t('app/model', 'Parent Name'),
            'wechat' => Yii::t('app/model', 'Wechat'),
            'alipay' => Yii::t('app/model', 'Alipay'),
            'telephone' => Yii::t('app/model', 'Telephone'),
            'agent_class' => Yii::t('app/model', 'Agent Class'),
            'cashier_status' => Yii::t('app/model', 'Cashier Status'),
            'insert_at' => Yii::t('app/model', 'Insert At'),
            'update_at' => Yii::t('app/model', 'Update At'),
            'login_at' => Yii::t('app/model', 'Login At'),
            'remark' => Yii::t('app/model', 'Remark'),
            'insert_at_start' => Yii::t('app/model', 'Insert At Start'),
            'insert_at_end' => Yii::t('app/model', 'Insert At End'),
            'priority' => Yii::t('app/model', 'priority'),
            'query_team' => Yii::t('app/model', 'query_team'),
            'invite_code' => Yii::t('app/model', 'invite_code'),

            'union_pay_rate' => Yii::t('app/model', 'union_pay_rate'),
            'union_pay_amount' => Yii::t('app/model', 'union_pay_amount'),
            'bank_card_rate' => Yii::t('app/model', 'bank_card_rate'),
            'bank_card_amount' => Yii::t('app/model', 'bank_card_amount'),

        ];
    }

    /**
     * @param $username
     * 退出APP
     */
    public static function byebye($username)
    {
        $token = Yii::$app->redis->get($username);
        if ($token && Yii::$app->redis->get('User_' . $token)) {
            Yii::$app->redis->del('User_' . $token);
        }
    }

    /**
     * 生成唯一的邀请码
     * @return string
     */
    public static function generateCashierInviteCode()
    {
        $code = 'ABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $rand = $code[rand(0, 25)]
            . strtoupper(dechex(date('m')))
            . date('d') . substr(time(), -5)
            . substr(microtime(), 2, 5)
            . sprintf('%02d', rand(0, 99));
        for (
            $a = md5($rand, true),
            $s = '123456789ABCDEFGHIJKLMNOPQRSTUVabcdefghijkmnpqrstuvwxy',
            $d = '',
            $f = 0;
            $f < 4;
            $g = ord($a[$f]),
            $d .= $s[($g ^ ord($a[$f + 4])) - $g & 0x1F],
            $f++
        ) ;

        $data = Cashier::find()->where('invite_code=:code', array(':code' => $d))->asArray()->one();
        if ($data && isset($data['invite_code']) && $data['invite_code']) {
            return self::generateCashierInviteCode();
        }

        return $d;

    }


    //获取所有收款员--主要获取可用的收款员用在添加/修改收款员功能中
    public static function getAllCashier($type = 0)
    {
        if ($type == 0) {
            $cashier = Cashier::find()->where('cashier_status=1')->select(['username', 'agent_class', 'wechat_rate', 'alipay_rate', 'union_pay_rate', 'bank_card_rate'])->indexBy('username')->orderBy('agent_class DESC')->asArray()->all();
            if ($cashier) {
                $temp = [];
                foreach ($cashier as $item) {
                    $temp[$item['username']] = $item['username'] . '-代理' . $item['agent_class'] . '【微信费率' . $item['wechat_rate'] . '】-【支付宝费率' . $item['alipay_rate'] . '】' .'-【云闪付费率' . $item['union_pay_rate'] . '】'.'-【银行卡费率' . $item['bank_card_rate'] . '】';
                }
                $cashier = $temp;
            }
        } else {
            $cashier = Cashier::find()->where('cashier_status=1')->indexBy('username')->asArray()->all();
        }
        return $cashier;
    }

    /**
     * 判断两者费率、代理是否正确
     */
    public static function feeOrClassIsOk($child)
    {
        $msg = null;
        $cashier = Cashier::find()->where(['username' => $child->parent_name])->asArray()->one();
        if ($cashier) {
            $wechatMaxRate = SystemConfig::getSystemConfig('wechatMaxRate');
            $alipayMaxRate = SystemConfig::getSystemConfig('alipayMaxRate');
            $unionPayMaxRate = SystemConfig::getSystemConfig('unionPayMaxRate');
            $bankCardMaxRate = SystemConfig::getSystemConfig('bankCardMaxRate');
            if ($child->agent_class <= $cashier['agent_class']) {
                $msg = '代理等级数字越小，等级越高<br>下级代理等级必须大于上级代理等级，请重新设置<br>当前所选上级的代理等级为：' . $cashier['agent_class'];
            } elseif ($child->wechat_rate > $cashier['wechat_rate']) {
                $msg = '下级微信费率不能大于上级，当前所选上级的微信费率为：' . $cashier['wechat_rate'];
            } elseif ($child->alipay_rate > $cashier['alipay_rate']) {
                $msg = '下级支付宝费率不能大于上级，当前所选上级的支付宝费率为：' . $cashier['alipay_rate'];
            } elseif ($child->union_pay_rate > $cashier['union_pay_rate']) {
                $msg = '下级云闪付费率不能大于上级，当前所选上级的云闪付费率为：' . $cashier['union_pay_rate'];
            } elseif ($child->bank_card_rate > $cashier['bank_card_rate']) {
                $msg = '下级银行卡费率不能大于上级，当前所选上级的银行卡费率为：' . $cashier['bank_card_rate'];
            } elseif (!isset($wechatMaxRate) || $wechatMaxRate == null) {
                $msg = '系统配置未设置微信最高费率，请前往设置！';
            } elseif (!isset($alipayMaxRate) || $alipayMaxRate == null) {
                $msg = '系统配置未设置支付宝最高费率，请前往设置！';
            } elseif (!isset($unionPayMaxRate) || $unionPayMaxRate == null) {
                $msg = '系统配置未设置云闪付最高费率，请前往设置！';
            } elseif (!isset($bankCardMaxRate) || $bankCardMaxRate == null) {
                $msg = '系统配置未设置银行卡最高费率，请前往设置！';
            } elseif ($child->alipay_rate > $alipayMaxRate) {
                $msg = '用户支付宝费率大于系统设置的最高费率[' . $alipayMaxRate . ']，请重新设置！';
            } elseif ($child->wechat_rate > $wechatMaxRate) {
                $msg = '用户微信费率大于系统设置的最高费率[' . $wechatMaxRate . ']，请重新设置！';
            } elseif ($child->union_pay_rate > $unionPayMaxRate) {
                $msg = '用户云闪付费率大于系统设置的最高费率[' . $unionPayMaxRate . ']，请重新设置！';
            } elseif ($child->bank_card_rate > $bankCardMaxRate) {
                $msg = '用户银行卡费率大于系统设置的最高费率[' . $bankCardMaxRate . ']，请重新设置！';
            }

            $next = Cashier::find()->where(['parent_name' => $child->username])->select(['wechat_rate'])->orderBy(['wechat_rate' => SORT_DESC])->one();
            if ($next) {
                if ($next->wechat_rate > $child->wechat_rate) {
                    $msg = $child->username . '的微信费率不能低于他下级的微信费率:' . $next->wechat_rate;
                }
            }

            $next = Cashier::find()->where(['parent_name' => $child->username])->select(['alipay_rate'])->orderBy(['alipay_rate' => SORT_DESC])->one();
            if ($next) {
                if ($next->alipay_rate > $child->alipay_rate) {
                    $msg = $child->username . '的支付宝费率不能低于他下级的支付宝费率:' . $next->alipay_rate;
                }
            }

            $next = Cashier::find()->where(['parent_name' => $child->username])->select(['union_pay_rate'])->orderBy(['union_pay_rate' => SORT_DESC])->one();
            if ($next) {
                if ($next->union_pay_rate > $child->union_pay_rate) {
                    $msg = $child->username . '的云闪付费率不能低于他下级的云闪付费率:' . $next->union_pay_rate;
                }
            }

            $next = Cashier::find()->where(['parent_name' => $child->username])->select(['bank_card_rate'])->orderBy(['bank_card_rate' => SORT_DESC])->one();
            if ($next) {
                if ($next->bank_card_rate > $child->bank_card_rate) {
                    $msg = $child->username . '的银行卡费率不能低于他下级的银行卡费率:' . $next->bank_card_rate;
                }
            }

        }
        return $msg;
    }

    //通过字段查询收款员
    public static function getCashierByColumn($column, $columnValue, $isAll = 0)
    {
        try {
            if ($isAll) {
                $cashiers = Cashier::find()->where("$column='$columnValue'")->asArray()->all();
            } else {
                $cashiers = Cashier::find()->where("$column='$columnValue'")->asArray()->one();
            }
            return $cashiers;
        } catch (\Exception $e) {
            return $e->getMessage();
        }
    }

    /**
     * 更新收款员各额度
     * @param $username          string      收款员用户名
     * @param $amount            number      变动金额
     * @param $updateField       string      额度字段
     * @return bool
     * @throws \yii\db\Exception
     */
    public static function updateCashierBalance($username, $amount, $updateField)
    {

        \Yii::info("{$username}--{$amount}--{$updateField}", 'cashierModel/updatecashierbalance');

        //验证额度字段
        if (!in_array($updateField, array('security_money', 'income', 'wechat_amount', 'alipay_amount', 'union_pay_amount', 'bank_card_amount'))) {
            return false;
        }

        //变动金额为负时，需要验证对应的额度是否够减
        if ($amount < 0) {
            $cashier = Cashier::find()->select('security_money, income,  wechat_amount, alipay_amount', 'union_pay_amount', 'bank_card_amount')->where('username=:username', array(':username' => $username))->asArray()->one();

            \Yii::info("{$username}--{$amount}--{$updateField}--" . json_encode($cashier, JSON_UNESCAPED_UNICODE), 'updateCashierBalance');

            if (!($cashier && isset($cashier[$updateField]) && is_numeric($cashier[$updateField]) && $cashier[$updateField] >= 0)) {
                return false;
            }

            if (abs($amount) > $cashier[$updateField]) {
                return false;
            }

        }

        //执行更改
        $res = \Yii::$app->db->createCommand(
            "update `cashier` set {$updateField}={$updateField}+:money, `update_at`=:time where `username`=:username",
            array(
                ':money' => $amount,
                ':time' => date('Y-m-d H:i:s'),
                ':username' => $username
            )
        )->execute();

        return (boolean)$res;
    }


    /**
     * @param $username
     * @param array $team
     * @return array
     * 得到团队成员
     */
    public static function calcTeam($first, &$team = [])
    {
        //先找到当前收款员的直接下级
        $next = Cashier::find()->where(['parent_name' => $first['username']])->andWhere(['<', 'cashier_status', 2])->asArray()->all();

        foreach ($next as $k => $v) {
            array_push($team, $v);
            Cashier::calcTeam($v, $team);
        }
        return $team;
    }

    /**
     * @param $team
     * @return |null
     * 统计团队当然总收款额度/次数、支付宝/微信总收款额度/次数
     */
    public static function teamIncome($team)
    {
        $data['zfbTotalMoney'] = 0;
        $data['wxTotalMoney'] = 0;
        $data['totalMoney'] = 0;
        $data['zfbTotalTimes'] = 0;
        $data['wxTotalTimes'] = 0;
        $data['totalTimes'] = 0;

        $data['zfbTotalMoneyAll'] = 0;
        $data['wxTotalMoneyAll'] = 0;
        $data['totalMoneyAll'] = 0;
        $data['zfbTotalTimesAll'] = 0;
        $data['wxTotalTimesAll'] = 0;
        $data['totalTimesAll'] = 0;
        if (!$team) {
            return $data;
        }
        //总成功数
        $zfbTotalMoneySuccess = 0;
        $wxTotalMoneySuccess = 0;
        $zfbTotalTimesSuccess = 0;
        $wxTotalTimesSuccess = 0;
        //总数（成功+失败）
        $zfbTotalMoneyAll = 0;
        $wxTotalMoneyAll = 0;
        $zfbTotalTimesAll = 0;
        $wxTotalTimesAll = 0;
        foreach ($team as $k => $v) {
            $zfbTotalMoneySuccess += Common::cashierTodayMoney($v['username'], 1, 0, 0, 1);
            $wxTotalMoneySuccess += Common::cashierTodayMoney($v['username'], 2, 0, 0, 1);
            $zfbTotalTimesSuccess += Common::cashierTodayTimes($v['username'], 1, 0, 0, 1);
            $wxTotalTimesSuccess += Common::cashierTodayTimes($v['username'], 2, 0, 0, 1);

            $zfbTotalMoneyAll += Common::cashierTodayMoney($v['username'], 1);
            $wxTotalMoneyAll += Common::cashierTodayMoney($v['username'], 2);
            $zfbTotalTimesAll += Common::cashierTodayTimes($v['username'], 1);
            $wxTotalTimesAll += Common::cashierTodayTimes($v['username'], 2);
        }
        $data['zfbTotalMoney'] = $zfbTotalMoneySuccess;
        $data['wxTotalMoney'] = $wxTotalMoneySuccess;
        $data['totalMoney'] = $zfbTotalMoneySuccess + $wxTotalMoneySuccess;
        $data['zfbTotalTimes'] = $zfbTotalTimesSuccess;
        $data['wxTotalTimes'] = $wxTotalTimesSuccess;
        $data['totalTimes'] = $zfbTotalTimesSuccess + $wxTotalTimesSuccess;

        $data['zfbTotalMoneyAll'] = $zfbTotalMoneyAll;
        $data['wxTotalMoneyAll'] = $wxTotalMoneyAll;
        $data['totalMoneyAll'] = $zfbTotalMoneyAll + $wxTotalMoneyAll;
        $data['zfbTotalTimesAll'] = $zfbTotalTimesAll;
        $data['wxTotalTimesAll'] = $wxTotalTimesAll;
        $data['totalTimesAll'] = $zfbTotalTimesAll + $wxTotalTimesAll;
        return $data;
    }

    /**
     * @param $username
     * @return null
     * 获取一级代理
     */
    public static function getFirstClass($username)
    {
        $parent = Yii::$app->redis->get(md5($username) . 'Parent');
        if ($parent) {
            return $parent;
        }
        $cashier = Cashier::find()->where(['username' => $username])->select(['parent_name'])->one();
        if (!$cashier) {
            return null;
        }
        $temp = $cashier->parent_name;
        while ($cashier->parent_name) {
            $cashier = Cashier::find()->where(['username' => $cashier->parent_name])->select(['parent_name'])->one();
            if (!($cashier->parent_name)) {
                Yii::$app->redis->set(md5($username) . 'Parent', $temp);
                return $temp;
            } else {
                $temp = $cashier->parent_name;
            }
        }
        Yii::$app->redis->set(md5($username) . 'Parent', $temp);
        return $temp;
    }

    /**
     * @param $username
     * @return null
     * 获取一级代理所有信息
     */
    public static function getFirstClassInfos($username)
    {
        $parent = Yii::$app->redis->get(md5($username) . 'ParentInfo');
        if ($parent) {
            return json_decode($parent, 1);
        }
        $cashier = Cashier::find()->where(['username' => $username])->asArray()->one();
        if (!$cashier) {
            return null;
        }
        $temp = $cashier;
        while ($cashier['parent_name']) {
            $cashier = Cashier::find()->where(['username' => $cashier['parent_name']])->asArray()->one();
            if (!isset($cashier['parent_name'])) {
                return $cashier;
            } else {
                $temp = $cashier;
            }
        }
        Yii::$app->redis->setex(md5($username) . 'ParentInfo', 300, json_encode($temp, 256));
        return $temp;
    }

    public static function getAllFollowers($cashierName, $followers = array())
    {
        $nextLevelCashiers = Cashier::find()->select('username, parent_name, agent_class, cashier_status')->where(['parent_name' => $cashierName])->asArray()->all();
        if ($nextLevelCashiers) {
            return self::getAllFollowers(array_column($nextLevelCashiers, 'username'), array_merge($followers, $nextLevelCashiers));
        }

        return $followers;
    }


    /**
     * @param int $flag
     * @return array|\yii\db\ActiveRecord[]
     */
    public static function getAllFirstLevelAgent($flag = 0){

        $cashier = Cashier::find()->where(['cashier_status' => 1])->andWhere(['agent_class' => 1])->select('username')->orderBy('username ASC')->asArray()->all();

        if($flag){
            $returnData = array();
            if($cashier){
                foreach($cashier as $k=>$v){
                    if($v['username'] && isset($returnData[$v['username']])){
                        continue;
                    }

                    //获取一代的可充值额度
                    $availableDepositAmount = \Yii::$app->redis->get('depositLimit'.$v['username']);
                    $availableDepositAmount = is_numeric($availableDepositAmount) && $availableDepositAmount > 0 ? $availableDepositAmount : 0;

                    $returnData[$v['username']] = $v['username'] . '  ---  可充值额度:  '.$availableDepositAmount;
                }
                $cashier = $returnData;
            }
            return $cashier;
        }

        return $cashier;

    }
}
