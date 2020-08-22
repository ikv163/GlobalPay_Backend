<?php

namespace app\models;

use Yii;
use app\common\Common;

/**
 * This is the model class for table "qr_code".
 *
 * @property int $id
 * @property string $username 二维码所属人（cashier）
 * @property string $qr_code 二维码简码别名
 * @property string $qr_address 二维码地址
 * @property string $qr_nickname 二维码用户昵称
 * @property string $qr_account 二维码实际账号
 * @property string $per_max_amount 每笔最大可收金额
 * @property string $per_min_amount 每笔最小可收金额
 * @property string $per_day_amount 每日可收金额
 * @property int $per_day_orders 每日接单笔数上限
 * @property int $qr_type 二维码类型 1支付宝 2微信
 * @property int $qr_status 二维码状态 0禁用 1可用 2接单 9删除
 * @property int $priority 二维码优先收款等级 默认0 越大越优先
 * @property string $last_money_time 上次收款时间
 * @property string $last_code_time 上次出码时间
 * @property String $control 控制权 自由 平台 代理 控制
 * @property int $is_shopowner 是否为店长二维码 1店长码 2店员码
 * @property int $qr_relation 关联的店员码ID（只有店长码才能进行关联）
 * @property string $insert_at 添加时间
 * @property string $update_at 修改时间
 */
class QrCode extends \yii\db\ActiveRecord
{
    public $last_money_time_start;
    public $last_money_time_end;
    public $last_code_time_start;
    public $last_code_time_end;
    public $insert_at_start;
    public $insert_at_end;
    public $query_team;


    /**
     * {@inheritdoc}
     */
    public static function tableName()
    {
        return 'qr_code';
    }

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['username', 'qr_code', 'qr_address', 'qr_nickname', 'qr_account', 'per_max_amount', 'per_min_amount', 'per_day_amount', 'per_day_orders', 'qr_type', 'qr_status', 'is_shopowner'], 'required'],
            [['per_max_amount', 'per_min_amount', 'per_day_amount'], 'number'],
            [['per_day_orders', 'qr_type', 'qr_status', 'priority', 'is_shopowner'], 'integer'],
            [['last_money_time', 'last_code_time', 'insert_at', 'update_at', 'query_team', 'telephone'], 'safe'],
            [['username', 'control', 'qr_code', 'qr_nickname', 'qr_account', 'qr_location', 'qr_relation', 'alipay_uid'], 'string', 'max' => 50],
            [['real_name', 'bank_card_number'], 'string', 'max' => 30],
            [['bank_code'], 'string', 'max' => 20],
            [['qr_address', 'qr_remark', 'bank_address'], 'string', 'max' => 255],
            [['qr_code', 'qr_address'], 'unique'],
            [['allow_order_type'], 'in', 'range'=>function($model){
                return Order::getQRAllowOrderTypes($model->qr_type);
            }],
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
            'qr_code' => Yii::t('app/model', 'Qr Code'),
            'qr_address' => Yii::t('app/model', 'Qr Address'),
            'qr_nickname' => Yii::t('app/model', 'Qr Nickname'),
            'qr_account' => Yii::t('app/model', 'Qr Account'),
            'per_max_amount' => Yii::t('app/model', 'Per Max Amount'),
            'per_min_amount' => Yii::t('app/model', 'Per Min Amount'),
            'per_day_amount' => Yii::t('app/model', 'Per Day Amount'),
            'per_day_orders' => Yii::t('app/model', 'Per Day Orders'),
            'qr_type' => Yii::t('app/model', 'Qr Type'),
            'qr_status' => Yii::t('app/model', 'Qr Status'),
            'priority' => Yii::t('app/model', 'Priority'),
            'last_money_time' => Yii::t('app/model', 'Last Money Time'),
            'last_code_time' => Yii::t('app/model', 'Last Code Time'),
            'control' => Yii::t('app/model', 'Control'),
            'is_shopowner' => Yii::t('app/model', 'Is Shopowner'),
            'qr_relation' => Yii::t('app/model', 'Qr Relation'),
            'insert_at' => Yii::t('app/model', 'Insert At'),
            'update_at' => Yii::t('app/model', 'Update At'),
            'qr_addressAccount' => Yii::t('app/model', 'qr_addressAccount'),
            'last_money_time_start' => Yii::t('app/model', 'Last Money Time Start'),
            'last_money_time_end' => Yii::t('app/model', 'Last Money Time End'),
            'last_code_time_start' => Yii::t('app/model', 'Last Code Time Start'),
            'last_code_time_end' => Yii::t('app/model', 'Last Code Time End'),
            'insert_at_start' => Yii::t('app/model', 'Insert At Start'),
            'insert_at_end' => Yii::t('app/model', 'Insert At End'),
            'qr_location' => Yii::t('app/model', 'Qr Location'),
            'query_team' => Yii::t('app/model', 'query_team'),
            'qr_remark' => Yii::t('app/model', 'qr_remark'),
            'alipay_uid' => Yii::t('app/model', 'alipay_uid'),
            'real_name' => Yii::t('app/model', 'real_name'),
            'bank_card_number' => Yii::t('app/model', 'bank_card_number'),
            'bank_code' => Yii::t('app/model', 'bank_code'),
            'bank_address' => Yii::t('app/model', 'bank_address'),
            'telephone' => Yii::t('app/model', 'Telephone'),
            'allow_order_type' => Yii::t('app/model', 'allow_order_type'),
        ];
    }

    //获取所有店员码--主要获取可用的店员码用在添加/修改二维码功能中
    public static function getAllClerkQr($type = 0, $qrType = 1)
    {
        if ($type == 0) {
            $clerk = QrCode::find()->where('is_shopowner=2')->andWhere("qr_type=$qrType")->andWhere("qr_status !=9 ")->select(['id', 'qr_code', 'qr_nickname', 'qr_account'])->indexBy('id')->asArray()->all();
            if ($clerk) {
                $temp = [];
                foreach ($clerk as $item) {
                    $temp[$item['qr_code']] = $item['qr_code'] . '-【昵称：' . $item['qr_nickname'] . '】【账号：' . $item['qr_account'] . '】';
                }
                $clerk = $temp;
            }
        } else {
            $clerk = QrCode::find()->where('is_shopowner=2')->andWhere("qr_type=$qrType")->andWhere("qr_status !=9 ")->indexBy('id')->asArray()->all();
        }
        return $clerk;
    }


    /**
     * 获取二维码的日统计数据
     */
    public static function getQrCodeDailyStatistics($qrCode)
    {

        $fields = 'qr_code.id, qr_code.username, qr_code.qr_code, qr_code.qr_type, qr_code.qr_status, cashier.alipay_rate, cashier.wechat_rate';

        $sql = "select {$fields} from qr_code left join cashier on qr_code.username = cashier.username where qr_code.qr_code = :qr_code";
        $params = array(
            ':qr_code' => $qrCode,
        );

        $data = \Yii::$app->db->createCommand($sql, $params)->queryOne();

        //从redis中取码的统计数据
        //接单金额
        $totalMoney = Common::qrTodayMoney($qrCode);
        $totalMoney = is_numeric($totalMoney) && $totalMoney >= 0 ? $totalMoney : 0;

        //成功接单金额
        $successMoney = Common::qrTodayMoney($qrCode, 0, 0, 1);
        $successMoney = is_numeric($successMoney) && $successMoney >= 0 ? $successMoney : 0;

        //接单笔数
        $totalTimes = Common::qrTodayTimes($qrCode);
        $totalTimes = is_numeric($totalTimes) && $totalTimes >= 0 ? intval($totalTimes) : 0;

        //成功接单笔数
        $successTimes = Common::qrTodayTimes($qrCode, 0, 0, 1);
        $successTimes = is_numeric($successTimes) && $successTimes >= 0 ? intval($successTimes) : 0;

        //成功率
        $successRate = $successTimes > 0 && $totalTimes > 0 ? round(($successTimes / $totalTimes * 100), 2) : 0;

        //收益
        $type = $data && isset($data['qr_type']) && is_numeric($data['qr_type']) && $data['qr_type'] > 0 && intval($data['qr_type']) == $data['qr_type'] ? intval($data['qr_type']) : 0;
        switch ($type) {
            case 1 :
                $rate = $data && isset($data['alipay_rate']) && is_numeric($data['alipay_rate']) && $data['alipay_rate'] > 0 ? $data['alipay_rate'] : 0;
                break;

            case 2 :
                $rate = $data && isset($data['wechat_rate']) && is_numeric($data['wechat_rate']) && $data['wechat_rate'] > 0 ? $data['wechat_rate'] : 0;
                break;

            default :
                $rate = 0;
                break;
        }

        $income = bcmul(($successMoney / 100), $rate, 2);


        //组合最终返回的数据
        $returnData = array(
            'total_money' => sprintf('%.2f', $totalMoney),                       //日接单金额
            'total_success_money' => sprintf('%.2f', $successMoney),             //日成功接单金额
            'total_order' => $totalTimes,                                               //日接单笔数
            'total_success_order' => $successTimes,                                     //日接单成功接单笔数
            'income' => sprintf('%.2f', $income),                               //日收益金额
            'success_rate' => sprintf('%.2f', $successRate) . '%',                 //接单成功率
        );
        return $returnData;
    }

}
