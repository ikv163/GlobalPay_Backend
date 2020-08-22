<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Deposit;

/**
 * DepositSearch represents the model behind the search form of `app\models\Deposit`.
 */
class DepositSearch extends Deposit
{

    public $begintime;
    public $query_team;
    public $endtime;
    public $deposit_money_range;

    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'deposit_status'], 'integer'],
            [['system_deposit_id', 'out_deposit_id', 'username', 'deposit_remark', 'system_remark', 'insert_at', 'update_at', 'begintime', 'endtime','deposit_money_range','query_team'], 'safe'],
            [['deposit_money'], 'number'],
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
        $query = Deposit::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if (!isset($this->begintime) || empty($this->begintime)) {
            $this->begintime = date('Y-m-d 00:00:00');
        }
        if (!isset($this->endtime) || empty($this->endtime)) {
            $this->endtime = date('Y-m-d 23:59:59');
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
        if($this->deposit_money_range){
            $cRange = explode('-', $this->deposit_money_range);
            $beginAmount = $cRange && isset($cRange[0]) && is_numeric($cRange[0]) ? $cRange[0] : '';
            $endAmount = $cRange && isset($cRange[1]) && is_numeric($cRange[1]) ? $cRange[1] : '';

            if(is_numeric($beginAmount)){
                $query->andWhere("`deposit_money` >= {$beginAmount}");
            }
            if(is_numeric($endAmount) && $endAmount >= $beginAmount){
                $query->andWhere("`deposit_money` <= {$endAmount}");
            }

            unset($cRange, $beginAmount, $endAmount);
        }

        $query->andFilterWhere([
            'deposit_money' => $this->deposit_money,
            'deposit_status' => $this->deposit_status,
        ]);

        $query->andFilterWhere(['like', 'system_deposit_id', $this->system_deposit_id])
            ->andFilterWhere(['like', 'out_deposit_id', $this->out_deposit_id])
            ->andFilterWhere(['like', 'deposit_remark', $this->deposit_remark])
            ->andFilterWhere(['like', 'system_remark', $this->system_remark])
            ->andFilterWhere(['>=', 'insert_at', $this->begintime])
            ->andFilterWhere(['<=', 'insert_at', $this->endtime]);

        $query->orderBy('insert_at DESC');

        return $dataProvider;
    }
}
