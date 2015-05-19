<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 14.05.15
 * Time: 10:28
 */

namespace app\modules\ticket\models;


use Yii;
use yii\base\Model;
use yii\data\ActiveDataProvider;
use app\modules\ticket\models\TicketAnswer;

class TicketAnswerSearch extends TicketAnswer{
    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['id', 'user_id', 'status', 'created_at'], 'integer'],
            [['theme', 'question'], 'safe'],
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


}