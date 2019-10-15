<?php

namespace Lit\LitMs;

class LitMsServer{

    private $httpHost;
    private $httpPort;
    private $serverSet;

    function  __construct( $workDir = ''){
        self::requireBaseFile($workDir);
        $this->serverConfig();
        $this->welcome();
        $this->serverStart();
    }

    private static function requireBaseFile($workDir){
        //配置文件
        $configFile = $workDir.DIRECTORY_SEPARATOR."Config.php";
        if(!file_exists($configFile)){
            echo "未找到配置文件:".$configFile.PHP_EOL;
        }else{
            require ( $configFile );
        }
        //基础函数
        require (__DIR__.DIRECTORY_SEPARATOR."LitMsFunction.php");
        //基础控制层
        require (__DIR__.DIRECTORY_SEPARATOR."LitMsController.php");
        //控制层文件
        $controllerFile = $workDir.DIRECTORY_SEPARATOR."Controller.php";
        if(!file_exists($controllerFile)){
            echo "未找到Controller文件:".$controllerFile.PHP_EOL;
        }else{
            require ( $controllerFile );
        }
    }

    private function serverConfig(){
        $this->httpHost = defined("LITMS_HTTP_HOST")? LITMS_HTTP_HOST : "127.0.0.1";
        $this->httpPort =  defined("LITMS_HTTP_PORT")? LITMS_HTTP_PORT : 8080;
        $this->serverSet["worker_num"] = defined("LITMS_WORKER_NUM") ?  LITMS_WORKER_NUM : 2;
        $this->serverSet["daemonize"] = defined("LITMS_DAEMONIZE") ?  LITMS_DAEMONIZE : false;
        if ( defined("SWOOLE_SERVER_SET") ) {
            $this->serverSet = array_merge($this->serverSet,SWOOLE_SERVER_SET);
        }
    }

    public function welcome (){
        echo "
        +--------------------------------------------------+
        |          _       _   _     __  __                |
        |         | |     (_) | |_  |  \/  |  ___          |
        |         | |     | | | __| | |\/| | / __|         |
        |         | |___  | | | |_  | |  | | \__ \         |
        |         |_____| |_|  \__| |_|  |_| |___/         |
        |                                                  |
        +--------------------------------------------------+
        ";
    }

    public function serverStart(){
        try {
            $controller = new \Controller();
            $httpServer = new \Swoole\Http\Server($this->httpHost, $this->httpPort);
            $httpServer->set($this->serverSet);
            $httpServer->on('request', function ($request, $response) use ($controller) {
                $response->end($controller->doIt($request, $response));
            });
            echo "Server start !", PHP_EOL;
            $httpServer->start();
        }catch ( \Exception $e ) {
            echo "Server started error :".$e->getMessage(),PHP_EOL;
        }
    }
}

