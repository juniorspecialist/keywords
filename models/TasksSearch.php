<?php

namespace app\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\models\Tasks;

/**
 * TasksSearch represents the model behind the search form about `app\models\Tasks`.
 */
class TasksSearch extends Tasks
{

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [

            [['desc','created_at','complete_at'], function ($attribute) {
                $this->$attribute = \yii\helpers\HtmlPurifier::process($this->$attribute);
            }],

            [['created_at','complete_at'], function ($attribute) {
                if (!\DateTime::createFromFormat('Y-m-d', $attribute)){
                    $this->$attribute = time();
                }else{
                    $this->$attribute = strtotime($this->$attribute);
                }
            }],

            [['created_at','user_id', 'status', 'complete_at'], 'integer'],

            [['words', 'file_result', 'desc'], 'safe'],
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
        $query = Tasks::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=>false
        ]);

        $this->load($params);

        if (!$this->validate()) {

            //default show tasks of user
            $query->andFilterWhere([
                'user_id' => Yii::$app->user->id,
            ]);
            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        //if user not admin
        if(!Yii::$app->user->identity->isAdmin()){
            $query->andFilterWhere([
                'user_id' => Yii::$app->user->id,
            ]);
        }

        //фильтр по дате поста, т.к. дата у нас в формате "time" в БД, а в форме лишь день-месяц-год, начинаем финтовать
        if(!empty($this->created_at) && $this->created_at!==0){

            $beginOfDay = strtotime("midnight", $this->created_at);
            $endOfDay   = strtotime("tomorrow", $beginOfDay) - 1;

            $query->andWhere(['<','created_at', $endOfDay]);
            $query->andWhere(['>','created_at', $beginOfDay]);
        }

        //filter by complete_time - complete_at
        if(!empty($this->complete_at) && $this->complete_at!==0){

            $beginOfDay = strtotime("midnight", $this->complete_at);
            $endOfDay   = strtotime("tomorrow", $beginOfDay) - 1;

            $query->andWhere(['<','complete_at', $endOfDay]);
            $query->andWhere(['>','complete_at', $beginOfDay]);
        }


        $query->andFilterWhere(['status' => $this->status]);

        $query->andFilterWhere(['like', 'desc', $this->desc]);

        return $dataProvider;
    }
}
