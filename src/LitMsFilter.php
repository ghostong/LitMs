<?php
/**
 * 过滤器基础类
 */

namespace Lit\Ms;

class LitMsFilter {

    private static $errorMessage = null;
    private static $errorCode = null;

    /**
     * 启动过滤器
     * @param $request
     * @param $response
     * @return bool
     */
    public function run( $request , $response ) : bool {
        $className = get_called_class();
        $filter = new $className ();
        $methodSon = get_class_methods( get_called_class() );
        $methodSelf = get_class_methods( __CLASS__ );
        $diffMethods = array_diff( $methodSon, $methodSelf );
        foreach ( $diffMethods as $functionName ){
            if (  !call_user_func([$filter,$functionName],$request,$response) ) {
                if (null == $this->getErrorCode()) {
                    $this->setError(0,"过滤器拦截");
                }
                return false;
            }
        }
        return true;
    }

    /**
     * 过滤器拦截错误信息码
     */
    public function getErrorCode() {
        return self::$errorCode;
    }

    protected function setErrorCode($errorCode) {
        self::$errorCode = $errorCode;
    }

    /**
     * 过滤器拦截错误信息
     */
    public function getErrorMessage(){
        return self::$errorMessage;
    }

    protected function setErrorMessage($errorMessage){
        self::$errorMessage = $errorMessage;
    }

    protected function setError( $code, $message){
        $this->setErrorCode( $code ) ;
        $this->setErrorMessage( $message );
    }

}