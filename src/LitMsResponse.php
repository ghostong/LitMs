<?php

namespace Lit\Ms;

class LitMsResponse {
    private $success = 1;
    private $responseCode = 1;
    private $responseMessage = "success";
    private $response = [];
    private $format = "json";
    private $statusCode = 200;
    private $htmlString = "";
    private $outString = "";

    /**
     * 成功接口
     * @param array $param
     * @return LitMsResponse
     **/
    public function success( array $param = [] ) {
        $this->success = 1;
        $this->responseCode = 1;
        $this->responseMessage = "success";
        $this->response = $param ?: [];
        return $this;
    }

    /**
     * 失败接口
     * @param int $code
     * @param string $message
     * @return LitMsResponse
     */
    public function error( int $code = 0, string $message = ""): LitMsResponse {
        $this->success = 0;
        $this->responseCode = $code;
        $this->responseMessage = $message;
        $this->response = [];
        return $this;
    }

    /**
     * HTML页面
     * @param $htmlFileName
     * @return LitMsResponse
     */
    public function html ( string $htmlFileName ): LitMsResponse {
        $htmlFileName = str_replace("..","",$htmlFileName);
        $this->setFormat("html");
        $this->setHtml( @file_get_contents(LITMS_WORK_DIR.DIRECTORY_SEPARATOR."View".DIRECTORY_SEPARATOR.$htmlFileName) );
        return $this;
    }

    /**
     * 字符串返回
     * @param string $string
     * @return LitMsResponse
     */
    public function string ( string $string ): LitMsResponse {
        $this->setFormat("string");
        $this->setString( $string );
        return $this;
    }

    /**
     * 获取Http状态码
     */
    public function getStatusCode(){
        return $this->statusCode;
    }

    /**
     * 设置Http状态码
     * @param int $code
     * @return LitMsResponse
     */
    public function setStatusCode( int $code ): LitMsResponse {
        $this->statusCode =  $code;
        return $this;
    }

    /**
     * 设置输出格式
     * @param $format
     * @return LitMsResponse
     */
    public function setFormat ( $format ): LitMsResponse {
        $this->format = $format;
        return $this;
    }

    /**
     * 获取输出格式
     */
    public function getFormat () {
        return $this->format;
    }

    /**
     * 设置输出html
     * @param $htmlString
     * @return LitMsResponse
     */
    public function setHtml ( $htmlString ): LitMsResponse {
        $this->htmlString = $htmlString;
        return $this;
    }

    /**
     * 获取输出html
     */
    public function getHtml () {
        return $this->htmlString;
    }

    /**
     * 设置输出字符串
     * @param $outString
     * @return LitMsResponse
     */
    public function setString ( $outString ): LitMsResponse {
        $this->outString = $outString;
        return $this;
    }

    /**
     * 获取输出字符串
     */
    public function getString () {
        return $this->outString;
    }

    /**
     * toString
     */
    function __toString(): string {
        $toStringArray = array(
            "success"         => $this->success,
            "responseCode"    => $this->responseCode,
            "responseMessage" => $this->responseMessage,
            "response"        => $this->response,
            "timestamp"       => time()
        );
        if ( $this->format == "json" ) {
            return json_encode( $toStringArray );
        } elseif ( $this->format == "html" ) {
            return $this->getHtml();
        } elseif ( $this->format == "string" ) {
            return $this->getString();
        }else{
            return print_r( $toStringArray, true );
        }
    }
}