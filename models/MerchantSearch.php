<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Merchant;

/**
 * MerchantSearch represents the model behind the search form of `app\models\Merchant`.
 */
class MerchantSearch extends Merchant
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'mch_status'], 'integer'],
            [['mch_name', 'mch_code', 'mch_key', 'pay_password', 'telephone', 'insert_at', 'update_at', 'remark'], 'safe'],
            [['available_money', 'used_money', 'balance', 'wechat_rate', 'alipay_rate', 'union_pay_rate', 'bank_card_rate'], 'number'],
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
        $query = Merchant::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andFilterWhere([
            'mch_status' => $this->mch_status,
            'available_money' => $this->available_money,
            'used_money' => $this->used_money,
            'balance' => $this->balance,
            'wechat_rate' => $this->wechat_rate,
            'alipay_rate' => $this->alipay_rate,
            'insert_at' => $this->insert_at,
            'update_at' => $this->update_at,
        ]);

        $query->andFilterWhere(['like', 'mch_name', $this->mch_name])
            ->andFilterWhere(['like', 'mch_code', $this->mch_code])
            ->andFilterWhere(['like', 'mch_key', $this->mch_key])
            ->andFilterWhere(['like', 'pay_password', $this->pay_password])
            ->andFilterWhere(['like', 'telephone', $this->telephone])
            ->andFilterWhere(['like', 'remark', $this->remark]);

        return $dataProvider;
    }
}
