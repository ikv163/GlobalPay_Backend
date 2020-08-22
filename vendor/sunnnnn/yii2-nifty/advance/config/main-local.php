<?php

$config = [
    'components' => [
        'request' => [
            // !!! insert a secret key in the following (if it is empty) - this is required by cookie validation
            'cookieValidationKey' => '6S_Japt2dOv_SwbvxlWFbiCa61whtLgh',
        ],
    ],
];

if (YII_ENV_DEV) {
    // configuration adjustments for 'dev' environment
    $config['bootstrap'][] = 'debug';
    $config['modules']['debug'] = [
        'class' => 'yii\debug\Module',
    ];
    //==================ADD===================
    $config['bootstrap'][] = 'gii';
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
    //========================================
}

return $config;
