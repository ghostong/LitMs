<?php
/**
 * 自定义过滤器
 */

class Filter extends Lit\LitMs\LitMsFilter {

    function rule1 ( $request, $response ) {
        return true;
    }

//    function rule2 ( $request,$response ) {
//        $this->setError(1,__FUNCTION__);
//        return false;
//    }

}