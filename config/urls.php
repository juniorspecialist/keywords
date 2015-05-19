<?php
/**
 * Created by PhpStorm.
 * User: alex
 * Date: 07.05.15
 * Time: 8:40
 */

return [

    '' => 'site/index',
    'about'=>'site/about',
    'contact'=>'site/contact',

    '<_a:(login|logout|signup|confirm-email|request-password-reset|reset-password|change-password|profil|captcha)>' => 'user/default/<_a>',

    'ticket'=>'ticket/default/index',
    [
        'pattern' => 'ticket/<id:\d+>',
        'route' => 'ticket/default/view',
        'suffix' => ''
    ],
    'ticket/<_a>' => 'ticket/default/<_a>',


    'tasks'=>'tasks/index',
    [
        'pattern' => 'tasks/<link:\w+>',
        'route' => 'tasks/view',
        'suffix' => ''
    ],

    'financy'=>'financy/index',
    [
        'pattern' => 'tasks/<link:\w+>',
        'route' => 'tasks/view',
        'suffix' => ''
    ],


    '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
    '<controller:\w+>/<action:\w+>/<link:\w+>' => '<controller>/<action>',
    '<controller:\w+>/<action:\w+>/<file:\w+>' => '<controller>/<action>',
    '<controller:\w+>/<action:\w+>/<id:\d+>' => '<controller>/<action>',
    '<controller:\w+>/<action:\w+>' => '<controller>/<action>',

    //'<controller:(ticket)>/<_a>'=>'ticket/default/<_a>',


    [
        'pattern' => '<controller>/<action>/<id:\d+>',
        'route' => '<controller>/<action>',
        'suffix' => ''
    ],
    [
        'pattern' => '<controller>/<action>',
        'route' => '<controller>/<action>',
        'suffix' => ''
    ],
    [
        'pattern' => '<module>/<controller>/<action>/<id:\d+>',
        'route' => '<module>/<controller>/<action>',
        'suffix' => ''
    ],
    [
        'pattern' => '<module>/<controller>/<action>',
        'route' => '<module>/<controller>/<action>',
        'suffix' => ''
    ],
    //'ticket'=>'ticket/index',

    //'ticket/view/<id:\d+>' => 'ticket/view',

    //'<_a:(login|logout|signup|confirm-email|request-password-reset|reset-password|change-password|profil|captcha)>' => 'pay/default/<_a>',

//    [
//        'pattern' => '<module>/<controller>/<action>/<id:\d+>',
//        'route' => '<module>/<controller>/<action>',
//        'suffix' => ''
//    ],
//    [
//        'pattern' => '<module>/<controller>/<action>',
//        'route' => '<module>/<controller>/<action>',
//        //'suffix' => '.html'
//    ],

//    '<module:\w+>/<controller:\w+>/<action:\w+>/<id:\d+>' => '<module>/<controller>/<action>',
//    '<module:\w+>/<controller:\w+>/<action:\w+>' => '<module>/<controller>/<action>',
//    '<module:\w+>/<controller:\w+>' => '<module>/<controller>/index',

    //'<module:\w+>/<_a:(index|create|close|view)>' => 'ticket/default/<_a>',


//    '<controller:\w+>/page/<page:\d+>' => '<controller>/',
//
//
//

//
//    '<controller:\w+>/'=>'<controller>/index',
//
//    '<module:\w+>/<controller:\w+>/<action:\w+>'=>'<module>/<controller>/<action>',
//
//    '<module:\w+>/<controller:\w+>'=>'<module>/<controller>/index',
];