<?php

$params = require(__DIR__ . '/params.php');


$config = [

    'id' => 'basic',

    'basePath' => dirname(__DIR__),

    'bootstrap' => ['log','debug'],

    'name'=>'MYKEYWORDS.RU',

    'timeZone'=>'Europe/Moscow',

    'language' => 'ru',

    'sourceLanguage' => 'ru',

    'modules' => [

        'debug' => [
            'class' => 'yii\\debug\\Module',
            'panels' => [
                'elasticsearch' => [
                    'class' => 'yii\\elasticsearch\\DebugPanel',
                ],
            ],
        ],

        'gii' => [
            'class' => 'yii\gii\Module',
            'allowedIPs' => ['127.0.0.1', '::1', '192.168.0.*', '192.168.178.20'] // adjust this to your needs
        ],

        'user' => [
            'class' => 'app\modules\user\Module',
        ],
    ],

    'components' => [

        'user' => [
            'identityClass' => 'app\modules\user\models\User',
            'enableAutoLogin' => true,
            'loginUrl' => ['user/default/login'],
            //'admins'=>['admin'],
        ],

        'elasticsearch' => [
            'class' => 'yii\elasticsearch\Connection',
            'nodes' => [
                ['http_address' => '127.0.0.1:9200'],
                // configure more hosts if you have a cluster
            ],
        ],

        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            //'enableStrictParsing' => true,
            'rules' => [

                '' => 'site/index',
                'about'=>'site/about',
                'contact'=>'site/contact',

                '<_a:(login|logout|signup|confirm-email|request-password-reset|reset-password|change-password|profil)>' => 'user/default/<_a>',

                '<controller:\w+>/page/<page:\d+>' => '<controller>/index',
                '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
                '<controller:\w+>/'=>'<controller>/index',
            ],
        ],

        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'zyc_YXMGAoy9bKAGy26haX5g2WAYB8kJ',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mail' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => YII_DEBUG,
//            'messageConfig' => [
//                'from' => 'noreply@yoursite.com',
//            ],
//            'transport' => [
//                'class' => 'Swift_SmtpTransport',
//                'host' => 'smtp.yandex.ru',
//                'username' => 'sample@yandex.ru',
//                'password' => '*****',
//                'port' => '587',
//                'encryption' => 'tls',
//            ],
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning'],
                ],
            ],
        ],
        'db' => require(__DIR__ . '/db.php'),
    ],
    'params' => $params,
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = 'yii\debug\Module';

    $config['bootstrap'][] = 'gii';
    $config['modules']['gii'] = 'yii\gii\Module';
}

return $config;
