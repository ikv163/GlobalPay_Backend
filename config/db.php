<?php
if (YII_DEBUG || YII_ENV == 'dev'){
    return [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=47.56.137.28;dbname=global_pay',
        'username' => 'testtest',
        'password' => 'testtest',
        'charset' => 'utf8',

        /*'dsn' => 'mysql:host=localhost:3306;dbname=global_pay',
        'username' => 'root',
        'password' => '123qwe',
        'charset' => 'utf8',*/


    ];
}else{
    return [
        'class' => 'yii\db\Connection',
        'dsn' => 'mysql:host=47.56.137.28;dbname=global_pay',
        'username' => 'testtest',
        'password' => 'testtest',
        'charset' => 'utf8',

        /*'dsn' => 'mysql:host=localhost:3306;dbname=global_pay',
        'username' => 'root',
        'password' => '123qwe',
        'charset' => 'utf8',*/
    ];
}
