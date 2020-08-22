<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SysBankcard;

/**
 * SysBankcardSearch represents the model behind the search form of `app\models\SysBankcard`.
 */
class SysBankcardSearch extends SysBankcard
{

    public $begintime;
    public $endtime;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'card_status'], 'integer'],
            [['bankcard_number', 'bankcard_owner', 'bank_code', 'bankcard_address', 'insert_at', 'update_at', 'begintime', 'endtime', 'card_owner'], 'safe'],
            [['balance'], 'number'],
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
     */
    public function search($params)
    {
        $query = SysBankcard::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if (isset($this->card_status) && $this->card_status != null) {
            $query->andWhere(['card_status' => $this->card_status]);
        } else {
            $query->andWhere(['<', 'card_status', 9]);
        }

        if(isset($this->card_owner) && is_numeric($this->card_owner) && $this->card_owner > 0){
            $query->andWhere(['card_owner'=>$this->card_owner]);
        }

        $query->andFilterWhere([
            'balance' => $this->balance,
            'card_status' => $this->card_status,
            'insert_at' => $this->insert_at,
            'update_at' => $this->update_at,
        ]);

        $query->andFilterWhere(['like', 'bankcard_number', $this->bankcard_number])
            ->andFilterWhere(['like', 'bankcard_owner', $this->bankcard_owner])
            ->andFilterWhere(['like', 'bank_code', $this->bank_code])
            ->andFilterWhere(['like', 'bankcard_address', $this->bankcard_address]);

        return $dataProvider;
    }
}
