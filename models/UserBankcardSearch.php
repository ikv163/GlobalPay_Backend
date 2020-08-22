<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\UserBankcard;

/**
 * UserBankcardSearch represents the model behind the search form of `app\models\UserBankcard`.
 */
class UserBankcardSearch extends UserBankcard
{

    public $begintime;
    public $endtime;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_type', 'card_status'], 'integer'],
            [['bankcard_number', 'bankcard_owner', 'bank_code', 'bankcard_address', 'username', 'insert_at', 'update_at', 'begintime', 'endtime'], 'safe'],
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
        $query = UserBankcard::find();

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

        $query->andFilterWhere([
            'user_type' => $this->user_type,
            'card_status' => $this->card_status,
        ]);

        $query->andFilterWhere(['like', 'bankcard_number', $this->bankcard_number])
            ->andFilterWhere(['like', 'bankcard_owner', $this->bankcard_owner])
            ->andFilterWhere(['like', 'bank_code', $this->bank_code])
            ->andFilterWhere(['like', 'bankcard_address', $this->bankcard_address])
            ->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['>=', 'insert_at', $this->begintime])
            ->andFilterWhere(['<=', 'insert_at', $this->endtime]);

        $query->orderBy('card_status ASC, insert_at DESC');

        return $dataProvider;
    }
}
