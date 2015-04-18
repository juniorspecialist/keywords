<?php

return [
    'adminEmail' => '',
    'supportEmail' => '',
    'user.passwordResetTokenExpire' => 3600,
    'admins'=>['admin'],//список имен админов
    'task.cost'=>50,//стоимость одной проверки
    'elastic.index_list'=>['bulk','word'],
    'elastic.per_pages'=>10000,//по сколько результатов читать при запросе на выборку, для сохранения в файл
];
