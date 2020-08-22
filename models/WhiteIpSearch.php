<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\WhiteIp;

/**
 * WhiteIpSearch represents the model behind the search form of `app\models\WhiteIp`.
 */
class WhiteIpSearch extends WhiteIp
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'ip_status'], 'integer'],
            [['user_ip', 'ip_remark', 'insert_at', 'update_at'], 'safe'],
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
        $query = WhiteIp::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if ($this->ip_status == null) {
            $query->andWhere('ip_status=1');
        } else {
            $query->andWhere(['ip_status' => $this->ip_status]);
        }

        $query->andFilterWhere(['like', 'user_ip', $this->user_ip])
            ->andFilterWhere(['like', 'ip_remark', $this->ip_remark]);

        return $dataProvider;
    }
}
