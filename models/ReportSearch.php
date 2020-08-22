<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use yii\data\ArrayDataProvider;

/**
 * ReportSearch represents the model behind the search form of `app\models\Report`.
 */
class ReportSearch extends Report
{

    public $begintime;
    public $endtime;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_type'], 'integer'],
            [['datas'], 'string', 'max' => 1500],
            [['username', 'finance_date', 'datas', 'insert_at', 'update_at', 'begintime', 'endtime'], 'safe'],
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
    public function search($params, $userType = 1)
    {
        $query = Report::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        $query->andWhere("`user_type` = $userType");


        if (!isset($this->begintime)) {
            $this->begintime = date('Y-m-d');
        }
        if (!isset($this->endtime)) {
            $this->endtime = date("Y-m-d");
        }

        $query->andFilterWhere(['like', 'username', $this->username])
            ->andFilterWhere(['>=', 'finance_date', $this->begintime])
            ->andFilterWhere(['<=', 'finance_date', $this->endtime . ' 23:59:59']);

        $query->orderBy('finance_date DESC');

        return $dataProvider;
    }
}
