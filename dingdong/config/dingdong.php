<?php

return [

    // 加密盐
    'salt' => 'RKek2dUYSoigxD6',

    // jwt 密码
    'jwt_key' => 'zY6dBijuOjEpxr6',

    // 项目域名
    'domain' => 'http://www.df.com',

    // 端口
    'ws_port' => 9120,

    // 是否开启ssl
    'open_ssl' => env('SSL.IS_OPEN', false),

    // ssl配置文件
    'context' => [
        'ssl' => [
            'local_cert'  => env('SSL.LOCAL_CERT', ''),
            'local_pk'    => env('SSL.LOCAL_PK', ''),
            'verify_peer' => false,
        ]
    ]
];