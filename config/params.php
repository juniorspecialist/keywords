<?php

//установим путь к каталогу для хранения файлов результатов - заданий выборок
Yii::setAlias('@taskDirFile', dirname(__DIR__) . '/runtime/result_files/');
//тестовый коммент

return [
    'adminEmail' => '',
    'supportEmail' => '',
    'user.passwordResetTokenExpire' => 3600,
    'admins'=>['admin'],//список имен админов
    'task.cost'=>50,//стоимость одной проверки
    'elastic.index_list'=>['bulk','word'],
    'elastic.per_pages'=>10000,//по сколько результатов читать при запросе на выборку, для сохранения в файл
    //'task.path.result.files'=>\Yii::getAlias('@app/runtime/result_files/'),
    'task.count.time.interval'=>10,
    'count.keywords'=>1328134024,//общее кол-во ключевых слов

    'max.count.open.ticket'=>2,//максимальное кол-во открытых тикетов по юзеру
];
