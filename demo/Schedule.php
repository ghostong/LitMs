<?php
/**
 * LitMs 定时任务
 */

////每x秒执行一次
//Lit\LitMs\LitMsSchedule::every(2,function(){
//    echo "every 2 second \n";
//});
//
////每x秒执行一次
//Lit\LitMs\LitMsSchedule::loop(2,function(){
//    echo "loop 2 second \n";
//});
//
////启动后x秒执行一次
//Lit\LitMs\LitMsSchedule::after( 10, function (){
//    echo "after 10 second \n";
//});
//
////在 Y-m-d H:i:s 执行一次
//Lit\LitMs\LitMsSchedule::at( "2020-03-01 01:30:40", function (){
//    echo "at ".date("Y-m-d H:i:s")."\n";
//});

Lit\LitMs\LitMsSchedule::atEvery("*/5","*","*","*","*", function (){

});