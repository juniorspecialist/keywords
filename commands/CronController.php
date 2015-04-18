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

            // business logic execution

            $this->log(true);

            $this->TaskStart();

            //sleep(260);

            $this->TaskEnd();

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

        //зафиксируем файл результата для дальнейшей записи
        if(file_exists(\Yii::getAlias('@app/runtime/'.$task->id.'.txt'))){
            unlink(\Yii::getAlias('@app/runtime/'.$task->id.'.txt'));
        }

        $this->result_file = \Yii::getAlias('@app/runtime/'.$task->id.'.txt');

        $elastic = new Bulk();

        $elastic->fileResult = \Yii::getAlias('@app/runtime/'.$task->id.'.txt');

        $elastic->createQuery($task);

        $elastic->resultToFile();

        unset($elastic->user_query);
    }

    /*
     * отправляем запрос в эластик
     * получаем результаты
     */



    /*
     * разрешаем запуск следующего задания по крону
     * обновляем в задании - файл результата и др. инфа по заданию
     */
    public function TaskEnd(){

        //обновим запись по заданию и укажем файл результата по ней

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