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
 *  线下协议上传demo
 * @version 1.0
 * @date 2024/3/19
 */
class Agree1051Demo extends TxDemo
{
    public static function main()
    {
        new Agree1051Demo();
        $demoConfig = self::$demoConfig;
        // 组装参数
        $bizParameter = new BizParameter();
        
        $bizParameter->addParam("reqTraceNum", TxUtils::genReqTraceNum());
        $bizParameter->addParam("signNum", "Tc40200008");
        $bizParameter->addParam("memberName", "竹溪县子怡鞋店");
        $bizParameter->addParam("agreementType", "2");
        $bizParameter->addParam("agreementJson", "{\"payAcctNoOpenAgreeToken\":\"3320240327141772874573646393345\",\"coopConfirmToken\":\"3320240327141772874286441426946\",\"nonNatureCusBenefitToken\":\"3320240327141772874286441426946\"}");
        $bizParameter->addParam("notifyUrl", "http://test.allinpay.com/open/testNotify");
        // 发送请求
        $txClient = new TxClient($demoConfig);
        $txResponse = $txClient->sendRequest("1050", $bizParameter, $demoConfig->getMemberUrl());
        // 打印响应结果
        echo PHP_EOL . "响应结果: " . $txResponse->bizData;
    }
}
(new Agree1051Demo())->main();