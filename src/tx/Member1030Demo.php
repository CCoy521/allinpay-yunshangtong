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

use util\DemoSM4Utils;
use util\TxUtils;
use vo\BizParameter;
use TxClient;

/**
 * 会员绑定手机号测试demo
 *
 * @author 邵鉴
 * @version 1.0
 * @date 2024/10/22
 */
class Member1030Demo extends TxDemo
{
    public static function main()
    {
        new Member1030Demo();
        $demoConfig = self::$demoConfig;
        // 组装参数
        $bizParameter = new BizParameter();
        $bizParameter->addParam("reqTraceNum", TxUtils::genReqTraceNum());
        $bizParameter->addParam("signNum", "Tc40200008");
        $bizParameter->addParam("phone", "");
        $bizParameter->addParam("phoneType", "1");  //2-被授权人，1-法人

        $authPerInfo = [];
        $authPerInfo["authPerName"] = "";
        $authPerInfo["authPerCerType"] = "1";
        $authPerInfo["authPerCerNum"] = DemoSM4Utils::encryptEcb($demoConfig->getSecretKey(), "");
        $authPerInfo["authPerPhone"] = "";
        $bizParameter->addMapParam("authPerInfo", $authPerInfo);
        // 发送请求
        $txClient = new TxClient($demoConfig);
        $txResponse = $txClient->sendRequest("1030", $bizParameter, $demoConfig->getMemberUrl());
        // 打印响应结果
        echo PHP_EOL . "响应结果: " . $txResponse->bizData;
    }
}

Member1030Demo::main();