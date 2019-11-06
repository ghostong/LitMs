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
2. 修改 Server.php 中 autoload 为自己的 vendor/autoload.php 即可

### 代码详解
1. Server.php
````PHP
<?php
require(dirname(__DIR__).'/vendor/autoload.php'); //autoload.php 文件目录
new \Lit\LitMs\LitMsServer(__DIR__);
````

2. Config.php
````PHP
<?php 
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
````

3. Controller.php 
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

4. Viwe 目录
````php
<?php
//此目录为静态HTML文件目录, 通过如下代码直接读取此文件
//本框架为动态微服务框架,尽量不要使用静态文件和静态HTML
return View("HTMLDemo.html");
````

5. Static 目录
````php
此目录为静态文件目录, 此目录中的文件通过url地址可以直接访问
例如:
    http://hostname/static/css/app.css 
本框架为动态微服务框架,尽量不要使用静态文件和静态HTML
````

6. Model
````php
<?php
//创建 WelcomeModel.php 类文件可直接使用 Model("Welcome")->方法名()调用
return Model("Welcome")->welcome();
````

### 启动项目
````BASH
php Server.php 
````