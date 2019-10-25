<?php
/**
 * 基础模块层
 */
namespace Lit\LitMs;

class LitMsModel{

    function __construct(){

    }

    public function __call($name, $arguments){
        return Error(50100, "方法".$name."不存在");
    }

}