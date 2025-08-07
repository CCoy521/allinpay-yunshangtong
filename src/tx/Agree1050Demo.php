<?php
namespace tx;
include 'TxDemo.php';
//include '../config/DemoConfig.php';
include '../util/DemoSM2Utils.php';
include '../util/DemoSM4Utils.php';
include '../util/TxUtils.php';
include '../vo/BizParameter.php';
include '../tx/TxClient.php';

require_once __DIR__ . '/../../vendor/autoload.php';

use tx\TxDemo;
use TxClient;
use util\DemoSM4Utils;
use util\TxUtils;
use vo\BizParameter;

/**
 * 线上协议签约demo
 * @author 邵鉴
 * @version 1.0
 * @date 2024/10/22
 */
class Agree1050Demo extends TxDemo
{
    public static function main()
    {
        new Agree1050Demo();
        $demoConfig = self::$demoConfig;
        // 组装参数
        $bizParameter = new BizParameter();

        $bizParameter->addParam("reqTraceNum", TxUtils::genReqTraceNum());
        $bizParameter->addParam("signNum", "Tn40200005");
        $bizParameter->addParam("memberName", "竹溪县子怡鞋店");
        $bizParameter->addParam("agreementType", "2");  //1 簿记账户  2 支付账户
        $bizParameter->addParam("agreementJson", "{\"cusId\":\"6601000152000Q8\",\"payAcctNo\":\"402154845096901\"}");
        //$bizParameter->addParam("agreementType", "2");
        //$bizParameter->addParam("agreementJson", "{\"cusId\":\"123\",\"payAcctNo\":\"123\"}");
        $bizParameter->addParam("jumpPageType", "1");
        $bizParameter->addParam("jumpUrl", "http://www.baidu.com");
        $bizParameter->addParam("notifyUrl", "http://test.allinpay.com/open/testNotify");
        // 发送请求
        $txClient = new TxClient($demoConfig);
        $txResponse = $txClient->sendRequest("1050", $bizParameter, $demoConfig->getMemberUrl());
        // 打印响应结果
        echo PHP_EOL . "响应结果: " . $txResponse->bizData;
    }
}

Agree1050Demo::main();