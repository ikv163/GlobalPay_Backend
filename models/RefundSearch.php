<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Refund;

/**
 * RefundSearch represents the model behind the search form of `app\models\Refund`.
 */
class RefundSearch extends Refund
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'refund_type', 'refund_status'], 'integer'],
            [['mch_order_id', 'order_id', 'username', 'qr_code', 'mch_name', 'order_type', 'order_fee', 'order_amount', 'actual_amount', 'order_status', 'is_settlement', 'insert_at', 'update_at', 'insert_at_start', 'insert_at_end', 'operator', 'remark'], 'safe'],
            [['refund_money'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function search($params)
    {
        $query = Refund::find()
            ->select(['order.mch_order_id', 'order.qr_code', 'order.mch_name', 'order.order_type', 'order.order_fee', 'order.order_amount', 'order.order_status', 'order.is_settlement', 'cashier.username', 'cashier.wechat_rate', 'cashier.alipay_rate', 'refund.*'])
            ->leftJoin('order', 'refund.order_id = order.order_id')
            ->leftJoin('cashier', 'order.username = cashier.username');

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        if (!isset($this->insert_at_start) || empty($this->insert_at_start)) {
            $this->insert_at_start = date('Y-m-d 00:00:00');
        }
        if (!isset($this->insert_at_end) || empty($this->insert_at_end)) {
            $this->insert_at_end = date('Y-m-d 23:59:59');
        }

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'refund.refund_money' => $this->refund_money,
            'refund.refund_type' => $this->refund_type,
            'order.username' => $this->username,
            'order.qr_code' => $this->qr_code,
            'order.order_status' => $this->order_status,
            'order.is_settlement' => $this->is_settlement,
            'order.order_type' => $this->order_type,
            'refund.refund_status' => $this->refund_status,
        ]);

        $query->andFilterWhere(['like', 'refund.order_id', $this->order_id])
            ->andFilterWhere(['like', 'order.mch_order_id', $this->mch_order_id])
            ->andFilterWhere(['like', 'refund.operator', $this->operator])
            ->andFilterWhere(['>=', 'refund.insert_at', $this->insert_at_start])
            ->andFilterWhere(['<=', 'refund.insert_at', $this->insert_at_end])
            ->andFilterWhere(['like', 'refund.remark', $this->remark]);
        $query->orderBy(['id' => SORT_DESC]);
        return $dataProvider;
    }
}
