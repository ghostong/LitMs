<?php
/**
 * LitMs Controller 层
 */

class Controller extends Lit\LitMs\LitMsController {

    function __construct(){

        //注册一个全method路由
        $this->all('/',function ($request,&$response){
            return Model("Welcome")->welcome();
        });

        //注册另一个get路由
        $this->get("/html",function ($request,&$response){
            return View("HtmlDemo.html");
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