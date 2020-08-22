<?php
$params = array_merge(
    require __DIR__ . '/../../common/config/params.php',
    require __DIR__ . '/../../common/config/params-local.php',
    require __DIR__ . '/params.php',
    require __DIR__ . '/params-local.php'
);

return [
    'id' => 'work-backend',
    'basePath' => dirname(__DIR__),
    'controllerNamespace' => 'backend\controllers',
    'bootstrap' => ['log'],
    //==================ADD===================
    'aliases' => [
        '@components' => '@backend/components',
    ],
    //========================================
    'modules' => [],
    'components' => [
        'request' => [
            'csrfParam' => '_csrf-backend',
        ],
        'user' => [
            'identityClass' => 'backend\models\Admin', //ADD
            'enableAutoLogin' => true,
            'identityCookie' => ['name' => '_identity-backend', 'httpOnly' => true],
        ],
        'session' => [
            // this is the name of the session cookie used for login on the backend
            'name' => 'advanced-backend',
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
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'urlManager' => [
            'enablePrettyUrl' => true,
            'showScriptName' => false,
            'rules' => [
            ],
        ],
        //==================ADD===================
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
        //=========================================
    ],
    'params' => $params,
    //==================ADD===================
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
    //========================================
];
