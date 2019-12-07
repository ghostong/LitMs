<?php
/**
 * 基础模块层
 */
namespace Lit\LitMs;

class LitMsModel{

    //单例缓存变量
    static private $instance;

    function __construct(){

    }

    //单例获取
    static public function getInstance(){
        if (!self::$instance instanceof self) {
            $className = get_called_class();
            self::$instance = new $className ();
        }
        return self::$instance;
    }

    public function __call($name, $arguments){
        return Error(50100, "方法".$name."不存在");
    }

}