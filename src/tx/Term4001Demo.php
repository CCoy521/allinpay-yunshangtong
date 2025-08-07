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

$demoConfig = new DemoConfig();

$bizParameter = new BizParameter();
$bizParameter->addParam("orgId", "55058404816VQJW");
$bizParameter->addParam("cusId", "55058404816VQLX");
$bizParameter->addParam("termNo", "12345678");
$bizParameter->addParam("operation", "03");//00：新增； 01：修改； 02：注销； 03：查询
$bizParameter->addParam("deviceType", "10");
$bizParameter->addParam("termSn", "123");
$bizParameter->addParam("termState", "00");//
$bizParameter->addParam("termAddress", "上海市-上海市-浦东新区-五星路");
$bizParameter->addParam("queryType", "TUA");
// 发送请求
$txClient = new TxClient($demoConfig);
$txResponse = $txClient->sendRequest("4001", $bizParameter, $demoConfig->getMemberUrl());

// 打印响应结果
echo PHP_EOL."响应结果: ".$txResponse->bizData;