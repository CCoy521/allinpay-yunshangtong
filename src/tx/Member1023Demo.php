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

class Member1023Demo extends TxDemo
{
    public static function main()
    {
        new Member1023Demo();
        $demoConfig = self::$demoConfig;
        // 组装参数
        $bizParameter = new BizParameter();
        $bizParameter->addParam("reqTraceNum", TxUtils::genReqTraceNum());
        $bizParameter->addParam("signNum", "#yunBizUserId_B2C#"); //#yunBizUserId_B2C#
        //$bizParam = "{\\\"reqTraceNum\\\":\\\"".TxUtils::genReqTraceNum()."\\\",\\\"signNum\\\":\\\"#yunBizUserId_B2C#\\\"}";
        // 发送请求
        $txClient = new TxClient($demoConfig);
        $txResponse = $txClient->sendRequest("1023", $bizParameter, $demoConfig->getMemberUrl());
        // 打印响应结果
        echo PHP_EOL . "响应结果: " . $txResponse->bizData;
    }
}

Member1023Demo::main();