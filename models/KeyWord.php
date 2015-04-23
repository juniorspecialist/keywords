<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 01.01.15
 * Time: 10:44
 */

namespace app\models;


class KeyWord  extends \yii\db\ActiveRecord{
    /**
     * @return array the list of attributes for this record
     */
    public function attributes()
    {
        // path mapping for '_id' is setup to field 'id'
        return ['id','word'];
    }

    public function rules()
    {
        return [
            // the name, email, subject and body attributes are required
            ['word', 'unique'],

            //[['word'], 'unique', 'targetClass' => KeyWord::className(), 'message' => 'Данное слово уже существует.'],
        ];

    }

    public static function tableName()
    {
        return 'keyword';
    }
} 