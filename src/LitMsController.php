<?php
/**
 * 模块基础类
 */
namespace Lit\Ms;

class LitMsController{

    //单例缓存变量
    static private $instance = array();

    //单例获取
    static public function getInstance(){
        $className = get_called_class();
        if (!isset (self::$instance[$className]) || !is_object(self::$instance[$className])) {
            self::$instance[$className] = new $className ();
        }
        return self::$instance[$className];
    }

}