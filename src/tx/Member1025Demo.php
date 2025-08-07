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
use util\DemoSM4Utils;
use util\TxUtils;
use vo\BizParameter;
use TxClient;

class Member1025Demo extends TxDemo
{
    public static function main()
    {
        new Member1025Demo();
        $demoConfig = self::$demoConfig;
        // 组装参数
        $bizParameter = new BizParameter();
        $bizParameter->addParam("reqTraceNum", TxUtils::genReqTraceNum());
        $bizParameter->addParam("signNum", "Tn470001");//#yunBizUserId_B2C#
        $bizParameter->addParam("memberRole", "收款方");
        $bizParameter->addParam("enterpriseNature", "2");//1-企业  2-个体工商户 3-事业单位
        $bizParameter->addParam("notifyUrl", "http://test.allinpay.com/open/testNotify");

        //企业基本信息
        $enterpriseBaseInfo = [
            "busLicenseValidate" => "2028-12-31",
            "idValidateStart" => "2021-12-31",
            "idValidateEnd" => "9999-12-31",
            "busiScope" => "水产之类",  //经营内容     //必填，
            "addressCode" => "110101",   //地区码
            //已实名会员可不填
            "enterpriseName" => "竹溪县子怡鞋店",
            "enterpriseAdress" => "金沪路55号",
            "unifiedSocialCredit" => "92420324MA4D68J28J",//92420324MA4D68J28J
            "legalPersonName" => "王三华",
            "legalPersonCerType" => "1",  //仅支持1：身份证，51370119380325580x
            "legalPersonCerNum"=> DemoSM4Utils::encryptEcb($demoConfig->getSecretKey(), ""),
            "legalPersonPhone" => "",//*/
        ];

        $bizParameter->addMapParam("enterpriseBaseInfo", $enterpriseBaseInfo);

        //受益人基本信息
        $legaAndBeneficiaryInfo = [
            //法人国籍
            "legalCountry" => "中国",
            //法人性别  1：男  2：女
            "legalSex" => "1",
            //法人职业
            "legalCareer" => "1",
            //法人住址
            "legalAddress" => "凤岗村",
            //商户类型   00个体户  01公司不含国企  02 合伙企业  03个人独资企业   04国企
            "merchantType" => "01",
            //受益所有人判定标准
            "beneficiaryJudgmentCriteria" => "1",
            //证明材料    当“商户类型”字段选择“2-公司（不包含国有企业）”时,则“证明材料”字段不可上送“64”；
            "beneficiaryJudgmentFile" => "61",
            //法人是否受益人 0 是 1否
            "legalIsBeneficiary" => "1",
            //法人是否为股东控制人  0 是 1否
            "legalIsShareholder" => "1",
            //受益人证件类型
            "beneficiaryCerType" => "1",
            //受益人姓名
            "beneficiaryName" => "",
            //受益人证件号码
            "beneficiaryCerNum",DemoSM4Utils::encryptEcb($demoConfig->getSecretKey(), ""),
            //受益人证件证件有效期
            "beneficiaryCerValidate" => "9999-12-31",
            //是否高管  0：否  1：是
            "isSeniorManagement" => "1",
            //受益人住址
            "beneficiaryAddress" => "凤岗村",
            //控股股东姓名
            "shareholderName" => "",
            //控股股东证件号码
            "shareholderCerNum" => DemoSM4Utils::encryptEcb($demoConfig->getSecretKey(), ""),
            //控股股东证件有效期
            "shareholderCerValidate" => "2099-12-31",
        ];

        $bizParameter->addMapParam("legaAndBeneficiaryInfo", $legaAndBeneficiaryInfo);
        $txClient = new TxClient($demoConfig);
        $txResponse = $txClient->sendRequest("1025", $bizParameter, $demoConfig->getMemberUrl());
        // 打印响应结果
        echo PHP_EOL . "响应结果: " . $txResponse->bizData;
    }
}

Member1025Demo::main();