<?php
$params = require __DIR__ . '/params.php';
$db = require __DIR__ . '/db.php';

$config = [
    'id' => 'nifty',
    'name' => 'Yii2 Nifty',
    'basePath' => dirname(__DIR__),
    'bootstrap' => ['log'],
    'aliases' => [
        '@bower' => '@vendor/bower-asset',
        '@npm'   => '@vendor/npm-asset',
        '@components' => '@app/components', //add
    ],
    'components' => [
        'request' => [
            'cookieValidationKey' => 'J0qXYhc28pdJPiLu5Nco8qK6r502KE-q',
        ],
        'cache' => [
            'class' => 'yii\caching\FileCache',
        ],
        'user' => [
            'identityClass' => 'app\models\Admin', //modify
            'enableAutoLogin' => true,
        ],
        'errorHandler' => [
            'errorAction' => 'site/error',
        ],
        'mailer' => [
            'class' => 'yii\swiftmailer\Mailer',
            'useFileTransport' => true,
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
	
	'language' => 'en',
	'timeZone' => 'Asia/Shanghai',
	/*=============== add end ================*/
];

if (YII_ENV_DEV) {
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];

    $config['bootstrap'][] = 'gii';
    /*=============== add begin ================*/
    $config['modules']['gii'] = [
		'class' => 'yii\gii\Module',
		'allowedIPs' => ['127.0.0.1', '::1'],
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
