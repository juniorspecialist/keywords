<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Financy;

/**
 * FinancySearch represents the model behind the search form about `app\models\Financy`.
 */
class FinancySearch extends Financy
{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'type_operation', 'sum_operation', 'created_at'], 'integer'],
        ];
    }

    /**
     * @inheritdoc
     */
    public function scenarios()
    {
        // bypass scenarios() implementation in the parent class
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
        $query = Financy::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
        ]);

        $this->load($params);

        //если пользователь НЕ админ, показываем только его финансы
        //if user not admin
        if(Yii::$app->user->identity->isAdmin()){

            $query->joinWith(['user']);



//            $query->andFilterWhere([
//                'user_id' => Yii::$app->user->id,
//            ]);
        }else{
            $query->andFilterWhere([
                'user_id' => Yii::$app->user->id,
            ]);
        }

        $query->orderBy('financy.id DESC');
        if (!$this->validate()) {
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere([
            'id' => $this->id,
            'user_id' => $this->user_id,
            'type_operation' => $this->type_operation,
            'sum_operation' => $this->sum_operation,
            'created_at' => $this->created_at,
        ]);

        return $dataProvider;
    }
}
