### 安装
````
#编辑composer.json文件
"require" : {
     ...
     "lit/litms": "dev-master"
}
#安装后使用文档中的调用方法即可使用.
````

### 初始化项目
1. composer安装好项目后,复制 vendor/lit/litms/demo 目录中的文件到项目目录.
2. 修改 Server.php 中 autoload 为自己的 vendor/autoload.php .
3. 修改 Server.php 的配置项, 每个配置项都有系统默认值

### 代码详解
1. Server.php
````PHP
<?php
//autoload
require(dirname(__DIR__).'/vendor/autoload.php');
$server = new \Lit\LitMs\LitMsServer();
$server
    ->setHttpHost("0.0.0.0")    //设置监听host ip
    ->setHttpPort(9000)    //设置 监听端口
    ->setWorkerNum(10)    //设置 进程数量
    ->setWorkDir(__DIR__)    //设置项目目录
    ->setDaemonize(false)    //设置是否守护进程
    ->setOpenBaseDir(__DIR__)    //设置读取安全目录
    ->setOpenBaseDir(dirname(__DIR__).DIRECTORY_SEPARATOR."vendor")    //设置读取安全目录
    ->setLogFile("/tmp/litmsError.log")    //设置错误日志文件
    ->setLogLevel(0)    //设置输出错误等级
    ->setSlowLogFile("/tmp/litmsSlow.log")    //设置慢日志文件
    ->setSlowTimeOut(1)    //设置慢日志时间
    ->setDocumentRoot(__DIR__.DIRECTORY_SEPARATOR."Static")    //设置静态目录
    ->setAuthenticate(['user1'=>'123','user2'=>'234','user3'=>'345'])    //开启简单身份认证,设置用户名密码
    ->run();
````

2. Controller.php 
````PHP
<?php
class Controller extends Lit\LitMs\LitMsController {
    function __construct(){
        //注册一个全method路由
        $this->all('/',function ($request,&$response){
            return Model("Welcome")->welcome();
        });
        //注册另一个get路由
        $this->get("/get",function ($request,&$response){
            return "Method get";
        });
        //注册另一个post路由
        $this->post("/post",function ($request,&$response){
            return "Method post";
        });
        //注册另一个delete路由
        $this->delete("/delete",function ($request,&$response){
            return "Method delete";
        });
        //注册另一个静态页面
        $this->get("/html",function ($request,&$response){
            return View("HtmlDemo.html");
        });
    }
}
````

3. Viwe 目录
````php
<?php
//此目录为静态HTML文件目录, 通过如下代码直接读取此文件
//本框架为动态微服务框架,尽量不要使用静态文件和静态HTML
return View("HTMLDemo.html");
````

4. Static 目录
````php
此目录为静态文件目录, 此目录中的文件通过url地址可以直接访问
例如:
    http://hostname/css/app.css 
本框架为动态微服务框架,尽量不要使用静态文件和静态HTML
````

5. Model
````php
<?php
//创建 WelcomeModel.php 类文件可直接使用 Model("Welcome")->方法名()调用
return Model("Welcome")->welcome();
````

### 启动项目
````BASH
php Server.php 
````