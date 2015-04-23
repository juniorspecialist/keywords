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
            [['user_id', 'type_operation', 'sum_operation', 'balance_user_after_operation'], 'required'],
            [['user_id', 'type_operation', 'sum_operation', 'balance_user_after_operation', 'created_at'], 'integer'],
            ['created_at', 'default', 'value'=>time()],
            ['desc', 'string'],
        ];
    }


    public function getType(){

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
