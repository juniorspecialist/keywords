<?php

namespace app\modules\ticket\models;

use Yii;
use \app\modules\user\models\User;

/**
 * This is the model class for table "ticket_answer".
 *
 * @property integer $id
 * @property integer $user_id
 * @property string $aswer
 * @property integer $created_at
 * @property integer $ticket_id
 *
 * @property Ticket $ticket
 * @property User $user
 */
class TicketAnswer extends \yii\db\ActiveRecord
{
    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return 'ticket_answer';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            //значения по умолчанию
            [['created_at'], 'default', 'value'=>time()],//
            [['user_id'], 'default', 'value'=>Yii::$app->user->id],//

            [['user_id', 'answer', 'created_at', 'ticket_id'], 'required'],
            [['user_id', 'created_at', 'ticket_id'], 'integer'],
            [['answer'], 'string'],

            [['answer'], function ($attribute) {
                $this->$attribute = \yii\helpers\HtmlPurifier::process($this->$attribute);
            }],

        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'Пользователь',
            'answer' => 'Текст ответа',
            'created_at' => 'Добавлено',
            'ticket_id' => 'Тикет',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getTicket()
    {
        return $this->hasOne(Ticket::className(), ['id' => 'ticket_id']);
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
