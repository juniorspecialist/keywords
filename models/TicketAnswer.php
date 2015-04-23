<?php

namespace app\models;

use Yii;

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
            [['user_id', 'aswer', 'created_at', 'ticket_id'], 'required'],
            [['user_id', 'created_at', 'ticket_id'], 'integer'],
            [['aswer'], 'string']
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user_id' => 'User ID',
            'aswer' => 'Aswer',
            'created_at' => 'Created At',
            'ticket_id' => 'Ticket ID',
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
