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
            [['user_id', 'status', 'created_at','updated_at'], 'integer'],
            [['question'], 'string'],
            [['theme'], 'string', 'max' => 250],

            [['user_id'], 'default', 'value'=>Yii::$app->user->id],
            [['status'], 'default', 'value'=>self::STATUS_OPEN],
            [['created_at','updated_at'], 'default', 'value'=>time()],
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
            'updated_at'=>'Обновлен'
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

    /*
     * меняем статус тикета
     * после изменения статус тикета - отправляем уведомление другому участнику тикета на почту с ссылкой
     */
    public function setStatusTicket($new_status){

        //определяем роль пользователя
        if(Yii::$app->user->identity && Yii::$app->user->identity->isAdmin()){//ADMIN
            $email_to = $this->user->email;
            $email_from = Yii::$app->params['adminEmail'];
            $name_from = $this->user->username;
            $user = $this->user;;
        }else{//user
            $email_to = Yii::$app->params['adminEmail'];
            $email_from = $this->user->email;
            $name_from = 'Admin';
            $user = User::findOne(['id'=>Yii::$app->user->id]);
        }

        Yii::$app->mailer->compose('changeTicketStatus', ['ticket' => $this, 'user'=>$user])
            ->setFrom([$email_from => Yii::$app->name])
            ->setTo($this->email)
            ->setSubject('Изменился статус тикета  №' . $this->id)
            ->send();

        /*Yii::$app->db->createCommand('UPDATE '.Ticket::tableName(). ' SET status=:status, id=:id, updated_at=:updated_at')
            ->bindValues([':status'=>$new_status, ':id'=>$this->id,':updated_at'=>time()])
            ->execute();
        */
    }
}
