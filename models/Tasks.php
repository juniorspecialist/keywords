<?php

namespace app\models;

use app\modules\user\models\User;
use yii\behaviors\TimestampBehavior;
use Yii;
use yii\helpers\Html;

/**
 * This is the model class for table "tasks".
 *
 * @property integer $id
 * @property integer $created_at
 * @property integer $user_id
 * @property integer $status
 * @property integer $complete_at
 * @property string $words
 * @property string $file_result
 *
 * @property User $user
 */
class Tasks extends \yii\db\ActiveRecord
{

    const STATUS_ERROR = 0;//ошибка
    const STATUS_CREATE = 1;//создано
    const STATUS_COMPLETE = 2; //выполнено
    const STATUS_IN_PROGRESS = 3; //в процессе выполнения


    const YESNOT_YES = 1;
    const YESNOT_NO = 2;

    /*
     * switcher Yes or Not
     */
    public static function getYesNot(){
        return [
            self::YESNOT_YES => 'Да',
            self::YESNOT_NO => 'Нет'
        ];
    }


    public static function getStatusesArray()
    {
        return [
            self::STATUS_ERROR => 'Ошибка',
            self::STATUS_CREATE => 'Создано',
            self::STATUS_COMPLETE => 'Выполнено',
            self::STATUS_IN_PROGRESS => 'В процессе',
        ];
    }

    public function getStatusName()
    {
        $statuses = self::getStatusesArray();
        return isset($statuses[$this->status]) ? $statuses[$this->status] : '';
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%tasks}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['words'], 'required', 'on'=>['create','update','count']],

            [['created_at', 'user_id', 'status', 'complete_at',  'notify_on_complete'], 'integer'],

            [['words','stop_list'], 'string'],

            //validate input keywords from user
            [['words'], 'validateWords', 'on'=>['create','update','count']],

            ['words','checkBalanceUser', 'on'=>['create','update']],

            [['user_id'],'default', 'value' => Yii::$app->user->id],

            [['created_at'],'default', 'value' => time()],

            [['link'],'default', 'value' => uniqid(true)],

            [['status'],'default', 'value' => Tasks::STATUS_CREATE],

            [['file_result', 'desc', 'link'], 'string', 'max' => 255],

            [['words', 'stop_list', 'desc'], function ($attribute) {
                $this->$attribute = \yii\helpers\HtmlPurifier::process($this->$attribute);
            }, 'on'=>['create','update','count']],

            [['words'], 'validateElastic', 'on'=>['create','update','count']],
        ];
    }

    /*
     * валидация параметров запроса через эластик
     */
    public function validateElastic(){
        if(!$this->hasErrors()){
            $bulk = new Bulk();
            if(!$bulk->userQueryValidate($this)){
                $this->addError('words','Параметры задания составлены с ошибкой, проверьте параметры.');
            }
        }
    }

    public function behaviors()
    {
        return [
//          [
//              'class' => TimestampBehavior::className(),
//              'attributes'=>['created_at'],
//              'createdAtAttribute' => 'created_at',
//              //'value' => time(),
//          ],
        ];
    }

    public function getComplete(){
        if(empty($this->complete_at) || $this->complete_at==0){
            return '';
        }else{
            return date('Y-m-d H:i:s', $this->complete_at);
        }
    }

    public function getCreated(){
        if(empty($this->created_at) || $this->created_at==0){
            return '';
        }else{
            return date('Y-m-d H:i:s', $this->created_at);
        }
    }


    /*
     * проверим баланс юзера, хватает ли ему баланса для создания задания
     */
    public function checkBalanceUser(){

        //проверим на лимит созданных заданий на выборку - максимум - 3
        if(!$this->hasErrors()){

            $balance = User::getBalance();

            if($balance==0){

                $this->addError('words', 'Не достаточно средств на вашем балансе, для создания задания');

            }else{

                //кол-во созданных заданий, но не проверенных
                $count = TasksSearch::find()->andWhere(['status'=>self::STATUS_CREATE, 'user_id'=>Yii::$app->user->id]) ->count();

                //подсчитаем на сколько заданий у юзера хватает баланса
                $cost_earlier_tasks = (($count+1)*Yii::$app->params['task.cost']);//+1 - итоговая сумма к списанию за все ранее созданные задания+ текущее

                if($cost_earlier_tasks>$balance){
                    $this->addError('words','Извините, вам не хватает средств на балансе, пополните баланс');
                }
            }
        }
    }

    /*
     * валидация списка стоп-слов
     * не более 30ти слов+ длина слов не более 50 символов
     */
    public function validateStoplist(){
        if(!$this->hasErrors() && !Yii::$app->user->identity->isAdmin()){

            $stop_words = explode(PHP_EOL, $this->stop_list);

            //проверим на количество ключевых слов для выборки
            if(sizeof($stop_words)>30){
                $this->addError('stop_list', 'Количество слов-исключений не должно превышать 30');
            }

            if(!$this->hasErrors()){
                //проверим длину каждого ключевого слова, не должна превышать 80 символов
                foreach($stop_words as $word){
                    if(strlen($word)>50){
                        $this->addError('stop_list', 'Длина одного слова-исключения не должна превышать 50 символов. Слишком длинное слово - '.$word);
                        break;
                    }
                }
            }
        }
    }

    /*
     * проверим на ограничения список ключевых слов
     * не более 20 слов, длина каждого ключевика не более 80ти символов
     */
    public function validateWords(){

        //после изменения статус отличного от "Создано", запрещено получать данные по кол-ву выборок либо править
        if($this->status==self::STATUS_IN_PROGRESS || $this->status==self::STATUS_COMPLETE){
            $this->addError('words', 'Ваше задание обрабатывается либо уже выполнено');
        }

        if(!$this->hasErrors() && !Yii::$app->user->identity->isAdmin()){

            $keywords = explode(PHP_EOL, $this->words);

            //проверим на количество ключевых слов для выборки
            if(sizeof($keywords)>20){
                $this->addError('words', 'Количество ключевых слов не должно превышать 20');
            }

            if(!$this->hasErrors()){
                //проверим длину каждого ключевого слова, не должна превышать 80 символов
                foreach($keywords as $word){

                    //длина ключевика менее 4х символов
                    if(strlen($word)<4){
                        $this->addError('words', 'Длина одного ключевого слова не должна быть менее 4х символов. Слишком короткое слово - '.$word);
                        break;
                    }

                    //длина не более 80 символов
                    if(strlen($word)>80){
                        $this->addError('words', 'Длина одного ключевого слова не должна превышать 80 символов. Слишком длинное слово - '.$word);
                        break;
                    }
                }
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
            'created_at' => 'Создан',
            'user_id' => ' ID пользователя',
            'status' => 'Статус',
            'complete_at' => 'Завершено',
            'words' => 'Ключевые слова',
            'file_result' => 'Путь к файлу результатов',
            'desc'=>'Комментарий',
            'stop_list'=>'Стоп слова',
            'notify_on_complete'=>'Уведомить меня о завершении(на почту)',
            'link'=>'Идентификатор',//уникальный идентификатор задания, используем для ссылки, вместо ID
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }

    /*
     * формирууем запрос на основании данных с формы и ВАЛИДИРУЕМ в ЭЛАСТИКЕ его
     */
    public function validateTask(){
        //обработка параметров документа и отправка для валидации
        //http://www.elastic.co/guide/en/elasticsearch/guide/master/_validating_queries.html

    }

    /*
     * установим новый статус здя задания
     */
    public static function setStatus($task_id, $status){
        Yii::$app->db->createCommand('UPDATE '.Tasks::tableName().' SET status=:status WHERE id=:id')
            ->bindValues([':id'=>$task_id, ':status'=>$status])
            ->execute();
    }

    public function getUrl($action='view'){
        if($action=='view'){
            return Yii::$app->urlManager->createAbsoluteUrl(['/tasks/view/', 'link'=>$this->link]);
        }
        if($action=='update'){
            return Yii::$app->urlManager->createAbsoluteUrl(['/tasks/update/', 'link'=>$this->link]);
        }
        if($action=='delete'){
            return Yii::$app->urlManager->createAbsoluteUrl(['/tasks/delete/', 'link'=>$this->link]);
        }

    }

    /*
     * ссылка на скачивание результата выполнения задания - выборки
     */
    public function getDownloadLink(){
        return Html::a(
            'Скачать файл результата',
            Yii::$app->urlManager->createAbsoluteUrl(['/tasks/download-result/','file'=>$this->link]),
            ['target'=>'_blank']
        );
    }

}
