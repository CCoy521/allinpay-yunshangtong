<?php
namespace tx;

include 'TxDemo.php';
include 'TxClient.php';
include '../config/DemoConfig.php';
include '../util/DemoSM2Utils.php';
include '../util/TxUtils.php';
include '../vo/BizParameter.php';


require_once __DIR__ . '/../../vendor/autoload.php';

use config\DemoConfig;
use TxClient;
use vo\BizParameter;
/**
 * 订单确认demo
 *【确认支付（后台+短信）】
 *
 * @author 邵鉴
 * @version 1.0
 * @date 2024/10/22
 */
$demoConfig = new DemoConfig();

$bizParameter = new BizParameter();
$bizParameter->addParam("respTraceNum", "20240228171459208501776449");
$bizParameter->addParam("verifyCode", "111111");
// 发送请求
$txClient = new TxClient($demoConfig);
$txResponse = $txClient->sendRequest("4003", $bizParameter, $demoConfig->getQueryUrl());

// 打印响应结果
echo PHP_EOL."响应结果: ".$txResponse->bizData;