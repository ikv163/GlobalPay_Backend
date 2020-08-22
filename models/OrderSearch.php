<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Order;

/**
 * OrderSearch represents the model behind the search form of `app\models\Order`.
 */
class OrderSearch extends Order
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'order_type', 'order_status', 'notify_status'], 'integer'],
            [['order_id', 'mch_order_id', 'username', 'qr_code', 'mch_name', 'callback_url', 'notify_url', 'expire_time', 'read_remark', 'insert_at', 'update_at', 'operator', 'insert_at_start', 'insert_at_end', 'update_at_start', 'update_at_end', 'query_team', 'is_settlement', 'refund_status'], 'safe'],
            [['order_fee', 'order_amount', 'benefit', 'actual_amount'], 'number'],
        ];
    }

    /**
     * {@inheritdoc}
     */
    public function scenarios()
    {
        return Model::scenarios();
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     * type为1时，代表统计查询
     */
    public function search($params, $type = 0)
    {
        $query = Order::find()->select(['order.*', 'qr_code.qr_location'])->leftJoin('qr_code', 'order.qr_code = qr_code.qr_code')->orderBy(['order.insert_at' => SORT_DESC]);
        if (isset($params['order_id'])) {
            $query->Where(['=', 'order.order_id',$params['order_id']]);
        }
        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);
        $this->load($params);
        if (!$this->validate()) {
            if ($type == 1) {
                return $query;
            } else {
                return $dataProvider;
            }
        }

        //查询下级
        $next = [];
        if ($this->username && empty($this->query_team)) {
            $query->andFilterWhere(['=', 'order.username', $this->username]);
        } elseif ($this->username && $this->query_team) {
            array_push($next, $this->username);
            $team = Cashier::calcTeam(Cashier::findOne(['username' => $this->username]));
            if ($team) {
                //直接下级
                if ($this->query_team == 1) {
                    foreach ($team as $v) {
                        if ($v['parent_name'] == $this->username) {
                            array_push($next, $v['username']);
                        }
                    }
                } else {
                    //所有下级
                    foreach ($team as $v) {
                        array_push($next, $v['username']);
                    }
                }
            }
        }

        \Yii::info(json_encode($next, 256), 'NextCasher');

        if (count($next)) {
            $query->andFilterWhere(['in', 'order.username', $next]);
        }

        if ($this->order_status == 999) {
            $query->andFilterWhere(['in', 'order.order_status', [2, 5]]);
        } elseif (isset($this->order_status) && !empty($this->order_status)) {
            $query->andFilterWhere(['=', 'order.order_status', $this->order_status]);
        }

        $query->andFilterWhere([
            'order.is_settlement' => $this->is_settlement,
            'order.order_type' => $this->order_type,
            'order.order_amount' => $this->order_amount,
            'order.notify_status' => $this->notify_status,
        ]);

        if (!empty($this->update_at_start) && !empty($this->update_at_end)) {
            $this->insert_at_start = $this->insert_at_end = null;
        } else {
            if (!isset($this->insert_at_start) || empty($this->insert_at_start)) {
                $this->insert_at_start = date('Y-m-d 00:00:00');
            }
            if (!isset($this->insert_at_end) || empty($this->insert_at_end)) {
                $this->insert_at_end = date('Y-m-d 23:59:59');
            }
        }

        $query->andFilterWhere(['like', 'order.order_id', $this->order_id])
            ->andFilterWhere(['like', 'order.mch_order_id', $this->mch_order_id])
            ->andFilterWhere(['=', 'order.qr_code', $this->qr_code])
            ->andFilterWhere(['=', 'order.mch_name', $this->mch_name])
            ->andFilterWhere(['>=', 'order.insert_at', $this->insert_at_start])
            ->andFilterWhere(['<=', 'order.insert_at', $this->insert_at_end])
            ->andFilterWhere(['>=', 'order.update_at', $this->update_at_start])
            ->andFilterWhere(['<=', 'order.update_at', $this->update_at_end])
            ->andFilterWhere(['like', 'order.operator', $this->operator])
            ->andFilterWhere(['like', 'order.read_remark', $this->read_remark]);
        if ($type == 1) {
            return $query;
        } else {
            return $dataProvider;
        }
    }

    /**
     * Creates data provider instance with search query applied
     *
     * @param array $params
     *
     * @return ActiveDataProvider
     */
    public function summary($params)
    {
        $query = Order::find()->select(['order.*', 'qr_code.qr_location', 'refund.refund_status', 'refund.refund_type'])->leftJoin('qr_code', 'order.qr_code = qr_code.qr_code')->leftJoin('refund', 'refund.order_id = order.order_id')->orderBy(['order.insert_at' => SORT_DESC]);

        $this->load($params);

        if (!$this->validate()) {
            return null;
        }

        //查询下级
        $next = [];
        if ($this->username && empty($this->query_team)) {
            $query->andFilterWhere(['=', 'order.username', $this->username]);
        } elseif ($this->username && $this->query_team) {
            array_push($next, $this->username);
            $team = Cashier::calcTeam(Cashier::findOne(['username' => $this->username]));
            if ($team) {
                //直接下级
                if ($this->query_team == 1) {
                    foreach ($team as $v) {
                        if ($v['parent_name'] == $this->username) {
                            array_push($next, $v['username']);
                        }
                    }
                } else {
                    //所有下级
                    foreach ($team as $v) {
                        array_push($next, $v['username']);
                    }
                }
            }
        }

        if (count($next)) {
            $query->andFilterWhere(['in', 'order.username', $next]);
        }

        if ($this->order_status == 999) {
            $query->andFilterWhere(['in', 'order.order_status', [2, 5]]);
        } elseif (isset($this->order_status) && !empty($this->order_status)) {
            $query->andFilterWhere(['=', 'order.order_status', $this->order_status]);
        }

        if ($this->refund_status == 999) {
            $orderIds = Refund::find()->where(['refund_status' => 2])->select(['order_id'])->asArray()->all();
            $orderIds = array_column($orderIds, 'order_id');
            $query->andFilterWhere(['not in', 'order.order_id', $orderIds]);
        } elseif (isset($this->refund_status) && !empty($this->refund_status)) {
            $query->andFilterWhere(['=', 'refund.refund_status', $this->refund_status]);
        }

        $query->andFilterWhere([
            'order.is_settlement' => $this->is_settlement,
            'order.order_type' => $this->order_type,
            'order.order_amount' => $this->order_amount,
            'order.notify_status' => $this->notify_status
        ]);

        if (!empty($this->update_at_start) && !empty($this->update_at_end)) {
            $this->insert_at_start = $this->insert_at_end = null;
        } else {
            if (!isset($this->insert_at_start) || empty($this->insert_at_start)) {
                $this->insert_at_start = date('Y-m-d 00:00:00');
            }
            if (!isset($this->insert_at_end) || empty($this->insert_at_end)) {
                $this->insert_at_end = date('Y-m-d 23:59:59');
            }
        }

        $query->andFilterWhere(['like', 'order.order_id', $this->order_id])
            ->andFilterWhere(['like', 'order.mch_order_id', $this->mch_order_id])
            ->andFilterWhere(['=', 'order.qr_code', $this->qr_code])
            ->andFilterWhere(['=', 'order.mch_name', $this->mch_name])
            ->andFilterWhere(['>=', 'order.insert_at', $this->insert_at_start])
            ->andFilterWhere(['<=', 'order.insert_at', $this->insert_at_end])
            ->andFilterWhere(['>=', 'order.update_at', $this->update_at_start])
            ->andFilterWhere(['<=', 'order.update_at', $this->update_at_end])
            ->andFilterWhere(['=', 'order.operator', $this->operator])
            ->andFilterWhere(['like', 'order.read_remark', $this->read_remark]);

        return $query;
    }
}
