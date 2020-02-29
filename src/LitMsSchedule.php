<?php
/**
 * Created by IntelliJ IDEA.
 * User: ghost
 * Date: 2020-03-01
 * Time: 00:31
 */

namespace Lit\LitMs;


class LitMsSchedule{

    private static $isSchedule = false;

    public static function every( $second, $callback ) {
        $millisecond = $second * 1000;
        \Swoole\Timer::tick( $millisecond, $callback );
        self::setSchedule();
    }

    public static function after ( $second, $callback ){
        $millisecond = $second * 1000;
        \Swoole\Timer::after( $millisecond, $callback );
        self::setSchedule();
    }

    public function at( $dateTime, $callback ){
        $second = strtotime($dateTime) - time();
        if ($second > 0) {
            self::after($second, $callback);
        }else{
            self::outError("at schedule expired ({$dateTime}); ignore ... ");
        }
    }

    private static function outError( $error ){
        \Swoole\Timer::after( 1000, function () use ( $error ) {
            echo $error,PHP_EOL;
        });
    }

    private static function setSchedule(){
        self::$isSchedule = true;
    }

    public static function isSchedule () {
        return self::$isSchedule;
    }

}