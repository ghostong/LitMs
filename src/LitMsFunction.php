<?php
function Model ( $modelName ) {
    $className = $modelName."Model";
    if(class_exists($className)){
        return new $className;
    }else{
        return Error(50101);
    }
}

function View ( $htmlFileName  ) {
    $htmlFileName = str_replace("..","",$htmlFileName);
    return @file_get_contents(WORK_DIR.DIRECTORY_SEPARATOR."View".DIRECTORY_SEPARATOR.$htmlFileName);
}

function Success(  Array $param = [] ){
    $outPut["success"] = 1;
    $outPut["responseCode"] = 1;
    $outPut["responseMessage"] =  "成功";
    $outPut["response"] = $param ? : [];
    return OutPut($outPut);
}

function Error( $code = 0 ,$message = "" ){
    $outPut["success"] = 0;
    $outPut["responseCode"] = (int) $code;
    $outPut["responseMessage"] = $message ? : ( ErrorCode($code) ? : "失败" );
    $outPut["response"] = [];
    return OutPut($outPut);
}

function OutPut( Array $outPut ){
    $outPut["timestamp"] = time();
    return json_encode( $outPut );
}

function ErrorCode($code){
    $ErrorCode[404] = "Not Found";
    $ErrorCode[405] = "Method Not Allow";

    $ErrorCode[50100] = "Model中方法不存在";
    $ErrorCode[50101] = "Model名不存在";


    return isset($ErrorCode[$code]) ? $ErrorCode[$code] : "";
}