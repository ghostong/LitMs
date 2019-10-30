<?php
/**
 * 基础服务
 */
namespace Lit\LitMs;

class LitMsServer{

    private $httpHost;
    private $httpPort;
    private $serverSet;

    function  __construct( $workDir){ //use __DIR__
        define("WORK_DIR",$workDir);
        $this->requireBaseFile(); //基础文件
        $this->requireModelFile(); //模块文件
        $this->serverConfig(); //服务配置文件
        $this->welcome(); //欢迎词
        $this->serverStart();//启动服务
    }

    private function requireBaseFile(){
        //配置文件
        $configFile = WORK_DIR.DIRECTORY_SEPARATOR."Config.php";
        if( !file_exists($configFile) ){
            echo "未找到配置文件:".$configFile.PHP_EOL;
        }else{
            require ( $configFile."" );
        }
        //基础函数
        require (__DIR__.DIRECTORY_SEPARATOR."LitMsFunction.php");
        //基础控制层
        require (__DIR__.DIRECTORY_SEPARATOR."LitMsController.php");
        //基础模块层
        require (__DIR__.DIRECTORY_SEPARATOR."LitMsModel.php");
        //控制层文件
        $controllerFile = WORK_DIR.DIRECTORY_SEPARATOR."Controller.php";
        if( !file_exists($controllerFile) ){
            echo "未找到Controller文件:".$controllerFile.PHP_EOL;
        }else{
            require ( $controllerFile."" );
        }
    }

    private function requireModelFile () {
        $fileIterator = new \FilesystemIterator(WORK_DIR.DIRECTORY_SEPARATOR."Model".DIRECTORY_SEPARATOR);
        foreach($fileIterator as $fileInfo) {
            if($fileInfo->isFile()){
                require_once ($fileInfo->getPathName()."");
            }
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
        $outPut = "";
        $outPut .= "+--------------------------------------------------+".PHP_EOL;
        $outPut .= "|   +      _       _   _     __  __            +   |".PHP_EOL;
        $outPut .= "|   |     | |     (_) | |_  |  \/  |  ___      |   |".PHP_EOL;
        $outPut .= "|   |     | |     | | | __| | |\/| | / __|     |   |".PHP_EOL;
        $outPut .= "|   |     | |___  | | | |_  | |  | | \__ \     |   |".PHP_EOL;
        $outPut .= "|   .     |_____| |_|  \__| |_|  |_| |___/     .   |".PHP_EOL;
        $outPut .= "|                                                  |".PHP_EOL;
        $outPut .= "+--------------------------------------------------|".PHP_EOL;
        foreach(@swoole_get_local_ip() as $key => $val) {
            $serverIp = "| Server address ".$key.": ".$val;
            $outPut .= $serverIp.str_repeat(" ",52-strlen($serverIp)-1)."|".PHP_EOL;
        }
        $httpHost = "| Use http://".$this->httpHost.":".$this->httpPort;
        $outPut .= $httpHost.str_repeat(" ",52-strlen($httpHost)-1)."|".PHP_EOL;
        $outPut .= "+--------------------------------------------------+".PHP_EOL;
        $outPut .= "|                                  Power By Ghost  |".PHP_EOL;
        $outPut .= "|                                 ghostong@126.com |".PHP_EOL;
        $outPut .= "+--------------------------------------------------+".PHP_EOL;
        echo $outPut;
    }

    public function serverStart(){
        if(defined("LITMS_OPEN_BASEDIR") && !empty(LITMS_OPEN_BASEDIR)) { //设置安全目录
            ini_set("open_basedir",implode(":",LITMS_OPEN_BASEDIR));
        }
        try {
            $controller = new \Controller();
            $httpServer = new \Swoole\Http\Server($this->httpHost, $this->httpPort);
            $httpServer->set($this->serverSet);
            $httpServer->on('request', function ($request, $response) use ($controller) {
                if(is_file(WORK_DIR.DIRECTORY_SEPARATOR."Static".DIRECTORY_SEPARATOR.$request->server['request_uri'])){
                    $response->sendfile(WORK_DIR.DIRECTORY_SEPARATOR."Static".DIRECTORY_SEPARATOR.$request->server['request_uri']);
                }else{
                    $response->end($controller->doIt($request, $response));
                }
            });
            echo "Server start !", PHP_EOL;
            $httpServer->start();
        }catch ( \Exception $e ) {
            echo "Server started error :".$e->getMessage(),PHP_EOL;
        }
    }
}

