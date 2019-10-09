<?php
/**
 * LitMs Controller 层
 */

class Controller extends LitMsController {
    function __construct(){

        //注册一个全method路由
        $this->all('/',function (){
            return "Welcome to LitMs";
        });

        //注册另一个get路由
        $this->get("/test",function (){
            return "Method get";
        });

        //注册另一个post路由
        $this->post("/test",function (){
            return "Method post";
        });

        //注册另一个delete路由
        $this->delete("/test",function (){
            return "Method delete";
        });


    }
}