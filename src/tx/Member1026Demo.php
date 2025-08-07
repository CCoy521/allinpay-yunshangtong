<?php
namespace tx;

include 'TxDemo.php';
include '../config/DemoConfig.php';
include '../util/DemoSM2Utils.php';
include '../util/TxUtils.php';
include '../vo/BizParameter.php';
include '../tx/TxClient.php';

require_once __DIR__ . '/../../vendor/autoload.php';

use tx\TxDemo;
use util\TxUtils;
use vo\BizParameter;
use TxClient;
/**
 *
 * 平台余额查询接口
 * @version 1.0
 * @date 2024/1/11
 */
class Member1026Demo extends TxDemo
{
    public static function main()
    {
        new Member1026Demo();
        $demoConfig = self::$demoConfig;
        // 组装参数
        $bizParameter = new BizParameter();
        $bizParameter->addParam("reqTraceNum", TxUtils::genReqTraceNum());
        /*     $bizParameter->addParam("signNum", "JACK20240322001");
             $bizParameter->addParam("memberRole", "二级商户");
             $bizParameter->addParam("enterpriseNature", "2");
             $bizParameter->addParam("notifyUrl", "http://www.baidu.com/");*/

        //收付通
        $bizParameter->addParam("qryType","1");

        //收银宝
        /*   $bizParameter->addParam("qryType","2");
           $bizParameter->addParam("cusId","552290058118Y2J");//*/



        $txClient = new TxClient($demoConfig);
        $txResponse = $txClient->sendRequest("1026", $bizParameter, $demoConfig->getMemberUrl());
        // 打印响应结果
        echo PHP_EOL . "响应结果: " . $txResponse->bizData;
    }
}

Member1026Demo::main();