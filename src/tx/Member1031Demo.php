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
 * 会员绑定收银宝商户测试demo
 *
 * @author 邵鉴
 * @version 1.0
 * @date 2024/10/22
 */
class Member1031Demo extends TxDemo
{
    public static function main()
    {
        new Member1031Demo();
        $demoConfig = self::$demoConfig;
        // 组装参数
        $businessParameter = new BizParameter();
        $businessParameter->addParam("reqTraceNum", TxUtils::genReqTraceNum());
        $businessParameter->addParam("signNum", "418001");
        // $businessParameter->addParam("signNum", "2024020524");
        $businessParameter->addParam("oriPhone", "");

        // 发送请求
        $transactionClient = new TxClient($demoConfig);
        $transactionResponse = $transactionClient->sendRequest("1031", $businessParameter, $demoConfig->getMemberUrl());
        // 打印响应结果
        echo PHP_EOL . "响应结果: " . $transactionResponse->bizData;
    }
}
Member1031Demo::main();

