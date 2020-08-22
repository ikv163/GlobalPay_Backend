<?php

namespace app\models;

use app\models\Cashier;
use yii\base\Model;
use yii\data\ActiveDataProvider;

/**
 * CashierSearch represents the model behind the search form of `app\models\Cashier`.
 */
class CashierSearch extends Cashier
{
    /**
     * {@inheritdoc}
     */
    public function rules()
    {
        return [
            [['id', 'agent_class', 'cashier_status'], 'integer'],
            [['username', 'login_password', 'pay_password', 'parent_name', 'wechat', 'alipay', 'telephone', 'insert_at', 'insert_at_start', 'insert_at_end', 'update_at', 'login_at', 'remark', 'query_team', 'invite_code'], 'safe'],
            [['income', 'security_money', 'wechat_rate', 'alipay_rate', 'wechat_amount', 'alipay_amount'], 'number'],
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
        $query = Cashier::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        if (!$this->validate()) {
            return $dataProvider;
        }

        if (isset($this->cashier_status) && $this->cashier_status != null) {
            $query->andWhere(['=', 'cashier_status', $this->cashier_status]);
        } else {
            $query->andWhere(['<', 'cashier_status', 2]);
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
            $query->orderBy(['agent_class' => SORT_ASC]);
        }

        $query->andFilterWhere([
            'income' => $this->income,
            'security_money' => $this->security_money,
            'wechat_rate' => $this->wechat_rate,
            'alipay_rate' => $this->alipay_rate,
            'wechat_amount' => $this->wechat_amount,
            'invite_code' => $this->invite_code,
            'alipay_amount' => $this->alipay_amount,
            'agent_class' => $this->agent_class,
        ]);

        $query->andFilterWhere(['like', 'parent_name', $this->parent_name])
            ->andFilterWhere(['like', 'wechat', $this->wechat])
            ->andFilterWhere(['like', 'alipay', $this->alipay])
            ->andFilterWhere(['like', 'telephone', $this->telephone])
            ->andFilterWhere(['like', 'remark', $this->remark])
            ->andFilterWhere(['>=', 'insert_at', $this->insert_at_start])
            ->andFilterWhere(['<=', 'insert_at', $this->insert_at_end]);
        $query->addOrderBy(['alipay_amount' => SORT_DESC, 'wechat_amount' => SORT_DESC]);
        return $dataProvider;
    }
}
