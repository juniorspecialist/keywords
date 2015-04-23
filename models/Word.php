<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 02.01.15
 * Time: 18:58
 */

namespace app\models;


class Word extends \yii\elasticsearch\ActiveRecord{
    /**
     * @return array the list of attributes for this record
     */
    public function attributes()
    {
        // path mapping for '_id' is setup to field 'id'
        return ['word'];
    }
} 