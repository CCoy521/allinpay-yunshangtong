<?php

include 'TxDemo.php';
include 'TxClient.php';
include '../config/DemoConfig.php';
include '../util/DemoSM2Utils.php';
include '../util/TxUtils.php';
include '../vo/BizParameter.php';


require_once __DIR__ . '/../../vendor/autoload.php';

use config\DemoConfig;
use util\TxUtils;
use vo\BizParameter;
/**
 * 【退款申请】
 *
 * @author 邵鉴
 * @version 1.0
 * @date 2024/10/22
 */
$demoConfig = new DemoConfig();

$bizParameter = new BizParameter();

$bizParameter->addParam("orgRespTraceNum", "20240626180321208501631603");
$bizParameter->addParam("reqTraceNum", "wxl" . TxUtils::genReqTraceNum());
$bizParameter->addParam("orgRespTraceNum", "20240626180321208501631603");
$bizParameter->addParam("orderAmount", 1);
//        bizParameter.addListParam("refundDetail", refundDetail);
$bizParameter->addParam("respUrl", "http://test.allinpay.com/open/testNotify");
// $bizParameter->addParam("chnlDiscAmt", "{\"orderInfo\":\"abcdefghiii\"}");//优惠信息：银联云闪付单品
// $bizParameter->addParam("extendParams", "{\"extend\":\"abcdefghiii\"}");

$refundDetail = array([
    "signNum" => "9665wxl202400008",
    "orderAmount" => 4,
    "acctType" => 1,
    "couponAmount" => 1,
//    "signNum" => "9665wxl202400006",
//    "orderAmount" => 2,
//    "sepDetail" => json_encode([
//        "sepDetail" => json_encode([
//            "signNum"=>"2705wxl00002",
//             "amount"=>"1"
//        ])
//    ])
]);
$bizParameter->addListParam("refundDetail", $refundDetail);//订单退款列表

// 发送请求
$txClient = new TxClient($demoConfig);
$txResponse = $txClient->sendRequest("2294", $bizParameter, $demoConfig->getUrl());

// 打印响应结果
echo PHP_EOL."响应结果: ".$txResponse->bizData;