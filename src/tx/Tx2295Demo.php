<?php

include 'TxDemo.php';
include 'TxClient.php';
include '../config/DemoConfig.php';
include '../util/DemoSM2Utils.php';
include '../util/TxUtils.php';
include '../vo/BizParameter.php';


require_once __DIR__ . '/../../vendor/autoload.php';

use config\DemoConfig;
use vo\BizParameter;
/**
 * 【订单关闭】
 *
 * @author 邵鉴
 * @version 1.0
 * @date 2024/10/22
 */
$demoConfig = new DemoConfig();

$bizParameter = new BizParameter();

$bizParameter->addParam("orgRespTraceNum", "20240229230140208901650174");

// 发送请求
$txClient = new TxClient($demoConfig);
$txResponse = $txClient->sendRequest("2295", $bizParameter, $demoConfig->getUrl());

// 打印响应结果
echo PHP_EOL."响应结果: ".$txResponse->bizData;