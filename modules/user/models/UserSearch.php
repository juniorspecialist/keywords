<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 19.04.15
 * Time: 22:08
 */

namespace app\modules\user\models;

use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\user\models\User;

class UserSearch extends User{


    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['status','balance'], 'integer'],
            [['status', 'email', 'username','balance'], 'safe'],
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
        $query = User::find();

        $dataProvider = new ActiveDataProvider([
            'query' => $query,
            'sort'=>false
        ]);

        $this->load($params);

        if (!$this->validate()) {

            // uncomment the following line if you do not want to any records when validation fails
            // $query->where('0=1');
            return $dataProvider;
        }

        $query->andFilterWhere(['status' => $this->status]);

        $query->andFilterWhere(['like', 'email', $this->email]);

        $query->andFilterWhere(['like', 'username', $this->username]);

        return $dataProvider;
    }
}