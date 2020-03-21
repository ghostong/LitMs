<?php
/**
 * 微服务基础类
 */
namespace Lit\Ms;

class LitMsServer {

    private $httpHost="127.0.0.1"; //默认host
    private $httpPort="8080"; //默认端口
    private $litMsDir="";
    private $workDir="";
    private $onStartFile="";
    private $isAuthenticate = false;
    private $authDict = array();
    private $serverConfig = array();
    private $openBaseDir = array();
    private $sslConnect=false;
    private $terminalWidth=52;

    function  __construct(){
        //框架目录
        $this->litMsDir = __DIR__.DIRECTORY_SEPARATOR;
        //默认项目目录
        if(isset($_SERVER["PWD"]) && $_SERVER["PWD"]) {
            $this->workDir  = dirname($_SERVER["PWD"].DIRECTORY_SEPARATOR.$_SERVER["PHP_SELF"]).DIRECTORY_SEPARATOR;
        }else{
            $this->workDir  = DIRECTORY_SEPARATOR."workdir".DIRECTORY_SEPARATOR;
        }
        //静态文件
        $this->serverConfig["document_root"] = $this->workDir.DIRECTORY_SEPARATOR."Static".DIRECTORY_SEPARATOR;
        $this->serverConfig["enable_static_handler"] =true;

    }

    //设置 http host
    public function setHttpHost ( string $host ){
        $this->httpHost = $host;
        return $this;
    }

    //设置 http port
    public function setHttpPort (int $port ) {
        $this->httpPort = $port;
        return $this;
    }

    //设置 项目目录
    public function setWorkDir ( string $workDir ) {
        if(substr($workDir,-1) == DIRECTORY_SEPARATOR){
            $this->workDir = $workDir;
        }else{
            $this->workDir = $workDir.DIRECTORY_SEPARATOR;
        }
        return $this;
    }

    //设置 进程数
    public function setWorkerNum ( int $workerNum ) {
        $this->serverConfig['worker_num'] = $workerNum;
        return $this;
    }

    //设置 守护进程
    public function setDaemonize ( bool $daemonize ) {
        $this->serverConfig["daemonize"] = $daemonize;
        return $this;
    }

    //设置 日志文件
    public function setLogFile ( string $logFile ){
        $this->serverConfig["log_file"] = $logFile;
        return $this;
    }

    //设置 日志输出级别
    public function setLogLevel ( string $logLevel ) {
        $this->serverConfig["log_level"] = $logLevel;
        return $this;
    }

    //设置 慢日志文件
    public function setSlowLogFile ( string $slowLogFile ) {
        $this->serverConfig["request_slowlog_file"] = $slowLogFile;
        return $this;
    }

    //设置 慢日志时间
    public function setSlowTimeOut ( int $seconds ){
        $this->serverConfig["request_slowlog_timeout"] = $seconds;
        return $this;
    }

    //设置 静态文件目录
    public function setDocumentRoot ( string $documentRoot ) {
        $this->serverConfig["document_root"] = $documentRoot;
        $this->serverConfig["enable_static_handler"] =true;
        return $this;
    }

    //设置 读取安全目录
    public function setOpenBaseDir ( string $dir ) {
        $this->openBaseDir[]=$dir;
        return $this;
    }

    //设置 简单身份认证
    public function setAuthenticate ( array $authDict ){
        $this->authDict = $authDict;
        $this->isAuthenticate = true;
        return $this;
    }

    //设置 启动钩子
    public function setOnStart( $onStartFile ){
        if(is_file($onStartFile)){
            $this->onStartFile = $onStartFile;
        }else{
            $this->onStartFile = "";
        }
        return $this;
    }

    //设置 SSL证书
    public function setSslCertFile( $sslCertFile, $sslKeyFile ){
        $this->sslConnect = true;
        $this->serverConfig["ssl_cert_file"] = $sslCertFile;
        $this->serverConfig["ssl_key_file"] = $sslKeyFile;
        return $this;
    }

    //框架基础文件
    private function requireBaseFile(){
        $fileList= [
            "LitMsTerminalDraw"=> $this->litMsDir."LitMsTerminalDraw.php", //绘图类
            "LitMsFunction"    => $this->litMsDir."LitMsFunction.php", //函数文件
            "LitMsFilter"      => $this->litMsDir."LitMsFilter.php", //基础过滤器文件
            "LitMsController"  => $this->litMsDir."LitMsController.php", //基础控制文件
            "LitMsModel"       => $this->litMsDir."LitMsModel.php", //基础模块文件
            "LitMsSchedule"    => $this->litMsDir."LitMsSchedule.php" //定时任务模块文件
        ];
        foreach ( $fileList as $name => $file) {
            if( !file_exists($file) ){
                echo "未找到".$name."文件: ".$file.PHP_EOL;
            }else{
                require( $file."" );
            }
        }
    }

    //导如用户必定义文件
    private function requireUserFile(){
        $fileList= [
            "Filter"     => $this->workDir."Filter.php",    //过滤器文件
            "Controller" => $this->workDir."Controller.php" ,//路由控制文件
        ];
        foreach ( $fileList as $name => $file) {
            if( !file_exists($file) ){
                echo "未找到".$name."文件: ".$file.PHP_EOL;
            }else{
                require( $file."" );
            }
        }
    }

    //用户自动加载
    private function requireModelFile () {
        $modelDir = $this->workDir."Model".DIRECTORY_SEPARATOR;
        if(!is_dir($modelDir)){
            echo "未找到Model目录:".$modelDir.PHP_EOL;
        }else{
            spl_autoload_register(function($className) use ($modelDir){
                if ( is_file( $modelDir.$className.".php" ) ) {
                    require $modelDir.$className.".php";
                }
            });
        }
    }

    //项目可以读取的安全目录
    private function safeDir (){
        if (!empty($this->openBaseDir)) {
            ini_set("open_basedir",implode(":",$this->openBaseDir));
        }
    }

    //设置框架常量
    private function setDefault (){
        //默认项目目录常量
        define("LITMS_WORK_DIR",$this->workDir);
    }

    //判断是否定时任务启动
    private function isSchedule(){
        if ( isset($_SERVER["argv"][1]) && (strtolower($_SERVER["argv"][1]) == "schedule" || strtolower($_SERVER["argv"][1]) == "crontab") ) {
            return true;
        }else{
            return false;
        }
    }

    //判断是否定时任务启动
    private function isShell(){
        if ( isset($_SERVER["argv"][1]) && (strtolower($_SERVER["argv"][1]) == "shell" || strtolower($_SERVER["argv"][1]) == "command") ) {
            return true;
        }else{
            return false;
        }
    }

    //启动前时调用一次
    private function onStart () {
        if ( $this->onStartFile ) {
            require_once ($this->onStartFile."");
        }
    }

    //定时任务启动
    private function scheduleStart(){
        require ($this->workDir."Schedule.php");
        echo "Schedule start @",date("Y-m-d H:i:s"),PHP_EOL;
    }

    //单次shell
    private function shellStart(){
        if ( isset($_SERVER["argv"][2]) ) {
            $exp = explode( "/",$_SERVER["argv"][2] );
            call_user_func([Model($exp[0]),$exp[1]] );
        }else{
            echo "args is empty",PHP_EOL;
        }
    }

    //启动服务
    private function serverStart(){
        $filter = new \Filter();
        $controller = new \Controller();
        if ( $this->sslConnect ){
            $httpServer = new \Swoole\Http\Server($this->httpHost, $this->httpPort, SWOOLE_PROCESS, SWOOLE_SOCK_TCP | SWOOLE_SSL);
        }else{
            $httpServer = new \Swoole\Http\Server($this->httpHost, $this->httpPort);
        }
        $httpServer->set($this->serverConfig);
        $httpServer->on('request', function ($request, $response) use ($filter,$controller) {
            try {
                if ($request->server['path_info'] == '/favicon.ico' || $request->server['request_uri'] == '/favicon.ico') {
                    $response->end();
                }elseif( $this->isAuthenticate && !EasyAuthenticate($request, $this->authDict) ){ //如果简单身份认证
                    $response->header('WWW-Authenticate','Basic realm="LitMs"');
                    $response->status(401);
                    $response->end(Error(403));
                }elseif(!$filter->doIt($request, $response) ){ //如果过滤器
                    $response->end(Error($filter->getErrorCode(),$filter->getErrorMessage()));
                }else{ //正常逻辑
                    $response->end($controller->doIt($request, $response));
                }
            }catch ( \Exception $e ) {
                $response->status(500);
                $response->end(Error(500));
                echo "Exception: ".$e->getMessage(),PHP_EOL;
            }
        });
        echo "Server start @",date("Y-m-d H:i:s"), PHP_EOL;
        $httpServer->start();
    }

    //启动服务
    public function run () {
        //载入框架基础文件
        $this->requireBaseFile();
        //导入用户必定义文件
        $this->requireUserFile();
        //载入用户自定义模块
        $this->requireModelFile();
        //安全目录
        $this->safeDir();
        //设置框架常量
        $this->setDefault();
        //当启动时调用
        $this->onStart();

        if ($this->isSchedule()) { //如果是定时任务
            //欢迎词
            LitMsTerminalDraw::scheduleWelcome( $this->terminalWidth );
            //启动服务
            $this->scheduleStart();
        }elseif($this->isShell()){ //如果是单次Shell
            //运行Shell
            $this->shellStart();
        }else{
            //欢迎词
            LitMsTerminalDraw::httpServerWelcome( $this->terminalWidth, $this->sslConnect, $this->httpHost, $this->httpPort );
            //启动服务
            $this->serverStart();
        }
    }
}