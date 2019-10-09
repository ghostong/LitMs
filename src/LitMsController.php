<?php
/**
 * 基础控制层
 */


class LitMsController{

    private  $requestCache;

    public function doIt ($request){
        $httpRequestUri = $request->server['request_uri'];
        $httpRequestMethod = strtolower($request->server['request_method']);
        if( $this->getRequestCache($httpRequestMethod,$httpRequestUri) ){
            $requestCache = $this->getRequestCache($httpRequestMethod,$httpRequestUri);
            return $requestCache['callBack']();
        }elseif(isset($this->requestCache[$httpRequestUri])){
            #todo
            //method not allow
            return "method not allow";
        }else{
            #todo
            //404
            return "404";
        }
    }

    public function get ($requestUri,$callBack) {
        $this->setRequestCache("get",$requestUri,$callBack);
    }

    public function post ($requestUri,$callBack) {
        $this->setRequestCache("post",$requestUri,$callBack);
    }

    public function put ($requestUri,$callBack) {
        $this->setRequestCache("put",$requestUri,$callBack);
    }

    public function delete ($requestUri,$callBack) {
        $this->setRequestCache("delete",$requestUri,$callBack);
    }

    public function all ($requestUri,$callBack) {
        $this->setRequestCache("all",$requestUri,$callBack);
    }

    private function setRequestCache($requestMethod,$requestUri,$callBack){
        if(isset($this->requestCache[$requestUri][$requestMethod])){
            new Exception("路由注册,".$requestMethod,":",$requestUri."重复");
        }
        $this->requestCache[$requestUri][$requestMethod] = array (
            "requestMethod" => $requestMethod,
            "callBack" => $callBack
        );
    }

    private function getRequestCache($requestMethod,$requestUri){
        if( isset($this->requestCache[$requestUri][$requestMethod]) ) {
            return $this->requestCache[$requestUri][$requestMethod];
        }elseif( isset($this->requestCache[$requestUri]["all"]) ){
            return $this->requestCache[$requestUri]["all"];
        }else{
            return array();
        }
    }

}