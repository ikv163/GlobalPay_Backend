<?php

namespace app\models;

use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\FinanceDetail;

/**
 * FinanceDetailSearch represents the model behind the search form of `app\models\FinanceDetail`.
 */
class FinanceDetailSearch extends FinanceDetail
{

    public $begintime;
    public $endtime;
    public $c_amount_range;
    public $b_amount_range;
    public $a_amount_range;
    public $query_team;


    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'user_type', 'finance_type'], 'integer'],
            [['change_amount', 'before_amount', 'after_amount'], 'number'],
            [['query_team','username', 'insert_at', 'remark', 'begintime', 'endtime', 'c_amount_range', 'b_amount_range','a_amount_range'], 'safe'],
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
        $query = FinanceDetail::find();

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

        //交易金额
        if($this->c_amount_range){
            $cRange = explode('-', $this->c_amount_range);
            $beginAmount = $cRange && isset($cRange[0]) && is_numeric($cRange[0]) ? $cRange[0] : '';
            $endAmount = $cRange && isset($cRange[1]) && is_numeric($cRange[1]) ? $cRange[1] : '';

            if(is_numeric($beginAmount)){
                $query->andWhere("`change_amount` >= {$beginAmount}");
            }
            if(is_numeric($endAmount) && $endAmount >= $beginAmount){
                $query->andWhere("`change_amount` <= {$endAmount}");
            }

            unset($cRange, $beginAmount, $endAmount);
        }

        //交易前金额
        if($this->b_amount_range){
            $cRange = explode('-', $this->b_amount_range);
            $beginAmount = $cRange && isset($cRange[0]) && is_numeric($cRange[0]) ? $cRange[0] : '';
            $endAmount = $cRange && isset($cRange[1]) && is_numeric($cRange[1]) ? $cRange[1] : '';

            if(is_numeric($beginAmount)){
                $query->andWhere("`before_amount` >= {$beginAmount}");
            }
            if(is_numeric($endAmount) && $endAmount >= $endAmount){
                $query->andWhere("`before_amount` <= {$endAmount}");
            }

            unset($cRange, $beginAmount, $endAmount);
        }

        //交易后金额
        if($this->a_amount_range){
            $cRange = explode('-', $this->a_amount_range);
            $beginAmount = $cRange && isset($cRange[0]) && is_numeric($cRange[0]) ? $cRange[0] : '';
            $endAmount = $cRange && isset($cRange[1]) && is_numeric($cRange[1]) ? $cRange[1] : '';

            if(is_numeric($beginAmount)){
                $query->andWhere("`after_amount` >= {$beginAmount}");
            }
            if(is_numeric($endAmount) && $endAmount >= $endAmount){
                $query->andWhere("`after_amount` <= {$endAmount}");
            }

            unset($cRange, $beginAmount, $endAmount);
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'change_amount' => $this->change_amount,
            'before_amount' => $this->before_amount,
            'after_amount' => $this->after_amount,
            'user_type' => $this->user_type,
            'finance_type' => $this->finance_type,
        ]);

        $query->andFilterWhere(['like', 'remark', $this->remark])
            ->andFilterWhere(['>=', 'insert_at', $this->begintime])
            ->andFilterWhere(['<=', 'insert_at', $this->endtime]);

        $query->orderBy('insert_at DESC');
        return $dataProvider;
    }
}
