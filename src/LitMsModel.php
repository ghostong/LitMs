<?php
/**
 * 基础模块层
 */
namespace Lit\LitMs;

class LitMsModel{

    //单例缓存变量
    static private $instance = array();

    function __construct(){

    }

    //单例获取
    static public function getInstance(){
        $className = get_called_class();
        if (!isset (self::$instance[$className]) || !is_object(self::$instance[$className])) {
            self::$instance[$className] = new $className ();
        }
        return self::$instance[$className];
    }

    public function __call($name, $arguments){
        return Error(50100, "方法".$name."不存在");
    }

}