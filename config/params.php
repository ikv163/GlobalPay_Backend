<?php

return [
    'adminEmail' => 'admin@example.com',
    'senderEmail' => 'noreply@example.com',
    'senderName' => 'Example.com mailer',
    'password' => 'password@@@@@!',

    'gatewayworkerHost' => 'globalpayapi.99xyp.com',
    'merchant_id' => '1000002',
    'merchant_key' => 'C49DD73A22C4FFDC2E1123382B70D336',
    'typay_deposit_url' => 'http://api.99xyp.com/deposit/create',


    'typay_merchant_id' => '10099',
    'typay_merchant_key' => '40fefe6c2b7e4aefe053e98bd978c67a',
    'typay_gateway_domain' => 'http://47.75.127.22:8081',

    //充值重定向及回调url
    'deposit_return_url' => '',
    'deposit_notify_url' => '/api/deposit-notify',

    //取款重定向及回调url
    'withdraw_return_url' => '',
    'withdraw_notify_url' => '/api/withdraw-notify',

    //收银台页面收款渠道数据加密key
    'counter_key' => '80fefg6gtb7e4aefe238fdt8',
];
