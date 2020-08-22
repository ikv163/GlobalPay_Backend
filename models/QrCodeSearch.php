<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\QrCode;

/**
 * QrCodeSearch represents the model behind the search form of `app\models\QrCode`.
 */
class QrCodeSearch extends QrCode
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
    public function rules()
    {
        return [
            [['id', 'per_day_orders', 'qr_type', 'qr_status', 'priority', 'control', 'is_shopowner'], 'integer'],
            [['username', 'qr_relation', 'qr_code', 'qr_address', 'qr_nickname', 'qr_account', 'last_money_time', 'insert_at', 'update_at', 'query_team', 'last_money_time_start', 'last_money_time_end', 'last_code_time_start', 'last_code_time_end', 'insert_at_start', 'insert_at_end', 'qr_location', 'real_name', 'bank_card_number', 'bank_address', 'bank_code','telphone'], 'safe'],
            [['per_max_amount', 'per_min_amount', 'per_day_amount'], 'number'],
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
        $query = QrCode::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if (isset($this->qr_status) && $this->qr_status != null) {
            $query->andWhere(['=', 'qr_status', $this->qr_status]);
        } else {
            $query->andWhere(['<', 'qr_status', 9]);
        }

        //查询下级的二维码
        $next = [];
        if ($this->username && empty($this->query_team)) {
            $query->andFilterWhere(['=', 'username', $this->username]);
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
            $query->andFilterWhere(['in', 'username', $next]);
        }

        $query->andFilterWhere([
            'per_max_amount' => $this->per_max_amount,
            'per_min_amount' => $this->per_min_amount,
            'per_day_amount' => $this->per_day_amount,
            'per_day_orders' => $this->per_day_orders,
            'qr_type' => $this->qr_type,
            'qr_status' => $this->qr_status,
            'priority' => $this->priority,
            'last_money_time' => $this->last_money_time,
            'control' => $this->control,
            'is_shopowner' => $this->is_shopowner,
            'qr_relation' => $this->qr_relation,
        ]);
        if ($this->qr_location == '空') {
            $query->andWhere(['=', 'qr_location', '']);
        } else {
            $query->andFilterWhere(['like', 'qr_location', $this->qr_location]);
        }

        $query->andFilterWhere(['like', 'qr_code', $this->qr_code])
            ->andFilterWhere(['like', 'qr_address', $this->qr_address])
            ->andFilterWhere(['like', 'qr_nickname', $this->qr_nickname])
            ->andFilterWhere(['>=', 'insert_at', $this->insert_at_start])
            ->andFilterWhere(['<=', 'insert_at', $this->insert_at_end])
            ->andFilterWhere(['>=', 'last_code_time', $this->last_code_time_start])
            ->andFilterWhere(['<=', 'last_code_time', $this->last_code_time_end])
            ->andFilterWhere(['>=', 'last_money_time', $this->last_money_time_start])
            ->andFilterWhere(['<=', 'last_money_time', $this->last_money_time_end])
            ->andFilterWhere(['like', 'qr_account', $this->qr_account]);
        $query->orderBy(['id' => SORT_DESC]);
        return $dataProvider;
    }
}
