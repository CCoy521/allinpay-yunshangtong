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

// 【单会员担保确认】
class Tx2091Demo extends TxDemo
{
    public static function main()
    {
        new Tx2091Demo();
        $demoConfig = self::$demoConfig;
        // 源担保消费申请订单付款信息
        $applicationList = [];
        $applicationMap1 = [];
        // $applicationMap1["orgReqTraceNum"] = "202401261732145417";
        // $applicationMap1["orgTransDate"] = "20240126";
        $applicationMap1["orgRespTraceNum"] = "20240225155256208901295028";
        $applicationMap1["orderAmount"] = 2;
        $applicationMap1["couponAmount"] = 1; // 平台抽佣金额
        $applicationList[] = $applicationMap1;

        $applicationMap2 = [];
        // $applicationMap2["orgReqTraceNum"] = "202401261733046406";
        // $applicationMap2["orgTransDate"] = "20240126";
        $applicationMap2["orgRespTraceNum"] = "20240218143405208901531993";
        $applicationMap2["orderAmount"] = 1;
        // $applicationList[] = $applicationMap2;

        // 分账信息
        $splitDetail = [];
        $splitMap1 = [];
        $splitMap1["signNum"] = "9665wxl202400004";
        $splitMap1["amount"] = 1;
        $splitDetail[] = $splitMap1;

        $splitMap2 = [];
        $splitMap2["signNum"] = "9665wxl202400005";
        $splitMap2["amount"] = 1;
        // $splitDetail[] = $splitMap2;

        // 单会员担保确认列表
        $applyList = [];
        $map1 = [];
        $map1["reqTraceNum"] = "wxl" . TxUtils::genReqTraceNum();
        $map1["applyInfo"] = json_encode($applicationList);
        $map1["signNum"] = "9665wxl202400009";
        $map1["amount"] = 2;
        $map1["couponAmount"] = 1; // 平台抽佣金额
        $map1["sepDetail"] = json_encode($splitDetail);
        $map1["summary"] = "343q434";
        $map1["extendParams"] = '{"extend":"abcdefghiii"}';
        $applyList[] = $map1;

        // 组装参数
        $bizParameter = new BizParameter();
        $bizParameter->addParam("batchNo", TxUtils::genReqTraceNum());
        $bizParameter->addListParam("applyList", $applyList);
        $bizParameter->addParam("respUrl", "http://test.allinpay.com/open/testNotify");
        // $bizParameter->addParam("extendParams", '{"extend":"abcdefghiii"}');

        // 发送请求
        $txClient = new TxClient($demoConfig);
        $txResponse = $txClient->sendRequest("2091", $bizParameter, $demoConfig->getUrl());
        // 打印响应结果
        echo PHP_EOL."响应结果: ".$txResponse->bizData;
    }
}
(new Tx2091Demo())->main();