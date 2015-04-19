<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 14.04.15
 * Time: 20:26
 */

namespace app\commands;
use app\models\Bulk;
use app\models\KeyWord;
use app\models\Word;
use app\models\Tasks;
use yii\console\Controller;
use yii\console\Exception;
use yii\helpers\Console;

class CronController extends Controller{


    public $tasks_mutex_name = 'mutex_tasks';//ID файла блокировки для MUTEX
    public $result_file;//файл результата, куда запишим результат выборки из эластика
    public $task_id;//ID задания которое мы выполняем

    /*
     * find task when user create for selecting
     */
    private function findTask(){

        $task = Tasks::find()->where(['status'=>Tasks::STATUS_CREATE])->orderBy('created_at ASC')->one();

        if(!$task){
            throw new Exception('Task not found');
        }else{
            return $task;
        }
    }

    /*
     * запускаем очередь выполнений заданий на выборку
     * поочередно берём созданные юзерами задания на выборку и отправляем запросы на сервер эластика
     */
    public function actionTasks(){

        if (\Yii::$app->get('mutex')->acquire($this->tasks_mutex_name)) {

            //обернём в транзакцию все действия
            $transaction = \Yii::$app->db->beginTransaction();

            //укажим имя файла результата
            $this->result_file = md5(time()).'.txt';

            try {

                // business logic execution
                $this->log(true);

                $this->TaskStart();

                $this->TaskEnd();

                $transaction->commit();

            } catch (Exception $e) {

                $this->TaskError();

                $transaction->rollBack();
            }

        } else {

            $this->log(false);

            // execution is blocked!
        }
    }

    /*
     * отправляем запрос в эластик на получение данных с выборки
     * формируем файл с данные - результатами выборки
     */
    public function TaskStart(){
        /*
         * ссылки на доки эластика для запросов
         * http://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-filtered-query.html
         * http://stackoverflow.com/questions/28001632/filter-items-which-array-contains-any-of-given-values
         * https://www.elastic.co/blog/quick-tips-regex-filter-buckets
         * http://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-regexp-query.html
         * http://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-regexp-query.html#regexp-syntax
         * http://www.elastic.co/guide/en/elasticsearch/reference/1.4/query-dsl-common-terms-query.html
         * https://www.elastic.co/blog/stop-stopping-stop-words-a-look-at-common-terms-query/
         * http://www.elastic.co/guide/en/elasticsearch/guide/current/_more_complicated_searches.html
         * http://www.elastic.co/guide/en/elasticsearch/guide/current/_full_text_search.html
         * http://www.elastic.co/guide/en/elasticsearch/reference/current/query-dsl-filtered-query.html
         */

        //находим первые в списке очереди задачи по выборке
        $task = $this->findTask();

        $this->task_id = $task->id;

        //удалим файл результата, если он существует
        if(file_exists(\Yii::getAlias('@taskDirFile').$this->result_file)){
            unlink((\Yii::getAlias('@taskDirFile').$this->result_file));
        }

        $elastic = new Bulk();

        $elastic->fileResult = \Yii::getAlias('@taskDirFile').$this->result_file;

        $elastic->createQuery($task);

        $elastic->resultToFile();

        unset($elastic->user_query);
    }


    /*
     * разрешаем запуск следующего задания по крону
     * обновляем в задании - файл результата и др. инфа по заданию
     */
    public function TaskEnd(){

        //обновим запись по заданию и укажем файл результата по ней
        $query = \Yii::$app->db->createCommand('UPDATE '.Tasks::tableName().' SET status=:status, file_result=:file,complete_at=:complete_at WHERE id=:id');

        $query->bindValues([':status'=>Tasks::STATUS_COMPLETE,':id'=>$this->task_id,':file'=>$this->result_file, ':complete_at'=>time()]);

        $query->execute();

        \Yii::$app->get('mutex')->release($this->tasks_mutex_name);
    }

    /*
     * в ходе выполнения задания произошла ошибка, запишим в лог+ откатимся назад
     */
    public function TaskError(){

        //запишим ошибку
        $this->log('error');

        //укажим, что задание на обработку выполнилось с ошибкой
        $query = \Yii::$app->db->createCommand('UPDATE '.Tasks::tableName().' SET status=:status, complete_at=:complete_at WHERE id=:id');

        $query->bindValues([':status'=>Tasks::STATUS_,':id'=>$this->task_id,':complete_at'=>time()]);

        $query->execute();


        //осовбодим очередь для след. задания
        \Yii::$app->get('mutex')->release($this->tasks_mutex_name);
    }

    /**
     * @param bool $success
     */
    private function log($success)
    {
        if ($success) {
            $this->stdout('Success!', Console::FG_GREEN, Console::BOLD);
        } else {
            $this->stderr('Error!', Console::FG_RED, Console::BOLD);
        }
        echo PHP_EOL;
    }
}