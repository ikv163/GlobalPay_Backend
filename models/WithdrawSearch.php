<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * WithdrawSearch represents the model behind the search form of `app\models\Withdraw`.
 */
class WithdrawSearch extends Withdraw
{

    public $begintime;
    public $query_team;
    public $endtime;
    public $withdraw_money_range;
    public $withdraw_bankcard_number;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_type', 'bankcard_id', 'withdraw_status'], 'integer'],
            [['system_withdraw_id', 'out_withdraw_id', 'username', 'withdraw_remark', 'system_remark', 'insert_at', 'update_at', 'begintime', 'endtime', 'withdraw_money_range', 'withdraw_bankcard_number', 'query_team'], 'safe'],
            [['withdraw_money'], 'number'],
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
        $query = Withdraw::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        //查询下级
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

        //订单金额
        if ($this->withdraw_money_range) {
            $cRange = explode('-', $this->withdraw_money_range);
            $beginAmount = $cRange && isset($cRange[0]) && is_numeric($cRange[0]) ? $cRange[0] : '';
            $endAmount = $cRange && isset($cRange[1]) && is_numeric($cRange[1]) ? $cRange[1] : '';

            if (is_numeric($beginAmount)) {
                $query->andWhere("`withdraw_money` >= {$beginAmount}");
            }
            if (is_numeric($endAmount) && $endAmount >= $beginAmount) {
                $query->andWhere("`withdraw_money` <= {$endAmount}");
            }

            unset($cRange, $beginAmount, $endAmount);
        }

        $query->andFilterWhere([
            'user_type' => $this->user_type,
            'withdraw_money' => $this->withdraw_money,
            'bankcard_id' => $this->bankcard_id,
            'withdraw_status' => $this->withdraw_status,
        ]);

        $query->andFilterWhere(['like', 'system_withdraw_id', $this->system_withdraw_id])
            ->andFilterWhere(['like', 'out_withdraw_id', $this->out_withdraw_id])
            ->andFilterWhere(['like', 'withdraw_remark', $this->withdraw_remark])
            ->andFilterWhere(['>=', 'insert_at', $this->begintime])
            ->andFilterWhere(['<=', 'insert_at', $this->endtime]);

        $query->orderBy('insert_at DESC');

        return $dataProvider;
    }
}
