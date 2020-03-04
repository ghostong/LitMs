<?php
/**
 * 终端绘图类
 */
namespace Lit\Ms;


class LitMsTerminalDraw{

    //Http 欢迎画面
    public static function httpServerWelcome ( $terminalWidth, $sslConnect, $httpHost, $httpPort ){
        $outPut = "";
        $outPut .= self::terminalDrawLine( $terminalWidth );
        $outPut .= self::terminalDrawRow("+      _       _   _     __  __            +",$terminalWidth,"middle");
        $outPut .= self::terminalDrawRow("|     | |     (_) | |_  |  \/  |  ___      |",$terminalWidth,"middle");
        $outPut .= self::terminalDrawRow("|     | |     | | | __| | |\/| | / __|     |",$terminalWidth,"middle");
        $outPut .= self::terminalDrawRow("|     | |___  | | | |_  | |  | | \__ \     |",$terminalWidth,"middle");
        $outPut .= self::terminalDrawRow(".     |_____| |_|  \__| |_|  |_| |___/     .",$terminalWidth,"middle");
        $outPut .= self::terminalDrawRow(" ",$terminalWidth,"middle");
        $outPut .= self::terminalDrawLine($terminalWidth);
        $protocol = $sslConnect?"https":"http";
        foreach(@swoole_get_local_ip() as $ip) {
            $outPut .= self::terminalDrawRow("With generate: {$protocol}://".$ip.":".$httpPort,$terminalWidth,"left");
        }
        $httpHost = "With config: {$protocol}://".$httpHost.":".$httpPort;
        $outPut .= self::terminalDrawRow($httpHost,$terminalWidth,"left");
        $outPut .= self::terminalDrawLine($terminalWidth);
        $outPut .= self::terminalDrawRow(" Power By Ghost ",$terminalWidth,"right");
        $outPut .= self::terminalDrawRow("ghostong@126.com",$terminalWidth,"right");
        $outPut .= self::terminalDrawLine($terminalWidth);
        echo $outPut;
    }

    //Schedule 欢迎画面
    public static function scheduleWelcome ( $terminalWidth ){
        $outPut = "";
        $outPut .= self::terminalDrawLine( $terminalWidth );
        $outPut .= self::terminalDrawRow("+      _       _   _     __  __            +",$terminalWidth,"middle");
        $outPut .= self::terminalDrawRow("|     | |     (_) | |_  |  \/  |  ___      |",$terminalWidth,"middle");
        $outPut .= self::terminalDrawRow("|     | |     | | | __| | |\/| | / __|     |",$terminalWidth,"middle");
        $outPut .= self::terminalDrawRow("|     | |___  | | | |_  | |  | | \__ \     |",$terminalWidth,"middle");
        $outPut .= self::terminalDrawRow(".     |_____| |_|  \__| |_|  |_| |___/     .",$terminalWidth,"middle");
        $outPut .= self::terminalDrawRow(" ",$terminalWidth,"middle");
        $outPut .= self::terminalDrawLine($terminalWidth);
        $outPut .= self::terminalDrawRow("Schedule Model ",$terminalWidth,"middle");
        $outPut .= self::terminalDrawLine($terminalWidth);
        $outPut .= self::terminalDrawRow(" Power By Ghost ",$terminalWidth,"right");
        $outPut .= self::terminalDrawRow("ghostong@126.com",$terminalWidth,"right");
        $outPut .= self::terminalDrawLine($terminalWidth);
        echo $outPut;
    }


    //终端线
    public static function terminalDrawLine( $width ){
        $start = "+";
        $end = "+";
        $line = $start . str_repeat("-",$width-2) . $end .PHP_EOL;
        return $line;
    }

    //终端行
    public static function terminalDrawRow( $str, $width , $margin){
        $start = "|";
        $end = "|";
        if(strlen($str)+4 >= $width){
            $str = substr($str,0,$width-4);
        }
        if ($margin == "right") {
            $str = $str." ";
            $line = $start.str_repeat(" ",$width-2-strlen($str)).$str.$end.PHP_EOL;
        }elseif($margin == "middle"){
            $line = $start.str_repeat(" ",ceil(($width-2-strlen($str))/2)).$str.str_repeat(" ",floor(($width-2-strlen($str))/2)).$end.PHP_EOL;
        }else{
            $str = " ".$str;
            $line = $start.$str.str_repeat(" ",$width-2-strlen($str)).$end.PHP_EOL;
        }
        return $line;
    }

}