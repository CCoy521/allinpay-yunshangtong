<?php
namespace Allinpay\YunshangTong\Tx;

include '../vo/TxRequest.php';
include '../vo/TxResponse.php';

use Allinpay\YunshangTong\Config\DemoConfig;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Allinpay\YunshangTong\Util\DemoSM2Utils;
use Allinpay\YunshangTong\Vo\BizParameter;
use Allinpay\YunshangTong\Vo\TxRequest;
use Exception;

/**
 * Test Client
 *
 * @author 邵鉴
 * @version 1.0
 * @date 2024/10/22
 */
class TxClient
{
    private $logger;
    private const YYYY_MM_DD = 'Ymd';
    private const HH_MM_SS = 'His';
    private $config;
    private $privateKey;
    private $tlPublicKey;

    /**
     * @throws \FG\ASN1\Exception\ParserException
     */
    public function __construct(DemoConfig $config)
    {
        $this->logger = new Logger('TxClient');
        $this->logger->pushHandler(new StreamHandler('php://stderr', Logger::INFO));
        $this->config = $config;
        $this->privateKey = DemoSM2Utils::privKeySM2FromBase64Str($config->getPrivateKeyStr());
        $this->tlPublicKey = DemoSM2Utils::pubKeySM2FromBase64Str($config->getAllinpayPublicKeyStr());
    }

    public function sendRequest(string $transCode, BizParameter $param, ?string $url = null)
    {
        $request = $this->assembleRequest($transCode, $param);
        echo PHP_EOL;
        $this->logger->info("request: " . $request->toString());
        $respStr = $this->post($request->toString(), $url);
        $this->logger->info("response: " . $respStr);
        $this->verify($respStr);
        $this->logger->info("验签成功");
        return json_decode($respStr);
    }

    /**
     * @throws Exception
     */
    private function verify(string $respStr): void
    {
        $map = json_decode($respStr, true);
        $sign = $map['sign'];
        $srcSignMsg = DemoSM2Utils::jsonMapToStr($respStr);
        echo PHP_EOL;
        $this->logger->info("待验签源串: " . $srcSignMsg);
        if (DemoSM2Utils::verify($this->tlPublicKey, $srcSignMsg, $sign) !== true){
            throw new Exception("响应验签失败");
        }
    }

    private function assembleRequest(string $transCode, BizParameter $param): TxRequest
    {
        $request = new TxRequest();
        $request->setAppId($this->config->getAppId());
        $request->setSpAppId($this->config->getSpAppId());
        $request->setTransCode($transCode);
        $request->setFormat($this->config->getFormat());
        $request->setCharset($this->config->getCharset());
        $request->setTransDate(date(self::YYYY_MM_DD));
        $request->setTransTime(date(self::HH_MM_SS));
        $request->setVersion($this->config->getVersion());
        $temp = json_encode($param, JSON_UNESCAPED_SLASHES|JSON_UNESCAPED_UNICODE);
        //json转义
        $temp = str_replace('"', '\"', $temp);
        $temp = str_replace('\\\"', '\\\\\"', $temp);
        $request->setBizData($temp);
        $request->setSignType("");
        $request->setSign("");
        $signedValue = DemoSM2Utils::jsonMapToStr($request->toString(), true);
        echo PHP_EOL;
        $this->logger->info("待签名源串: " . $signedValue);
        $sign = trim(DemoSM2Utils::sign($this->privateKey, $signedValue));
        $request->setSignType($this->config->getSignType());
        $request->setSign($sign);
        return $request;
    }

    private function post(string $param, ?string $url = null): string
    {
        $client = new Client([
            'verify' => false,
            'headers' => [
                'Connection' => 'keep-alive',
                'Content-Type' => 'application/json;charset=utf-8',
            ]
        ]);

        try {
            $response = $client->post($url ?? $this->getReqUrl(), [
                'body' => $param
            ]);
            return $response->getBody()->getContents();
        } catch (GuzzleException $e) {
            $this->logger->error($e->getMessage(), ['exception' => $e]);
            throw $e;
        }
    }

    protected function getReqUrl(): string
    {
        return $this->config->getUrl();
    }

    public function getConfig(): DemoConfig
    {
        return $this->config;
    }
}

