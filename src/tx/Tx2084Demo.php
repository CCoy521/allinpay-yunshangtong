<?php
namespace tx;

include 'TxDemo.php';
include 'TxClient.php';
//include '../config/DemoConfig.php';
include '../util/DemoSM2Utils.php';
include '../util/DemoSM4Utils.php';
include '../util/TxUtils.php';
include '../vo/BizParameter.php';


require_once __DIR__ . '/../../vendor/autoload.php';

use tx\TxDemo;
use util\DemoSM4Utils;
use util\TxUtils;
use vo\BizParameter;
use TxClient;
/**
 * 【转账申请】
 *
 * @author gejunqing
 * @version 1.0
 * @date 2024/1/11
 */
class Tx2084Demo extends TxDemo
{
    public static function main()
    {
        new Tx2084Demo();
        $demoConfig = self::$demoConfig;
    	// 组装参数
        $bizParameter = new BizParameter();
        $bizParameter->addParam("reqTraceNum", TxUtils::genReqTraceNum(). "lp2084");//商户订单号
        $bizParameter->addParam("signNum", "#yunBizUserId_B2C#");//商户会员编号-转出方//#yunBizUserId_B2C#
//        $bizParameter->addParam("acctType", 1);//1：簿记账户（默认）、2：支付账户
//        $bizParameter->addParam("acctNum", "1001010101");//支付账户-转出方
        $bizParameter->addParam("inSignNum", "9665wxl202400006");//商户会员编号-转入方//ysttwolp01
//        $bizParameter->addParam("inAcctType", 1);//1：簿记账户（默认）、2：支付账户
//        $bizParameter->addParam("inAcctNum", "1001010101");//支付账户-转出方
        $bizParameter->addParam("respUrl", "http://test.allinpay.com/open/testNotify");//后台通知地址
        $bizParameter->addParam("orderAmount", 1000);//订单金额
       // $bizParameter->addParam("summary", "摘要");
       // $bizParameter->addParam("extendParams", "{\"extend\":\"abcdefghiii\"}");

        // 发送请求
        $txClient = new TxClient($demoConfig);
        $txResponse = $txClient->sendRequest("2084", $bizParameter, $demoConfig->getUrl());

        // 打印响应结果
        echo PHP_EOL."响应结果: ".$txResponse->bizData;
    }
}
(new Tx2084Demo())->main();