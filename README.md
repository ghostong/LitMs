###安装
````
#编辑composer.json文件
"require" : {
     ...
     "lit/litms": "dev-master"
}
#安装后使用文档中的调用方法即可使用.
````

###初始化项目
1. 选定代码根目录,创建 Server.php (任意php文件名)
2. 粘如一下代码
````PHP
<?php
require(dirname(__DIR__).'/vendor/autoload.php'); //autoload.php 文件目录
new \Lit\LitMs\LitMsServer(__DIR__);
````

3. 创建配置文件 Config.php
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
````

4. 创建控制层文件 Controller.php 
````PHP
<?php
class Controller extends LitMsController {
    function __construct(){
        //注册一个全method路由
        $this->all('/',function ($request,&$response){
            return "Welcome to LitMs";
        });
        //注册一个全method路由
        $this->post('/ao',function ($request,&$response){
            return "Welcome to LitMs";
        });
        //注册另一个get路由
        $this->get("/test",function ($request,&$response){
            return "Method get";
        });
        //注册另一个post路由
        $this->post("/test",function ($request,&$response){
            return "Method post";
        });
        //注册另一个delete路由
        $this->delete("/test",function ($request,&$response){
            return "Method delete";
        });
    }
}
````

###启动项目
````BASH
php Server.php 
````