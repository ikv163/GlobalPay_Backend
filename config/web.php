<?php

$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'basic',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm' => '@vendor/npm-asset',
        '@components' => '@app/components', //add
    ],


    // 操作日志
    'on beforeRequest' => function($event) {
        \yii\base\Event::on(\yii\db\BaseActiveRecord::className(), \yii\db\BaseActiveRecord::EVENT_BEFORE_UPDATE, ['components\DbLog', 'beforeUpdate']);
        \yii\base\Event::on(\yii\db\BaseActiveRecord::className(), \yii\db\BaseActiveRecord::EVENT_AFTER_UPDATE, ['components\DbLog', 'afterUpdate']);
        \yii\base\Event::on(\yii\db\BaseActiveRecord::className(), \yii\db\BaseActiveRecord::EVENT_AFTER_DELETE, ['components\DbLog', 'afterDelete']);
        \yii\base\Event::on(\yii\db\BaseActiveRecord::className(), \yii\db\BaseActiveRecord::EVENT_AFTER_INSERT, ['components\DbLog', 'afterInsert']);
    },


    'components' => [
        'redis' => [
            'class' => 'yii\redis\Connection',
            'hostname' => 'localhost',
            'port' => 6379,
            'database' => 0,
            'password' => '123qwe',

            /*'hostname' => '47.56.83.121',
            'port' => 6379,
            'database' => 0,
            'password' => 'yabo123!@#',*/
        ],
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => 'OavTGRC3YTayliry5ZmCNkgIonjhTGSD',
        ],
        'cache' => [
            //'class' => 'yii\caching\FileCache',
            'class' => 'yii\redis\Cache',
            'redis' => [
                'hostname' => 'localhost',
                'port' => 6379,
                'database' => 0,
                'password' => '123qwe'

                /*'hostname' => '47.56.83.121',
                'port' => 6379,
                'database' => 0,
                'password' => 'yabo123!@#'*/
            ],
        ],
        'session' => [
            'name' => 'advanced-frontend',
            'class' => 'yii\redis\Session',
            'redis' => [
                'hostname' => 'localhost',
                'port' => 6379,
                'database' => 0,
                'password' => '123qwe'
            ],

            /*'redis' => [
                'hostname' => '47.56.83.121',
                'port' => 6379,
                'database' => 0,
                'password' => 'yabo123!@#'
            ],*/

            'timeout' => 86400
        ],
        'user' => [
            'identityClass' => 'app\models\Admin', //modify
            'enableAutoLogin' => true,
            'authTimeout' => 86400,
        ],


        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            // send all mails to a file by default. You have to set
            // 'useFileTransport' to false and configure a transport
            // for the mailer to send real emails.
            'useFileTransport' => true,
        ],
        'log' => [
            'traceLevel' => YII_DEBUG ? 3 : 0,
            'targets' => [
                [
                    'class' => 'yii\log\FileTarget',
                    'levels' => ['error', 'warning', 'info'],
                    'logVars' => [],
                    'logFile' => '@runtime/logs/app.log',
                    'microtime' => true,
                ],
            ],
        ],
        'db' => $db,
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        /*=============== add begin ================*/
        'helper' => [
            'class' => 'sunnnnn\helper\Helper'
        ],
        'i18n' => [
            'translations' => [
                'app*' => [
                    'class' => 'yii\i18n\PhpMessageSource',
                    'basePath' => '@components/messages',
                    'fileMap' => [
                        'app' => 'app.php',
                        'app/menu' => 'menu.php',
                        'app/model' => 'model.php',
                        'app/ctrl' => 'ctrl.php',
                        'app/view' => 'view.php',
                    ],
                ],
            ],
        ],
        /*=============== add end ================*/
    ],
    'params' => $params,

    /*=============== add begin ================*/
    'modules' => [
        'auth' => [
            'class' => 'sunnnnn\nifty\auth\Module',
        ]
    ],
    'as access' => [
        'class' => 'sunnnnn\nifty\auth\components\AccessControl',
        'allowActions' => [
            'site/login',
            'site/ajax-login',
            'site/error',
            'debug/*',
            'gii/*',
            'curl/*',
        ]
    ],

    'language' => 'zh-CN',
    'timeZone' => 'Asia/Shanghai',
    /*=============== add end ================*/
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
        // uncomment the following to add your IP if you are not connecting from localhost.
        //'allowedIPs' => ['127.0.0.1', '::1'],
    ];

    $config['bootstrap'][] = 'gii';
    /*=============== add begin ================*/
    $config['modules']['gii'] = [
        'class' => 'yii\gii\Module',
        //'allowedIPs' => ['127.0.0.1', '::1'],
        'allowedIPs' => ['*'],
        'generators' => [
            'crud' => [
                'class' => 'yii\gii\generators\crud\Generator',
                'templates' => [
                    'sunnnnn-nifty-curd' => '@components/gii/generators/crud/default',
                    'sunnnnn-nifty-curd-ajax' => '@components/gii/generators/crud-ajax/default',
                ]
            ],
            'model' => [
                'class' => 'yii\gii\generators\model\Generator',
                'templates' => [
                    'sunnnnn-nifty-model' => '@components/gii/generators/model/default',
                ]
            ],
        ],
    ];
    /*=============== add end ================*/
}

return $config;
