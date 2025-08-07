<?php

namespace tx;

include 'TxDemo.php';
include 'TxClient.php';
include '../config/DemoConfig.php';
include '../util/DemoSM2Utils.php';
include '../util/TxUtils.php';
include '../vo/BizParameter.php';

use vo\BizParameter;
use config\DemoConfig;
use TxClient;
use tx\TxDemo;
use util\TxUtils;

require_once __DIR__ . '/../../vendor/autoload.php';

/**
 * 【提现申请】
 *
 * @author 邵鉴
 * @version 1.0
 * @date 2024/10/22
 */
class Tx2290Demo extends TxDemo
{
    public static function main()
    {
        // 订单过期时间
        $orderValidTime = date('Y-m-d H:i:s', strtotime('+720 minutes'));

        $WITHDRAW_SFT = [
            "WITHDRAW_SFT" => "{\"payBankNumber\":\"\",\"payBankName\":\"\",\"province\":\"\",\"city\":\"\",\"settleType\":\"\"}"
        ];
        // 组装参数
        $bizParameter = new BizParameter();

        $bizParameter->addParam("signNum", "9665wxl202400006");
        $bizParameter->addParam("reqTraceNum", "wxl" . TxUtils::genReqTraceNum());
        $bizParameter->addParam("acctType", 1);//1: 簿记账户（默认）、2：支付账户、4：应用营销账户
        // $bizParameter->addParam("payAcctSubNo", "");//支付账号
        $bizParameter->addParam("orderAmount", 3);
        $bizParameter->addParam("couponAmount", 0);
        $bizParameter->addParam("verifyType", 0);
        $bizParameter->addParam("respUrl", "http://test.allinpay.com/open/testNotify");
        $bizParameter->addParam("orderValidTime", $orderValidTime);
        $bizParameter->addMapParam("payMode", $WITHDRAW_SFT);
        // $bizParameter->addParam("receiveAcctType", 1);
        $bizParameter->addParam("acctNum", "6212261001029054530");
        // $bizParameter->addParam("withdrawType", "D0");
        $bizParameter->addParam("summary", "摘要");
        $bizParameter->addParam("extendParams", "{\"extend\":\"小粒粒测试\"}");

        // 发送请求
        $demoConfig = new DemoConfig();
        $txClient = new TxClient($demoConfig);
        $txResponse = $txClient->sendRequest("2290", $bizParameter, $demoConfig->getUrl());

        // 打印响应结果
        echo PHP_EOL."响应结果: ".$txResponse->bizData;
    }
}

// 执行主函数
Tx2290Demo::main();

