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
use vo\BizParameter;

/**
 * @description 合规补录
 * @author 任海东
 * @since 2024年6月17日
 */
class Hy1076Demo extends TxDemo
{

    public function test()
    {
        $demoConfig = self::$demoConfig;
        $txClient = new TxClient($demoConfig);
        $bizParameter = new BizParameter();
        $bizParameter->setParameters($this->mockBizContent());
        $txResponse = $txClient->sendRequest("1076",$bizParameter, $demoConfig->getCusUrl());
        echo PHP_EOL . "响应结果: " . $txResponse->bizData;
    }
    private function mockBizContent()
    {
        $params = [
            'reqTraceNum' => (new DateTime())->format('mdHis') . round(microtime(true) * 1000),
            'signNum' => '9911900504565AB123',
            'notifyUrl' => 'http://test.allinpay.com/open/testNotify',
            'cusShortName' => '新的简称',
            'storePic' => '3320240711091811216332855947266',
            'innerPic' => '3320240711091811216332855947266'
        ];
        return $params;
    }
}

(new Hy1076Demo())->test();

