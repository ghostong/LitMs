<?php
/**
 * LitMs 微服务配置框架
 */

//服务监听IP 默认值 127.0.0.1
define("LITMS_HTTP_HOST","0.0.0.0");

//服务监听端口 默认值 8080
define("LITMS_HTTP_PORT",8080);

//服务Worker进程数 默认值 2
define("LITMS_WORKER_NUM",2);

//服务守护进程化 默认值 false
define("LITMS_DAEMONIZE", false);

//其他服务设置, 支持swoole_server::set 所有参数, 此参数会覆盖之前配置
//参考 https://wiki.swoole.com/wiki/page/274.html
define("SWOOLE_SERVER_SET",array(

));

//服务所读取文件限制(安全)目录
//目录名称请以目录分隔符(DIRECTORY_SEPARATOR)结尾
define("LITMS_OPEN_BASEDIR",array(
    __DIR__.DIRECTORY_SEPARATOR, // 当前项目目录
    dirname(__DIR__).DIRECTORY_SEPARATOR."vendor".DIRECTORY_SEPARATOR, // vendor目录
));