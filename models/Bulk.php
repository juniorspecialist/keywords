<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 16.01.15
 * Time: 10:39
 */

namespace app\models;


use Yii;
use yii\base\ErrorException;
use yii\helpers\ArrayHelper;
use yii\helpers\Json;

class Bulk extends \yii\elasticsearch\ActiveRecord{

    public $user_query;//запрос пользователя на основании его параметров из задания

    public $fileResult;//файл результата куда пишим итоговые данные по заданию

    public $scroll_id;//ID запроса, по которому получаем результат выборки, отправленной ранее через scroll/scan
    public $scroll_total;//общее кол-во найденное значений при scroll/scan
    public $scroll_write = 0;//кол-во значений записанное в файл, при croll/scan

    /**
     * @return array the list of attributes for this record
     */
    public function attributes()
    {
        // path mapping for '_id' is setup to field 'id'
        return ['word'];
    }

    /**
     * @return string the name of the index this record is stored in.
     */
//    public static function index()
//    {
//        return '_all';//'_all';
//    }

    /**
     * @return string the name of the type this record is stored in.
     */
//    public static function type()
//    {
//        return ['bulk','word'];
//    }


    /*
     * добавляем нескольоо записей за один запрос
     */
    public function multiCreate($data){

        // TODO do this via command
        $url = [static::index(), static::type(), '_bulk'];
        $response = static::getDb()->post($url, [], $data);
        unset($data);
        //echo '<pre>'; print_r($response);die();
        unset($response);
//        $n = 0;
//        $errors = [];
//        foreach ($response['items'] as $item) {
//            if (isset($item['create']['status']) && $item['create']['status'] == 200) {
//                if (isset($item['create']['found']) && $item['create']['found']) {
//                    $n++;
//                }
//            } else {
//                $errors[] = $item['create'];
//            }
//        }
//        if (!empty($errors) || isset($response['errors']) && $response['errors']) {
//            echo '<pre>';
//            print_r( $errors);
//            //die(__METHOD__ . ' failed creating records.');
//        }

        //return $n;

    }

    /*
     * формируем запрос для отправки
     * $model - модель Tasks
     */
    public function createQuery($model){

        $find_words = [];

        //список слов для выборки
        if($model->words){
            foreach(explode(PHP_EOL,$model->words) as $word_rule){
                $find_words[] = ['regexp'=>['word'=>$word_rule]];
            }
        }

        $this->user_query = self::find();//->fields(['word'])

        //$this->user_query->search()
        //не получаем "_source" - данные, не нужны
        //$this->user_query->source = null;

        $json['bool'] = ['should'=>$find_words];

        if($model->stop_list){

            $json['bool'] = ArrayHelper::merge($json['bool'],['must_not'=>['terms'=>['word'=>explode(PHP_EOL,$model->stop_list)]]]);
        }

        $this->user_query->query($json);

        unset($find_words);
        unset($json);
        unset($model);
    }

    /*
     * определяем общее кол-во найденных данных
     * в цикле получаем данные порциями и пишим их в файл
     */
    public function resultToFile(){

        //получаем общее кол-во значений
        $total = $this->userQueryCount();

        $count_pages = round($total/Yii::$app->params['elastic.per_pages']);

        $this->user_query->limit(Yii::$app->params['elastic.per_pages']);
        $this->user_query->fields(['word']);

        for($i=0;$i<$count_pages;$i++){

            $this->writeFile($this->user_query->offset($i*Yii::$app->params['elastic.per_pages'])->asArray()->all());
        }
    }

    /*
     * на основании ранее созданного запроса
     * отправляем запрос на получении кол-ва результатов
     */
    public function userQueryCount(){

        if($this->user_query){
            return ($this->user_query->count());
        }else{
            throw new \yii\web\HttpException(400, 'Запрашиваемый user_query в Elastic пустой, необходимо указать.');
        }

    }

    /*
     * формируем запрос на валидацию данных для запроса через эластик
     */
    public function userQueryValidate($model){

        /*
         * http://www.elastic.co/guide/en/elasticsearch/guide/master/_validating_queries.html
         * http://www.elastic.co/guide/en/elasticsearch/reference/current/search-validate.html
         * http://www.elastic.co/guide/en/elasticsearch/reference/1.4/search-request-scroll.html#scroll-scan
         * https://github.com/elastic/elasticsearch/issues/707
         * http://people.mozilla.org/~wkahngreene/elastic/guide/reference/api/search/search-type.html
         * http://www.elastic.co/guide/en/elasticsearch/reference/current/search-request-scroll.html
         * http://elasticsearch.qiniudn.com/guide/en/elasticsearch/guide/current/scan-scroll.html
         * http://www.elastic.co/guide/en/elasticsearch/reference/current/search-request-scroll.html#scroll-scan
         * http://www.elastic.co/guide/en/elasticsearch/reference/current/search-validate.html
         *
         */

        $this->createQuery($model);

        $this->user_query->limit = null;

        $url = [static::index(), static::type(), '_validate','query'];

        $command = $this->user_query->createCommand();

        $query = $command->queryParts;
        if (empty($query)) {
            $query = '{}';
        }
        if (is_array($query)) {
            $query = Json::encode($query);
        }

        //спец. параметры для валидации
        $options = ['_search'];
        $options['explain'] = 1;

        $response = static::getDb()->get($url, $options, $query);

        return $response['valid'];
    }


    /*
     * пишим в файл данные полученные из Эластика
     * $data - скорее всего будет массив
     */
    public function writeFile($data){

        if($data){

            foreach($data as $j=>$keyword){

                file_put_contents($this->fileResult, $keyword['fields']['word'][0], FILE_APPEND);

                unset($data[$j]);
            }

            unset($data);
        }
    }

    /*
     * scroll/scan http://www.elasticsearch.org/guide/en/elasticsearch/reference/current/search-request-search-type.html#scan
     */
    public function scrollScan(){
        //сперва отправляем запрос на формирование данных:по сколько данных получаем, сам запрос выборки+д.р. параметры
        // в ответ получаем ID, по которому будет получать результаты выборки
        //пример адреса отправки запроса - localhost:9200/twitter/tweet/_search?scroll=1m&search_type=scan

        if($this->user_query){

            $this->user_query->fields(['word']);

            $url = [static::index(), static::type(), '_search'];

            $command = $this->user_query->createCommand();

            $query = $command->queryParts;
            if (empty($query)) {
                $query = '{}';
            }
            if (is_array($query)) {
                $query = Json::encode($query);
            }

            //спец. параметры для валидации
            $options['scroll'] = '60s';//спустя 1минуту удаляем найденные данные
            $options['search_type'] = 'scan';
            //$options['size']= 100;

            $response = static::getDb()->get($url, $options, $query);


            $this->scroll_id = $response['_scroll_id'];

            //общее кол-во всех значений
            $this->scroll_total = $response['hits']['total'];

            //echo 'total='.$this->scroll_total.PHP_EOL;

            unset($response);

            //получем массив данных  - РЕЗУЛЬТАТОВ выборки

            $this->scrollScanIteration();

//            echo 'write_file='.$this->scroll_write.PHP_EOL;
//            echo 'memory_get_peak_usage='.memory_get_peak_usage(true);
        }
    }

    /*
     * блоками/кусками получаем результаты ранее отправленного запроса на выборку
     * примерно адреса, для получения данных - http://10.0.2.15:9200/_search/scroll?scroll=10s&scroll_id=c2Nhb[...]zk7
     */
    public function scrollScanIteration(){

        //echo 'scrol_id='.$this->scroll_id.PHP_EOL;

        $url = ['_search','scroll'];

        //спец. параметры для валидации
        $options['scroll'] = '60s';//спустя 1минуту удаляем найденные данные
        $options['scroll_id'] = $this->scroll_id;

        $response = static::getDb()->get($url, $options, null);

        $this->scroll_id = $response['_scroll_id'];

        $continue = false;

        if(!empty($response['hits']['hits'])){

            $continue = true;

            foreach($response['hits']['hits'] as $j=>$keyword){

                file_put_contents($this->fileResult, $keyword['fields']['word'][0], FILE_APPEND);

                //$this->scroll_write++;

                unset($response['hits']['hits'][$j]);
            }
            unset($response);
        }

        if($continue==true){
            //отправляем запрос на получение следующих данных
            $this->scrollScanIteration();
        }
    }

}