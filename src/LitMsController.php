<?php
/**
 * 基础控制层
 */
namespace Lit\LitMs;

class LitMsController{

    private  $requestCache;

    public function doIt ( $request , $response ){
        $httpRequestUri = $request->server['request_uri'];
        $httpRequestMethod = strtolower($request->server['request_method']);
        if( $this->getRequestCache($httpRequestMethod,$httpRequestUri) ){
            $requestCache = $this->getRequestCache($httpRequestMethod,$httpRequestUri);
            return $requestCache['callBack']($request,$response);
        }elseif(isset($this->requestCache[$httpRequestUri])){
            return $this->errorPage($request ,$response,405);
        }else{
            return $this->errorPage($request ,$response, 404);
        }
    }

    public function errorPage ( $request , $response , $status ){
        $response->status($status);
        return Error($status);
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
        if(isset($this->requestCache[$requestUri][$requestMethod]) || isset($this->requestCache[$requestUri]["all"])){
            throw new \Exception("Routing error,"." duplicate request uri ".$requestMethod.":".$requestUri);
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