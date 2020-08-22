<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\TransactionFlow;

/**
 * TransactionFlowSearch represents the model behind the search form of `app\models\TransactionFlow`.
 */
class TransactionFlowSearch extends TransactionFlow
{
    public $insert_at_start;
    public $insert_at_end;
    public $trans_time_start;
    public $trans_time_end;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'client_id', 'trade_type', 'trans_type', 'trans_status', 'trade_cate'], 'integer'],
            [['client_code', 'trans_id', 'trans_account', 'trans_time', 'trans_remark', 'trans_username', 'read_remark', 'md5_sign', 'pick_at', 'insert_at', 'update_at', 'insert_at_start', 'insert_at_end', 'trans_time_start', 'trans_time_end'], 'safe'],
            [['trans_amount', 'trans_fee', 'before_balance', 'trans_balance'], 'number'],
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
        $query = TransactionFlow::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if (!isset($this->trans_time_start) || empty($this->trans_time_start)) {
            $this->trans_time_start = date('Y-m-d 00:00:00');
        }
        if (!isset($this->trans_time_end) || empty($this->trans_time_end)) {
            $this->trans_time_end = date('Y-m-d 23:59:59');
        }


        $query->andFilterWhere([
            'client_id' => $this->client_id,
            'trade_type' => $this->trade_type,
            'trans_type' => $this->trans_type,
            'trade_cate' => $this->trade_cate,
            'trans_amount' => $this->trans_amount,
            'trans_status' => $this->trans_status,
        ]);

        $query->andFilterWhere(['like', 'client_code', $this->client_code])
            ->andFilterWhere(['like', 'trans_id', $this->trans_id])
            ->andFilterWhere(['like', 'trans_account', $this->trans_account])
            ->andFilterWhere(['like', 'trans_username', $this->trans_username])
            ->andFilterWhere(['like', 'read_remark', $this->read_remark])
            ->andFilterWhere(['>=', 'insert_at', $this->insert_at_start])
            ->andFilterWhere(['<=', 'insert_at', $this->insert_at_end])
            ->andFilterWhere(['>=', 'trans_time', $this->trans_time_start])
            ->andFilterWhere(['<=', 'trans_time', $this->trans_time_end]);
        $query->orderBy(['trans_time' => SORT_DESC]);
        return $dataProvider;
    }
}
