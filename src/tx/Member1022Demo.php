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

// Framework: Custom PHP application

/**
 * 会员资料补录测试demo
 *
 * @author gejunqing
 * @version 1.0
 * @date 2024/1/11
 */
class Member1022Demo extends TxDemo
{
    public static function main()
    {
        new Member1022Demo();
        $demoConfig = self::$demoConfig;
        // 组装参数
        $bizParameter = new BizParameter();
        $bizParameter->addParam("reqTraceNum", TxUtils::genReqTraceNum());
        // $businessParameter->addParam("appId", "21754330162443890690");
        $bizParameter->addParam("signNum", "busi1");
        $bizParameter->addParam("notifyUrl", "http://test.allinpay.com/open/testNotify");

        $bizParameter->addParam("legpCerFront", "3320240305201764992937885954050"); // 法人身份证（肖像面）

        $bizParameter->addParam("legpCerBack", "3320240305201764993022011109378"); // 法人身份证（国徽面）

        $bizParameter->addParam("unifiedSocialCredit", "3320240305201764992483856740353"); // 统一信用证

        $bizParameter->addParam("otherPhotocopyType", "8"); // 其他影印件类型

        $bizParameter->addParam("photocopyToken", "3320240305201764992483856740353"); // 影印件图片文件

        // 发送请求
        $txClient = new TxClient($demoConfig);
        $txResponse = $txClient->sendRequest("1022", $bizParameter, $demoConfig->getMemberUrl());
        // 打印响应结果
        echo PHP_EOL . "响应结果: " . $txResponse->bizData;
    }
}
(new Member1022Demo())->main();
