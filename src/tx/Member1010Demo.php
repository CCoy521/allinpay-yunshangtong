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
 * 个人会员实名及绑卡申请
 * @author 邵鉴
 * @version 1.0
 * @date 2024/10/22
 */
class Member1010Demo extends TxDemo
{
    public static function main()
    {
        new Member1010Demo();
        $demoConfig = self::$demoConfig;
        // 组装参数
        $bizParameter = new BizParameter();

        $bizParameter->addParam("reqTraceNum", TxUtils::genReqTraceNum());
        $bizParameter->addParam("signNum", "2705wx100002");
        $bizParameter->addParam("memberRole","分销方");
        $bizParameter->addParam("cerType","1");

        $bizParameter->addParam("cerNum", DemoSM4Utils::encryptEcb($demoConfig->getSecretKey(),"410725199907022818"));
        $bizParameter->addParam("name","苏大大");
        $bizParameter->addParam("acctNum",  DemoSM4Utils::encryptEcb($demoConfig->getSecretKey(),"6217858000141669850"));

        $bizParameter->addParam("phone","15617906676");
        $bizParameter->addParam("bindType","8");//6-通联通协议支付，7-收银宝快捷，8-银行卡四要素
////


//        $bizParameter->addParam("validDate","1299");
//        $bizParameter->addParam("cvv2","333");
        $bizParameter->addParam("bizOrderCode", (string)TxUtils::getMillisecond());
        // 发送请求
        $txClient = new TxClient($demoConfig);
        $txResponse = $txClient->sendRequest("1010", $bizParameter, $demoConfig->getMemberUrl());
        // 打印响应结果
        echo PHP_EOL."响应结果: ".$txResponse->bizData;
    }
}

Member1010Demo::main();