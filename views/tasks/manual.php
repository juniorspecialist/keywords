<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 15.04.15
 * Time: 10:42
 */
//инструкция по использования правил в выборке, показываем юзеру при добавления задания

use yii\bootstrap\Modal;

Modal::begin([
'header' => '<h2>Информация о синтаксисе ключевых слов</h2>',
'size'=>Modal::SIZE_LARGE,
'toggleButton' => false,
'id'=>'manualModal',
]);

echo 'Say hello...';


Modal::end();