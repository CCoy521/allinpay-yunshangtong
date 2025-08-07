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
use TxClient;
use util\TxUtils;
use vo\BizParameter;
/**
 * 【订单状态查询】
 *
 * @author 邵鉴
 * @version 1.0
 * @date 2024/10/22
 */
class Tq3001Demo extends TxDemo
{
    public static function main()
    {
        new Tq3001Demo();
        $demoConfig = self::$demoConfig;
        $logger = self::$logger;
        // 组装参数
        $bizParameter = new BizParameter();
        $bizParameter->addParam("respTraceNum", "20240328175954208901135731");
        // 发送请求
        $txClient = new TxClient($demoConfig);
        $txResponse = $txClient->sendRequest("3001", $bizParameter, $demoConfig->getQueryUrl());
        // 打印响应结果
        echo PHP_EOL . "响应结果: " . $txResponse->bizData;
    }
}

Tq3001Demo::main();