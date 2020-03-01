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
     * @param string $name Schedule名称
     */
    public static function loop( $second, callable $callback, $name = ""  ) {
        $millisecond = $second * 1000;
        \Swoole\Timer::tick( $millisecond, function() use( $callback ,$name ){
            if ($name){
                self::outMessage( $name." start !" );
            }
            call_user_func($callback);
        });
    }

    /**
     * X秒后执行一次, 每次启动后会生效.
     * @param integer $second 间隔秒
     * @param callable $callback 回调函数
     * @param string $name Schedule名称
     */
    public static function after( $second, callable $callback, $name = "" ){
        $millisecond = $second * 1000;
        \Swoole\Timer::after( $millisecond, function() use( $callback ,$name ) {
            if ($name){
                self::outMessage( $name." start !" );
            }
            call_user_func($callback);
        });
    }

    /**
     * 每隔X秒执行一次, 非阻塞, 如果没有执行完也会启动新的定时器.
     * @param integer $second 间隔秒
     * @param callable $callback 回调函数
     * @param string $name Schedule名称
     */
    public static function every( $second, callable $callback, $name = "" ) {
        $millisecond = $second * 1000;
        \Swoole\Timer::after( $millisecond, function() use ( $second, $callback, $name ){
            self::every( $second, $callback,$name );
            if ($name){
                self::outMessage( $name." start !" );
            }
            call_user_func( $callback );
        });
    }

    /**
     * 在指定时间执行一次, 如果时间过期将不执行
     * @param String $dateTime 指定时间
     * @param callable $callback 回调函数
     * @param string $name Schedule名称
     */
    public static function at( $dateTime, callable $callback, $name = "" ){
        $second = strtotime($dateTime) - time();
        if ( $second > 0 ) {
            self::after( $second, $callback, $name );
        }else{
            self::outMessage("At schedule expired ({$dateTime}); Ignore ... ");
        }
    }

    /**
     * @param $minute 分
     * @param $hour 时
     * @param $day 日
     * @param $month 月
     * @param $week 周
     * @param callable $callback 回调函数
     * @param string $name Schedule名称
     */
    public static function cron( $minute, $hour, $day, $month, $week, callable $callback, $name = "" ){
        $nextCron = self::getNextCron() * 1000;
        \Swoole\Timer::after( $nextCron, function () use ( $minute, $hour, $day, $month, $week, $callback, $name ) {
            if ( self::canRun ( $minute, $hour, $day, $month, $week, time() ) ) {
                \Swoole\Timer::after(10,function () use ( $callback, $name ) {
                    if ($name){
                        self::outMessage( $name." start !" );
                    }
                    call_user_func( $callback );
                }) ;
            }
            self::cron(  $minute, $hour, $day, $month, $week, $callback, $name );
        });
    }

    //下次Cron执行时间
    private static function getNextCron(){
        $now = time();
        return strtotime( date ( "Y-m-d H:i:00",$now + 60 ) ) - $now;
    }

    //判断时间是否可以执行
    private static function canRun( $minute, $hour, $day, $month, $week, $now ) {
        if (
            self::timeCan( $minute, date("i",$now) ) && self::timeCan( $hour, date("H",$now) ) &&
            self::timeCan( $day, date("d",$now) ) && self::timeCan( $month, date("m",$now) ) &&
            self::timeCan( $week, date("w",$now) ? : 7 )
        ){
            return true;
        }else{
            return false;
        }
    }

    //分钟是否允许
    private static function timeCan( $runTime, $formatTime ) {
        if ( "*" == $runTime ) {
            return true;
        }
        if( $runTime == $formatTime ) {
            return true;
        }
        if( stripos($runTime,"-") !== false ) {
            $minExp = explode("-",$runTime);
            sort ( $minExp );
            if ( $minExp[0] <= $formatTime && $formatTime <= $minExp[1] ) {
                return true;
            }
        }
        if( stripos($runTime,"/") !== false ) {
            $minExp = explode("/", $runTime);
            if ($formatTime % $minExp[1] == 0) {
                return true;
            }
        }
        return false;
    }

    /**
     * 错误输出
     * @param String $msg 错误信息
     */
    private static function outMessage( $msg ){
        echo "[".date("Y-m-d H:i:s")."] ".$msg,PHP_EOL;
    }

}