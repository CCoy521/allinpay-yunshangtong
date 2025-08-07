<?php
namespace tx;
include 'TxDemo.php';
include '../util/DemoSM2Utils.php';
include '../util/DemoSM4Utils.php';
include '../util/TxUtils.php';
include '../vo/BizParameter.php';
include '../tx/TxClient.php';

require_once __DIR__ . '/../../vendor/autoload.php';

use DateTime;
use TxClient;
use util\DemoSM4Utils;
use vo\BizParameter;

/**
 * @description 个人进件
 * @author 任海东
 * @since 2024年6月17日
 */
class Hy1070Demo extends TxDemo
{

    public function test()
    {
        $demoConfig = self::$demoConfig;
        $txClient = new TxClient($demoConfig);
        $bizParameter = new BizParameter();
        $bizParameter->setParameters($this->mockBizContent());
        $txResponse = $txClient->sendRequest("1070",$bizParameter, $demoConfig->getCusUrl());
        echo PHP_EOL . "响应结果: " . $txResponse->bizData;
    }
    private function mockBizContent()
    {
        $basic = [
            'cusName' => '云商通测试商户',
            'cusSimName' => '测试使用',
            'comProperty' => '3',
            'servicePhone' => '17621605022',
            'mcc' => '5311',
            'corpBusName' => '竹溪县子怡鞋店',
            'creditCode' => '92420324MA4D68J28J',
            'creditCodeExpire' => '2056-12-31',
            'legalName' => '王三华',
            'legalIdType' => '01',
            'legalIdNo' => DemoSM4Utils::encryptEcb(self::$demoConfig->getSecretKey(), '51370119380325580x'),
            'legalIdStartdate' => '2020-10-24',
            'legalIdExpire' => '2040-10-24',
            'address' => '浦东新区金沪路55号',
            'contactPerson' => '吴大名',
            'contactPhone' => '18888889999',
            'registerFund' => '5',
            'staffTotal' => '5',
            'operateLimit' => '1',
            'inspect' => '1',
            'busConactPerson' => '吴大明',
            'busConactTel' => '18888888888',
            'busDetail' => '餐饮零售烟草专卖',
            'businessPlace' => '1',
            'busAddress' => '金沪路55号',
            'districtCode' => '110101',
            'icpInfo' => '',
            'appScenario' => '',
            'personTel' => '18818181818',
            'expandUser' => '陈辉1(18897099799)',
            'clearTimeRule' => '',
        ];

        $bnf = [
            'legalCountry' => '中国',
            'legalSex' => '1',
            'legalCareer' => '2',
            'legalAddress' => '金沪路55号',
            'merchantType' => '1',
            'beneficiaryJudgmentCriteria' => '1',
            'beneficiaryCerType' => '01',
            'beneficiaryName' => '吴二氏',
            'beneficiaryCerNum' => DemoSM4Utils::encryptEcb(self::$demoConfig->getSecretKey(), '429005197502113030'),
            'beneficiaryCerValidate' => '2059-05-12',
            'isSeniorManagement' => '1',
            'beneficiaryAddress' => '乡村大院',
            'shareholderName' => '吴名氏',
            'shareholderCerNum' => DemoSM4Utils::encryptEcb(self::$demoConfig->getSecretKey(), '429005197502113033'),
            'shareholderCerValidate' => '2069-05-12',
        ];

        $bank = [
            'acctAttr' => '1',
            'acctNum' => DemoSM4Utils::encryptEcb(self::$demoConfig->getSecretKey(), '6210830206'),
            'openBankNo' => '01030000',
            'openBankBranchName' => '中国农业银行股份有限公司北京樱桃园支行',
            'payBankNumber' => '403100004030',
            'openBankProvince' => '安徽省',
            'openBankCity' => '合肥市',
        ];

        $prodList = [
            [
                'pid' => 'P0003',
                'mtrxCode' => 'VSP501',
                'feeRate' => '3.00',
            ],
            [
                'pid' => 'P0003',
                'mtrxCode' => 'VSP511',
                'feeRate' => '5',
                'feeCycle' => '2',
                'lowLimit' => '0.01',
            ],
        ];

        $branchList = [
            [
                'branchNo' => '',
                'branchName' => '门店1',
                'branchAddr' => '金沪路55号',
                'contactPerson' => '小冬瓜',
                'contactPhone' => '18818181818',
                'districtCode' => '110101',
            ],
        ];

        $pic = [
            'unifiedSocialCreditPhoto' => '3320240711161811317747167244289',
            'legalFacePhoto' => '3320240711161811317747167244289',
            'legalNationalEmblemPhoto' => '3320240711161811317747167244289',
            'settleAcctPhoto' => '3320240711161811317747167244289',
            'businessDoorHeadPhoto' => '3320240711161811317747167244289',
            'businessInteriorPhoto' => '3320240711161811317747167244289',
            'personHeadPic' => '3320240711161811317747167244289',
        ];

        $params = [
            'reqTraceNum' => (new DateTime())->format('mdHis') . round(microtime(true) * 1000),
            'signNum' => '711002345678',
            'notifyUrl' => 'http://test.allinpay.com/open/testNotify',
            'merAddBasicInfo' => $basic,
            'merAddBeneficiaryInfo' => $bnf,
            'merAddBankAcctInfo' => $bank,
            'merAddProductInfo' => $prodList,
            'merAddBranchInfo' => $branchList,
            'merAddAttachmentInfo' => $pic,
        ];

        return $params;
    }
}

(new Hy1070Demo())->test();

