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
use util\TxUtils;
use vo\BizParameter;
use TxClient;

$demoConfig = new DemoConfig();

$bizParameter = new BizParameter();
$bizParameter->addParam("reqTraceNum", TxUtils::genReqTraceNum());
$bizParameter->addParam("signNum", "Tc40200008");
$bizParameter->addParam("phone", "");
$bizParameter->addParam("applyRespTraceNum", "20240402190453103000226561");
$bizParameter->addParam("verifyCode", "342556");

// 发送请求
$txClient = new TxClient($demoConfig);
$txResponse = $txClient->sendRequest("1032", $bizParameter, $demoConfig->getMemberUrl());

// 打印响应结果
echo PHP_EOL."响应结果: ".$txResponse->bizData;