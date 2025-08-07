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
 * 【单订单担保确认】
 *
 * @author gejunqing
 * @version 1.0
 * @date 2024/1/11
 */
class Tx2090Demo extends TxDemo
{
    public static function main()
    {
        new Tx2090Demo();
        $demoConfig = self::$demoConfig;
    	//分账列表
        $sepDetail = array(
            [
                "signNum" => "9665wxl202400006",
                "amount" => 1,
                "remark" => "remark"
            ],
            [
                "signNum" => "ysttwolp03",
                "amount" => 1
            ]
        );
    	
    	//收款人列表
    	$receiverList = array(
            [
                "signNum" => "9665wxl202400006",
                "amount" => 2,
                "couponAmount" => 0,
                "sepDetail" => json_encode($sepDetail)
            ],
            [
                "signNum" => "9665wxl202400003",
                "amount" => 2,
                "couponAmount" => 0
            ]
        );
    	
    	// 组装参数
        $bizParameter = new BizParameter();
        $bizParameter->addParam("reqTraceNum", "wxl". TxUtils::genReqTraceNum());
//        $bizParameter->addParam("orgReqTraceNum", "202401291705084746");
//        $bizParameter->addParam("orgTransDate", "20240129");
        $bizParameter->addParam("orgRespTraceNum", "20240225155256208901295028");
        $bizParameter->addListParam("receiverList", $receiverList);
        $bizParameter->addParam("respUrl", "http://test.allinpay.com/open/testNotify");
        $bizParameter->addParam("summary", "摘要");
        $bizParameter->addParam("extendParams", "{\"extend\":\"abcdefghiii\"}");
        

        // 发送请求
        $txClient = new TxClient($demoConfig);
        $txResponse = $txClient->sendRequest("2090", $bizParameter, $demoConfig->getUrl());

        // 打印响应结果
        echo PHP_EOL."响应结果: ".$txResponse->bizData;
    }
}
(new Tx2090Demo())->main();