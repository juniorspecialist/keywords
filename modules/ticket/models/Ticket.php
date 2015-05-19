<?php

namespace app\modules\ticket\models;

use Yii;
use app\modules\user\models\User;
use app\modules\ticket\models\TicketAnswer;

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
            [['user_id', 'status', 'created_at','updated_at','view_admin', 'view_user'], 'integer'],
            [['question'], 'string', 'min'=>5],
            [['theme'], 'string', 'max' => 250,'min'=>5],

            //проверка кол-ва открытых тикетов у юзера
            [['theme'], 'limitTicket'],

            //после добавления тикета выделим его для админа
            [['view_admin'], 'default', 'value'=>0],//админ не просмотрел,
            [['view_user'], 'default', 'value'=>1],//дя юзера он не новый тикет

            [['user_id'], 'default', 'value'=>Yii::$app->user->id],
            [['status'], 'default', 'value'=>self::STATUS_OPEN],
            [['created_at','updated_at'], 'default', 'value'=>time()],

            [['theme', 'question'], function ($attribute) {
                $this->$attribute = \yii\helpers\HtmlPurifier::process($this->$attribute);
            }],
        ];
    }

    /*
     * кол-во открытых тикетов по юзеру
     */
    public function limitTicket(){

        if(!$this->hasErrors()){
            $count = Yii::$app->db->createCommand('SELECT COUNT(id) as count FROM ticket WHERE user_id=:user_id AND status=:status')
                ->bindValues([':user_id'=>Yii::$app->user->id,':status'=>Ticket::STATUS_OPEN])
                ->queryScalar();
            if(Yii::$app->params['max.count.open.ticket']==$count){
                $this->addError('theme','У вас уже есть '.$count.' открытых тикета, чтобы открыть ещё, надо закрыть их.');
            }
        }

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
     * получамм список ответов по тикету с данными по юзерам
     */
//    public function getTicketAnswersWithUsers(){
//        return $this->hasMany(User::className(), ['id' => 'user_id'])->viaTable(TicketAnswer::tableName(),['ticket_id'=>'id']);
//        //return $this->hasMany(Category::className(), ['id' => 'category_id'])->viaTable(GameCategory::tableName(), ['game_id'=>'id']);
//    }

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
     * кол-во не просмотренных тикетов по юзеру(админ или юзер)
     */
    public function getNotviews(){
        //для админа
        if(Yii::$app->user->identity->isAdmin()){

        }else{

        }
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
