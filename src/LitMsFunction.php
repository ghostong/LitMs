<?php
//载入模块
function Model ( $modelName, $instantiate = false ) {
    $className = $modelName."Model";
    if($instantiate) {
        return new $className;
    }else{
        return $className::getInstance();
    }
}

//载入视图
function View ( $htmlFileName  ) {
    $htmlFileName = str_replace("..","",$htmlFileName);
    return @file_get_contents(LITMS_WORK_DIR.DIRECTORY_SEPARATOR."View".DIRECTORY_SEPARATOR.$htmlFileName);
}

//成功接口
function Success(  Array $param = [] ){
    $outPut["success"] = 1;
    $outPut["responseCode"] = 1;
    $outPut["responseMessage"] =  "成功";
    $outPut["response"] = $param ? : [];
    return OutPut($outPut);
}

//失败接口
function Error( $code = 0 ,$message = "" ){
    $outPut["success"] = 0;
    $outPut["responseCode"] = (int) $code;
    $outPut["responseMessage"] = $message ? : ( ErrorMsg($code) ? : "失败" );
    $outPut["response"] = [];
    return OutPut($outPut);
}

//输出
function OutPut( Array $outPut ){
    $outPut["timestamp"] = time();
    return json_encode( $outPut );
}

//错误码
function ErrorMsg($code){
    $ErrorCode[403] = "Forbidden";
    $ErrorCode[404] = "Not Found";
    $ErrorCode[405] = "Method Not Allow";
    return isset($ErrorCode[$code]) ? $ErrorCode[$code] : "";
}

//简单身份认证
function EasyAuthenticate( $request, $authDict ){
    if (isset($request->header["authorization"])) {
        $authorStr = $request->header["authorization"];
        $expAuthor = explode(" ",$authorStr);
        $queryVerify = end($expAuthor);
        foreach($authDict as $userName => $passWord){
            $verify = base64_encode($userName.":".$passWord);
            if ($verify === $queryVerify){
                return true;
            }
        }
    }
    return false;
}