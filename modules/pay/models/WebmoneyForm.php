<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 07.05.15
 * Time: 14:01
 */

namespace app\models;

use Yii;
use yii\base\Model;


//https://webmoney.ua/files/InterfacesWM.pdf
//http://webmoney.ua/cooperation/developers
//http://webmoney.ua/cooperation/merchants#q1
//http://owebmoney.ru/merchant.shtml
//https://github.com/baibaratsky/php-webmoney
//https://github.com/pycmam/myPaymentPlugin
//https://github.com/baibaratsky/php-webmoney
//https://github.com/frost-nzcr4/webmoney
//https://github.com/search?l=PHP&q=privat&ref=searchresults&type=Repositories&utf8=%E2%9C%93

class WebmoneyForm extends Model{

    public $login;
    public $pass_first;
    public $pass_second;
    public $order_id;
    public $sum;
    public $InvoiceID;
    public $Description;

    /**
     * @return array the validation rules.
     */
    public function rules()
    {
        return [
            // name, email, subject and body are required
            [['name', 'email', 'subject', 'body'], 'required'],
            // email has to be a valid email address
            ['email', 'email'],
            // verifyCode needs to be entered correctly
            ['verifyCode', 'captcha'],
        ];
    }

    /**
     * @return array customized attribute labels
     */
    public function attributeLabels()
    {
        return [
            'Description' => 'Описание услуги',
            ''=>'',
            ''=>'',
            ''=>'',
            ''=>'',
            ''=>'',
            ''=>'',
            ''=>'',
            ''=>'',
        ];
    }

    /*
     * на основании параметров используем либо тестовый сервер либо рабочий для формирования сслок на оплату
     */
    public function getUrl(){

    }
}