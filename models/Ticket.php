<?php

namespace app\models;

use Yii;
use app\modules\user\models\User;
use app\models\TicketAnswer;

/**
 * This is the model class for table "ticket".
 *
 * @property integer $id
 * @property string $theme
 * @property integer $user_id
 * @property integer $status
 * @property integer $created_at
 * @property string $question
 *
 * @property User $user
 * @property TicketAnswer[] $ticketAnswers
 */
class Ticket extends \yii\db\ActiveRecord
{

    const STATUS_OPEN = 1;
    const STATUS_CLOSE = 2;//

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ticket';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['theme', 'question'], 'required'],
            [['user_id', 'status', 'created_at'], 'integer'],
            [['question'], 'string'],
            [['theme'], 'string', 'max' => 250],

            [['user_id'], 'default', 'value'=>Yii::$app->user->id],
            [['status'], 'default', 'value'=>self::STATUS_OPEN],
            [['created_at'], 'default', 'value'=>time()],
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'theme' => 'Тема',
            'user_id' => 'User ID',
            'user'=>'Пользователь',
            'status' => 'Статус',
            'created_at' => 'Создан',
            'question' => 'Вопрос',
            'ticketanswers'=>'Ответы на тикет',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicketAnswers()
    {
        return $this->hasMany(TicketAnswer::className(), ['ticket_id' => 'id']);
    }

    /*
     * определяем статус тикета
     */
    public function getStatusName(){

        $statuses = Ticket::getStatuses();

        return $statuses[$this->status];
    }

    public static function getStatuses(){
        return [
            self::STATUS_OPEN=>'Открыт',
            self::STATUS_CLOSE=>'Закрыт',
        ];
    }
}
