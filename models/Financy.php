<?php

namespace app\models;

use Yii;
use app\modules\user\models\User;

/**
 * This is the model class for table "{{%financy}}".
 *
 * @property integer $id
 * @property integer $user_id
 * @property integer $type_operation
 * @property integer $sum_operation
 * @property integer $balance_user_after_operation
 * @property integer $created_at
 *
 * @property User $user
 */
class Financy extends \yii\db\ActiveRecord
{

    const TYPE_OPERATION_MINUS = 1;//списание баланса
    const TYPE_OPERATION_PLUS = 2;//пополнение баланса

    //статусы заявки на пополнение
    const STATUS_NOT_PAID = 1;//заявка на пополение не оплачена
    const STATUS_PAID = 2;//заявка на пополение оплачена
    const STATUS_PAID_FAIL = 3;//заявка на оплату отменена

    //тип системы пополнения
    const TYPE_PAY_SYSTEM_ROBOKASSA = 1;//робокасса
    const TYPE_PAY_SYSTEM_WEBMONEY = 2;//вэбмани


    //текстовое название типа выбранного пополнения
    public function getTypePaySystemName(){
        $list = self::getTypeSystemList();
        return $list[$this->type_pay_system];
    }

    //список всех возможных вариантов пополнения баланса
    public function getTypeSystemList(){
        return [
            self::TYPE_PAY_SYSTEM_WEBMONEY=>'WebMoney',
            self::TYPE_PAY_SYSTEM_ROBOKASSA=>'Робокасса',
        ];
    }

    /**
     * @inheritdoc
     */
    public static function tableName()
    {
        return '{{%financy}}';
    }

    /**
     * @inheritdoc
     */
    public function rules()
    {
        return [
            [['sum_operation'], 'required'],
            [['user_id', 'type_operation', 'sum_operation', 'created_at', 'status', 'type_pay_system'], 'integer'],
            ['created_at', 'default', 'value'=>time()],
            ['status', 'default', 'value'=>self::STATUS_NOT_PAID],
            ['user_id', 'default', 'value'=>Yii::$app->user->id],
            ['desc', 'string'],
        ];
    }


    public function getTypeOperation(){

        $list = Financy::getTypeOperations();

        return $list[$this->type_operation];
    }

    /*
     * список типов фин. операций
     */

    public static function getTypeOperations(){
        return [
            self::TYPE_OPERATION_MINUS=>'Списание',
            self::TYPE_OPERATION_PLUS=>'Пополнение',
        ];
    }

    /**
     * @inheritdoc
     */
    public function attributeLabels()
    {
        return [
            'id' => 'ID',
            'user' => 'Пользователь',
            'type_operation' => 'Тип операции',
            'sum_operation' => 'Сумма',
            'balance_user_after_operation' => 'Баланс после операции',
            'created_at' => 'Дата операции',
            'desc'=>'Комментарий',
        ];
    }

    /**
     * @return \yii\db\ActiveQuery
     */
    public function getUser()
    {
        return $this->hasOne(User::className(), ['id' => 'user_id']);
    }
}
