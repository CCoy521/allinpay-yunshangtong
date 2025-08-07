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
 * @description 进件查询
 * @author 任海东
 * @since 2024年6月17日
 */
class Hy1072Demo extends TxDemo
{

    public function test()
    {
        $demoConfig = self::$demoConfig;
        $txClient = new TxClient($demoConfig);
        $bizParameter = new BizParameter();
        $bizParameter->setParameters($this->mockBizContent());
        $txResponse = $txClient->sendRequest("1072",$bizParameter, $demoConfig->getCusUrl());
        echo PHP_EOL . "响应结果: " . $txResponse->bizData;
    }
    private function mockBizContent()
    {
        $params = [
            'reqTraceNum' => (new DateTime())->format('mdHis') . round(microtime(true) * 1000),
            'signNum' => '7110023456'
        ];
        return $params;
    }
}

(new Hy1072Demo())->test();

