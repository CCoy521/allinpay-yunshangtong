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
class Member1020Demo extends TxDemo
{
    public static function main()
    {
        new Member1020Demo();
        $demoConfig = self::$demoConfig;
        // 组装参数
        $bizParameter = new BizParameter();
        $bizParameter->addParam("reqTraceNum", TxUtils::genReqTraceNum());
        $bizParameter->addParam("signNum", "busi2");
        $bizParameter->addParam("memberRole", "收款方");
        $bizParameter->addParam("enterpriseNature", "2");  // 1企业，2个体户，3事业单位
        $bizParameter->addParam("notifyUrl", "http://test.allinpay.com/open/testNotify");

        $enterpriseBaseInfo = []; // 企业基本信息
        $enterpriseBaseInfo["enterpriseName"] = "餐饮店";
        $enterpriseBaseInfo["enterpriseAdress"] = "上海滩";
        $enterpriseBaseInfo["unifiedSocialCredit"] = "";
        $enterpriseBaseInfo["busLicenseValidate"] = "9999-12-31";
        $enterpriseBaseInfo["legalPersonName"] = "王三华";
        $enterpriseBaseInfo["legalPersonCerType"] = "1";
        $enterpriseBaseInfo["legalPersonCerNum"] = DemoSM4Utils::encryptEcb($demoConfig->getSecretKey(), "");
        $enterpriseBaseInfo["idValidateStart"] = "2023-12-31";
        $enterpriseBaseInfo["idValidateEnd"] = "9999-12-31";
        $enterpriseBaseInfo["legalPersonPhone"] = "";
        $bizParameter->addMapParam("enterpriseBaseInfo", $enterpriseBaseInfo);

        $bankAccountDetail = [];  // 银行账户信息
        $bankAccountDetail["acctAttr"] = "1"; // 0对私，1对公
        $bankAccountDetail["acctNum"] = DemoSM4Utils::encryptEcb($demoConfig->getSecretKey(), "123426789159100");
        $bankAccountDetail["bankReservePhone"] = "12312341234"; // 银行预留手机，对私必填
        $bankAccountDetail["openBankNo"] = "01020000"; // 银行代码，对公必填
        $bankAccountDetail["openBankBranchName"] = "中国工商银行上海滩分行";
        $bankAccountDetail["payBankNumber"] = "123456789123"; // 支行行号
        $bankAccountDetail["openBankProvince"] = "上海市";
        $bankAccountDetail["openBankCity"] = "上海市";
        $bizParameter->addMapParam("bankAcctDetail", $bankAccountDetail);

        // 发送请求
        $txClient = new TxClient($demoConfig);
        $txResponse = $txClient->sendRequest("1020", $bizParameter, $demoConfig->getMemberUrl());
        // 打印响应结果
        echo PHP_EOL."响应结果: ".$txResponse->bizData;
    }
}

Member1020Demo::main();