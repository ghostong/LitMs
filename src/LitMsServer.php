<?php
/**
 * 基础服务
 */
namespace Lit\LitMs;

class LitMsServer{

    private $httpHost;
    private $httpPort;
    private $litMsDir;
    private $workDir;
    private $onStartFile;
    private $isAuthenticate = false;
    private $authDict = array();
    private $serverConfig = array();
    private $openBaseDir = array();

    function  __construct(){
        //默认host
        $this->httpHost = "127.0.0.1";
        //默认端口
        $this->httpPort = 8080;
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

    //框架基础文件
    private function requireBaseFile(){
        //基础函数
        require ($this->litMsDir."LitMsFunction.php");
        //基础控制层
        require ($this->litMsDir."LitMsController.php");
        //基础模块层
        require ($this->litMsDir."LitMsModel.php");
    }

    //导入控制层
    private function requireContollerFile(){
        //控制层文件
        $controllerFile = $this->workDir."Controller.php";
        if( !file_exists($controllerFile) ){
            echo "未找到Controller文件:".$controllerFile.PHP_EOL;
        }else{
            require ( $controllerFile."" );
        }
    }

    //用户自定义模块文件
    private function requireModelFile () {
        $modelDir = $this->workDir."Model".DIRECTORY_SEPARATOR;
        if(!is_dir($modelDir)){
            echo "未找到Model目录:".$modelDir.PHP_EOL;
        }
        $fileIterator = new \FilesystemIterator($modelDir);
        foreach($fileIterator as $fileInfo) {
            if($fileInfo->isFile()){
                require_once ($fileInfo->getPathName()."");
            }
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

    //启动前时调用一次
    private function onStart () {
        if ( $this->onStartFile ) {
            $outPut = "";
            $outPut .= $this->terminalDrawLine(52);
            $outPut .= $this->terminalDrawRow("On Start v",52,"middle");
            echo $outPut,PHP_EOL;
            require_once ($this->onStartFile."");
            $outPut = "";
            $outPut .= $this->terminalDrawRow("On Start ^",52,"middle");
            $outPut .= $this->terminalDrawLine(52);
            echo PHP_EOL,$outPut;
        }
    }

    //欢迎画面
    private function welcome (){
        $outPut = "";
        $outPut .= $this->terminalDrawLine(52);
        $outPut .= $this->terminalDrawRow("+      _       _   _     __  __            +",52,"middle");
        $outPut .= $this->terminalDrawRow("|     | |     (_) | |_  |  \/  |  ___      |",52,"middle");
        $outPut .= $this->terminalDrawRow("|     | |     | | | __| | |\/| | / __|     |",52,"middle");
        $outPut .= $this->terminalDrawRow("|     | |___  | | | |_  | |  | | \__ \     |",52,"middle");
        $outPut .= $this->terminalDrawRow(".     |_____| |_|  \__| |_|  |_| |___/     .",52,"middle");
        $outPut .= $this->terminalDrawRow(" ",52,"middle");
        $outPut .= $this->terminalDrawLine(52);
        foreach(@swoole_get_local_ip() as $key => $val) {
            $outPut .= $this->terminalDrawRow("IP address for ".$key.": ".$val,52,"left");
        }
        $httpHost = "Use http://".$this->httpHost.":".$this->httpPort;
        $outPut .= $this->terminalDrawRow($httpHost,52,"left");
        $outPut .= $this->terminalDrawLine(52);
        $outPut .= $this->terminalDrawRow(" Power By Ghost ",52,"right");
        $outPut .= $this->terminalDrawRow("ghostong@126.com",52,"right");
        $outPut .= $this->terminalDrawLine(52);
        echo $outPut;
    }

    //启动服务
    private function serverStart(){
        try {
            $controller = new \Controller();
            $httpServer = new \Swoole\Http\Server($this->httpHost, $this->httpPort);
            $httpServer->set($this->serverConfig);
            $httpServer->on('request', function ($request, $response) use ($controller) {
                if( $this->isAuthenticate && !EasyAuthenticate($request, $this->authDict) ){
                    $response->header('WWW-Authenticate','Basic realm="LitMs"');
                    $response->status(401);
                    $response->end(Error(403));
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

    //启动服务
    public function run () {
        //载入框架基础文件
        $this->requireBaseFile();
        //载入控制层
        $this->requireContollerFile();
        //载入用户自定义模块
        $this->requireModelFile();
        //安全目录
        $this->safeDir();
        //设置框架常量
        $this->setDefault();
        //当启动时调用
        $this->onStart();
        //欢迎词
        $this->welcome();
        //启动服务
        $this->serverStart();
    }


    //终端线
    private function terminalDrawLine( $width ){
        $start = "+";
        $end = "+";
        $line = $start . str_repeat("-",$width-2) . $end .PHP_EOL;
        return $line;
    }

    //终端行
    private function terminalDrawRow( $str, $width , $margin){
        $start = "|";
        $end = "|";
        if(strlen($str)+4 >= $width){
            $str = substr($str,0,$width-4);
        }
        if ($margin == "right") {
            $str = $str." ";
            $line = $start.str_repeat(" ",$width-2-strlen($str)).$str.$end.PHP_EOL;
        }elseif($margin == "middle"){
            $str = $str;
            $line = $start.str_repeat(" ",ceil(($width-2-strlen($str))/2)).$str.str_repeat(" ",floor(($width-2-strlen($str))/2)).$end.PHP_EOL;
        }else{
            $str = " ".$str;
            $line = $start.$str.str_repeat(" ",$width-2-strlen($str)).$end.PHP_EOL;
        }
        return $line;
    }
}