<?php
/**
 * 定时器基础类
 */

namespace Lit\LitMs;

class LitMsSchedule{

    /**
     * 循环, 每隔X秒执行一次, 阻塞, 如果回调函数执行太慢, 将在函数执行完后重新计时.
     * @param integer $second 间隔秒
     * @param callable $callback 回调函数
     */
    public static function loop( $second, callable $callback ) {
        $millisecond = $second * 1000;
        \Swoole\Timer::tick( $millisecond, $callback );
    }

    /**
     * X秒后执行一次, 每次启动后会生效.
     * @param integer $second 间隔秒
     * @param callable $callback 回调函数
     */
    public static function after( $second, callable $callback ){
        $millisecond = $second * 1000;
        \Swoole\Timer::after( $millisecond, $callback );
    }

    /**
     * 每隔X秒执行一次, 非阻塞, 如果没有执行完也会启动新的定时器.
     * @param integer $second 间隔秒
     * @param callable $callback 回调函数
     */
    public static function every( $second, callable $callback ) {
        $millisecond = $second * 1000;
        \Swoole\Timer::after( $millisecond, function() use ( $second, $callback ){
            self::every( $second, $callback );
            call_user_func( $callback );
        });
    }

    /**
     * 在指定时间执行一次, 如果时间过期将不执行
     * @param String $dateTime 指定时间
     * @param callable $callback 回调函数
     */
    public static function at( $dateTime, callable $callback ){
        $second = strtotime($dateTime) - time();
        if ( $second > 0 ) {
            self::after( $second, $callback );
        }else{
            self::outError("At schedule expired ({$dateTime}); Ignore ... ");
        }
    }

    /**
     * @param $minute
     * @param $hour
     * @param $day
     * @param $month
     * @param $week
     * @param callable $callback
     */
//    public static function atEvery( $minute, $hour, $day, $month, $week, callable $callback ){
//        $millisecond = self::getNextTime( $minute, $hour, $day, $month, $week );
//        var_dump($millisecond);
////        \Swoole\Timer::after( $millisecond, function() use ( $minute, $hour, $day, $month, $week, $callback ){
////            self::atEvery( $minute, $hour, $day, $month, $week, $callback );
////            call_user_func( $callback );
////        });
//    }

    /**
     * @param $minute
     * @param $hour
     * @param $day
     * @param $month
     * @param $week
     * @return int
     */
//    private static function getNextTime( $minute, $hour, $day, $month, $week ){
//        $time = time();
//        if ( trim($minute) == "*" ) {
//            $min = date( "i",$time+60 );
//        }else{
//            $exp = explode("/",$minute);
//            $min = end($exp);
//            $min = floor( date("i", $time + ($min * 60) ) / $min ) * $min ;
//        }
//
//        if ( trim($hour) == "*" ) {
//            $h = date( "H",$time );
//        }else{
//            $exp = explode("/",$hour);
//            $h = end($exp);
//            $h = floor( date("i", $time + ($h * 60 * 60) ) / $h ) * $h ;
//        }
//
//        return $h.":".$min;
//    }

    /**
     * 错误输出
     * @param String $error 错误信息
     */
    private static function outError( $error ){
        self::after( 1, function () use ( $error ) {
            echo $error,PHP_EOL;
        });
    }

}