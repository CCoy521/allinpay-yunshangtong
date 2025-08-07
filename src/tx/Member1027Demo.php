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
 * 测试demo
 * 企业会员实名开户
 * @author 邵鉴
 * @version 1.0
 * @date 2024/10/22
 */
class Member1027Demo extends TxDemo
{
    public static function main()
    {
        new Member1027Demo();
        $demoConfig = self::$demoConfig;
        // 组装参数
        $bizParameter = new BizParameter();

        $bizParameter->addParam("reqTraceNum", TxUtils::genReqTraceNum());
        $bizParameter->addParam("signNum", "Tc40200008");
        $bizParameter->addParam("notifyUrl", "http://test.allinpay.com/open/testNotify/");
        $bizParameter->addParam("infoType","5");
        //  1-基本信息 2-银行账户信息 3-协议信息 4-影印件ocr核对信息    5-绑定手机号信息
        // 发送请求
        $txClient = new TxClient($demoConfig);
        $txResponse = $txClient->sendRequest("1027", $bizParameter, $demoConfig->getMemberUrl());
        // 打印响应结果
        echo PHP_EOL . "响应结果: " . $txResponse->bizData;
    }
}

Member1027Demo::main();