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
 * 【消费申请】
 *
 * @author 邵鉴
 * @version 1.0
 * @date 2024/10/22
 */
class Tx2085Demo extends TxDemo
{
    public static function main()
    {
        new Tx2085Demo();
        $demoConfig = self::$demoConfig;
        // 支付模式
        $bankCardNo = DemoSM4Utils::encryptEcb($demoConfig->getSecretKey(), "6212261001029054530");

        // 微信正扫
        $SCAN_WEIXIN = [
        "SCAN_WEIXIN" => json_encode([
            "limitPay" => "no_credit",
            "vspCusid" => "55058404816VQJW"
            ])
        ];

        // 银联正扫
        $SCAN_UNIONPAY = [
        "SCAN_UNIONPAY" => json_encode([
            "limitPay" => "no_credit"
            ])
        ];

        // 支付宝正扫
        $SCAN_ALIPAY = [
        "SCAN_ALIPAY" => json_encode([
            "limitPay" => "no_credit",
            "vspCusid" => "552100053118ZCV"
            ])
        ];

        // 收银宝POS
        $ORDER_VSPPAY = [
        "ORDER_VSPPAY" => json_encode([
            "vspCusid" => "6602900601500JK"
            ])
        ];

        // 付款码支付
        $CODEPAY_VSP = [
        "CODEPAY_VSP" => json_encode([
            "vspCusid" => "",
            "authcode" => "280310687633511560"
            ])
        ];

        // H5收银台
        $H5_CASHIER_VSP = [
        "H5_CASHIER_VSP" => json_encode([
            "vspCusid" => ""
            ])
        ];

        $sepDetail = array([
            "signNum"=>"2705wxl00002",
            "amount"=>"1"
        ]);


        // 订单过期时间
        $orderValidTime = date('Y-m-d H:i:s', strtotime('+720 minutes'));

        // 组装参数
        $bizParameter = new BizParameter();
        $bizParameter->addParam("signNum", "111111");//TESTWC10011
        $bizParameter->addParam("receiverSignNum", "XiaoWanZiDaYinDian");

        $bizParameter->addParam("reqTraceNum", "wxl" . TxUtils::genReqTraceNum());//商户订单号
        $bizParameter->addParam("orderAmount", 3);//订单金额
        $bizParameter->addParam("payAmount", 2);//支付金额
        $bizParameter->addParam("promotionAmount", 1);//营销金额
        $bizParameter->addParam("couponAmount",1 );//平台抽佣金额
        $bizParameter->addParam("verifyType", 1);//交易验证方式-0：无验证、1：短信验证码（默认-1：短信验证码）

        //支付模式
        $bizParameter->addMapParam("payMode", $SCAN_WEIXIN);//微信正扫
//        bizParameter.addMapParam("payMode", QUICKPAY_VSP);//收银宝快捷
//        bizParameter.addMapParam("payMode", QUICKPAY_SFT);//收付通快捷
//        bizParameter.addMapParam("payMode", SCAN_UNIONPAY);//银联正扫
//        bizParameter.addMapParam("payMode", SCAN_ALIPAY);//支付宝正扫
//        bizParameter.addMapParam("payMode", ORDER_VSPPAY);//收银宝POS
//        bizParameter.addMapParam("payMode", CODEPAY_VSP);//付款码支付

        $bizParameter->addMapParam("sepDetail", $sepDetail);//分账规则
        $bizParameter->addParam("reqsUrl", "http://www.baidu.com");//前台通知地址
        $bizParameter->addParam("respUrl", "http://test.allinpay.com/open/testNotify");//后台通知地址
        $bizParameter->addParam("orderValidTime", $orderValidTime);//订单过期时间
//        $bizParameter->addParam("goodsType", "goodsType");//商品类型
//        $bizParameter->addParam("bizGoodsNo", "bizGoodsNo123456");//商户商品编号
        $bizParameter->addParam("goodsName", "goodsName123456");//商品名称
        $bizParameter->addParam("goodsDesc", "goodsDesc123456");//商品描述
        $bizParameter->addParam("industryCode", "111111");//行业代码
        $bizParameter->addParam("industryName", "222222");//行业名称
        $bizParameter->addParam("summary", "摘要");
        $bizParameter->addParam("extendParams", "{\"extend\":\"abcdefghiii\"}");
        
        
        
        // 发送请求
        $txClient = new TxClient($demoConfig);
        $txResponse = $txClient->sendRequest("2085", $bizParameter, $demoConfig->getUrl());

        // 打印响应结果
        echo PHP_EOL."响应结果: ".$txResponse->bizData;
    }
}

Tx2085Demo::main();

