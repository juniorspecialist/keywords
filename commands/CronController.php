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
use app\modules\user\models\User;
use yii\console\Controller;
use yii\console\Exception;
use yii\helpers\Console;
use app\models\Financy;

class CronController extends Controller{


    public $tasks_mutex_name = 'mutex_tasks';//ID файла блокировки для MUTEX
    public $result_file;//файл результата, куда запишим результат выборки из эластика
    public $task;//модель задания которое мы выполняем

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


            //находим первые в списке очереди задачи по выборке
            $this->task = $this->findTask();

            //обернём в транзакцию все действия
            $transaction = \Yii::$app->db->beginTransaction();

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

        //удалим файл результата, если он существует
        if(file_exists(\Yii::getAlias('@taskDirFile').'/'.$this->task->link.'.txt')){
            unlink((\Yii::getAlias('@taskDirFile').'/'.$this->task->link.'.txt'));
        }


        //формируем запрос к эластику на выборку данных
        $elastic = new Bulk();

        $elastic->fileResult = \Yii::getAlias('@taskDirFile').'/'.$this->task->link.'.txt';

        $elastic->createQuery($this->task);

        //$elastic->user_query->fields(['word']);

        //$elastic->resultToFile();
        //получаем данные порциями, типа через Итератор-эластика и пишим в файл
        $elastic->scrollScan();

        unset($elastic->user_query);
    }


    /*
     * разрешаем запуск следующего задания по крону
     * обновляем в задании - файл результата и др. инфа по заданию
     */
    public function TaskEnd(){

        //обновим запись по заданию и укажем файл результата по ней
        $sql = 'UPDATE '.Tasks::tableName().' SET status=:status, file_result=:file, complete_at=:complete_at WHERE id=:id';

        $query = \Yii::$app->db->createCommand($sql);

        //спишим с баланса юзера сумма за выборку
        $query->bindValues([':status'=>Tasks::STATUS_COMPLETE,':id'=>$this->task->id,':file'=>$this->task->link.'.txt', ':complete_at'=>time()]);

        $query->execute();

        //запишим операцию списания денег в лог фин. операций
        $financy = new Financy();
        $financy->sum_operation = \Yii::$app->params['task.cost'];
        $financy->type_operation = Financy::TYPE_OPERATION_MINUS;
        $financy->user_id = $this->task->user_id;
        $financy->balance_user_after_operation = (int)($this->task->user->balance - \Yii::$app->params['task.cost']);

        if($financy->validate()){

            $financy->save();

            //обновим баланс юзера
            User::minusBalance($financy->user_id);

        }else{

            print_r($financy->errors).PHP_EOL;

            $this->log('error');
        }

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

        $query->bindValues([':status'=>Tasks::STATUS_ERROR,':id'=>$this->task->id,':complete_at'=>time()]);

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

    /*
     * читаем текстовый файлик и пишим данные в эластик
     */
    public function actionWrite(){
        //
        //$this->readfile('12.txt'); sleep(5);
        //$this->readfile('13.txt'); sleep(5);
        //$this->readfile('14.txt'); sleep(5);
        //$this->readfile('15.txt'); sleep(5);

        //$this->readfile('16.txt'); sleep(5);
        //$this->readfile('17.txt'); sleep(5);
        //$this->readfile('18.txt'); sleep(5);
        //$this->readfile('19.txt'); sleep(5);
        //$this->readfile('22_.txt'); sleep(5);
        //$this->readfile('21_.txt'); sleep(5);
        //$this->readfile('22_.txt'); sleep(5);
        //$this->readfile('23_.txt'); sleep(5);
        //$this->readfile('24_.txt'); sleep(5);

        //$this->readfile('28_.txt'); sleep(5);
        //$this->readfile('29_.txt'); sleep(5);

        //$this->readfile('38_.txt'); sleep(5);
        $this->readfile('39_.txt'); sleep(5);
        $this->readfile('40_.txt'); sleep(5);
        $this->readfile('41_.txt'); sleep(5);
        $this->readfile('42_.txt'); sleep(5);
        $this->readfile('43_.txt'); sleep(5);
        $this->readfile('44_.txt'); sleep(5);
        $this->readfile('45_.txt'); sleep(5);
        $this->readfile('46_.txt'); sleep(5);
        $this->readfile('47_.txt'); sleep(5);
        $this->readfile('48_.txt'); sleep(5);
        $this->readfile('49_.txt'); sleep(5);
        $this->readfile('50_.txt'); sleep(5);
    }

    public function readfile($name){
        $data_send = '';
        $handle = fopen('/var/www/keywords/web/'.$name, "r") or die("Couldn't get handle");
        if ($handle){
            $bulk = new \app\models\Bulk();
            $i=1;
            while (!feof($handle)) {
                $i++;
                
                //if($skip){
                    //if($i<4267782570){continue;}
                //}
                $buffer = fgets($handle, 4096);
                $data_send.=json_encode(['index'=>array('_id' => md5($buffer))]). "\n".json_encode(['word'=>$buffer]). "\n";
                if($i%300==0){
                    $bulk->multiCreate($data_send);
                    unset($data_send);
                    $data_send = '';
                }
            }
            fclose($handle);
            $bulk->multiCreate($data_send);
            unset($handle);
            unset($bulk);
            @unlink('/var/www/keywords/web/'.$name);
            $this->stdout($name.PHP_EOL, Console::FG_GREEN, Console::BOLD);
        }
    }
}