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
 * 个人会员实名及绑卡确认
 *
 * @author gejunqing
 * @version 1.0
 * @date 2024/1/11
 */
class Member1011Demo extends TxDemo
{
    public static function main()
    {
        new Member1011Demo();
        $demoConfig = self::$demoConfig;
        // 组装参数
        $bizParameter = new BizParameter();
        // 发送请求
        $bizParameter->addParam("reqTraceNum", TxUtils::genReqTraceNum());
        $bizParameter->addParam("signNum", "2705wxl00001");
        $bizParameter->addParam("applyRespTraceNum", "20240304111308101000950414");
        $bizParameter->addParam("phone", "15201933462");
        // $businessParameter->addParam("validDate", "1230");
        // $businessParameter->addParam("cvv2", "463");
        $bizParameter->addParam("verifyCode", "277733");

        $txClient = new TxClient($demoConfig);
        $txResponse = $txClient->sendRequest("1011", $bizParameter, $demoConfig->getMemberUrl());
        // 打印响应结果
        echo PHP_EOL."响应结果: ".$txResponse->bizData;
    }
}
(new Member1011Demo())->main();