<?php

return [
    "server" => [
        "host" => "127.0.0.1",
        "port" => "8090",
    ],
    "client" => [
        "host" => "127.0.0.1",
        "port" => "8989",
        "setting"=>[
            'log_level' => 5,
            'log_file' => '/tmp/swoole.log',
            'open_eof_check' => true, //打开EOF检测
            'package_eof' => "\r\n", //设置EOF
        ]
        ],
    'redis' =>[
        'host'=>'127.0.0.1',
        'port'=>6379,
        'password'=>"",
        "topic"=>"pubsub"
    ],
    "time_tick" => "5000",
    "subscribe_type"=>"tcp",
    'subscribe' => "Task\\Server\\Tcp"
];