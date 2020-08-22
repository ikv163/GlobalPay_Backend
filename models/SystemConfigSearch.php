<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\SystemConfig;

/**
 * SystemConfigSearch represents the model behind the search form of `app\models\SystemConfig`.
 */
class SystemConfigSearch extends SystemConfig
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'config_status'], 'integer'],
            [['config_name', 'config_code', 'config_value', 'insert_at', 'update_at', 'remark'], 'safe'],
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
        $query = SystemConfig::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }
        if (isset($this->config_status) && $this->config_status != null) {
            $query->where(['config_status' => $this->config_status]);
        } else {
            $query->where([
                'in', 'config_status', [0, 1]
            ]);
        }

        $query->andFilterWhere(['like', 'config_name', $this->config_name])
            ->andFilterWhere(['like', 'config_code', $this->config_code])
            ->andFilterWhere(['like', 'config_value', $this->config_value])
            ->andFilterWhere(['like', 'remark', $this->remark]);

        $query->orderBy(['id' => SORT_DESC]);
        return $dataProvider;
    }
}
