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
 * 【担保消费申请】
 *
 * @author gejunqing
 * @version 1.0
 * @date 2024/1/11
 */
class Tx2089Demo extends TxDemo
{
    public static function main()
    {
        new Tx2089Demo();
        $demoConfig = self::$demoConfig;
        //收款人列表
        $receiverList = array(
            [
                "signNum"=>"9665wxl202400009",
                "amount"=>2
            ],
            [
                "signNum"=>"9665wxl202400003",
                "amount"=>2
            ]
        );

      
    	// 组装参数
        $bizParameter = new BizParameter();
        $bizParameter->addParam("reqTraceNum", "wxl". TxUtils::genReqTraceNum());
        $bizParameter->addParam("signNum", "123456");//商户会员编号-付款人
        $bizParameter->addListParam("receiverList", $receiverList);
        $bizParameter->addParam("orderAmount", 4);//订单金额
        $bizParameter->addParam("payAmount", 0);//支付金额
        $bizParameter->addParam("promotionAmount", 4);//营销金额
        $bizParameter->addParam("reqsUrl", "http://www.baidu.com");
        $bizParameter->addParam("respUrl", "http://test.allinpay.com/open/testNotify");
        $bizParameter->addParam("orderValidTime", "");
        $bizParameter->addParam("goodsType", "1");
        $bizParameter->addParam("bizGoodsNo", "bizGoodsNo123456");
        $bizParameter->addParam("goodsName", "goodsName123456");
        $bizParameter->addParam("goodsDesc", "goodsDesc123456");
        $bizParameter->addParam("industryCode", "111111");
        $bizParameter->addParam("industryName", "222222");
        $bizParameter->addParam("summary", "摘要");
        // $bizParameter->addParam("extendParams", "{\"extend\":\"abcdefghiii\"}");
        // 支付模式
        $map = [
            "SCAN_WEIXIN"=>"{\"vspCusid\":\"\"}"
        ];
        $bizParameter->addMapParam("payMode", $map);
      
        // 发送请求
        $txClient = new TxClient($demoConfig);
        $txResponse = $txClient->sendRequest("2089", $bizParameter, $demoConfig->getUrl());

        // 打印响应结果
        echo PHP_EOL."响应结果: ".$txResponse->bizData;
    }
}
(new Tx2089Demo())->main();
