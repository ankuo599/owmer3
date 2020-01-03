<?php
return [
    // 签发的key
    "key" => "chenbool",
    
    // 非必须。签发者
    "iss" => "http://example.org",

    // 非必须。接收该JWT的一方。
    // "aud" => "http://aihuishou.com",

    // token签发时间，unix时间戳格式
    "iat" => time(),

    //过期时间
    "exp" => time() + 7200,

    //(Not Before):某个时间点后才能访问,比如设置time()+30,表示当前时间30秒后才能使用
    "nbf" => time(),
];