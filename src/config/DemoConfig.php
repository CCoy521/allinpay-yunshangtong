<?php
namespace Allinpay\YunshangTong\Config;

require_once __DIR__ . '/../../vendor/autoload.php';
use FG\ASN1\ASNObject;
/**
 * 测试参数
 *
 * @author 邵鉴
 * @version 1.0
 * @date 2024/10/22
 */

class DemoConfig
{
    /**
     * demo测试公共应用私钥（测试）
     * 21762000921804636162，ABC
     */
    private String $privateKeyStr = "MIGTAgEAMBMGByqGSM49AgEGCCqBHM9VAYItBHkwdwIBAQQgiaZmB+feACtziE8SYjVZsaQwLNLRiyO8ebSupeoWIF2gCgYIKoEcz1UBgi2hRANCAATwEo0zq6KaB992PToWeJH52LmfS0sFovnB8/LMaoIAOTlFJtA3YgjWXKlO3KT+GqOCfCC4xE60isCr28tqy7hM";
//    private String $privateKeyStr = "MIGTAgEAMBMGByGGSM49AgEGCCGBHM9VAYItBHkwdwIBAQQgiaZmB+feACtziE8SYjVZsaQWLNLRiyO8ebsupeoWIF2gCgYIKoECZ1UBgi2hRANCAATwE00zq6KaB992PToWeJH52LmfS0sFovnB8/LMaOIAOTIFJtA3YgjWXKI03KT+GqOCfCC4xE60isCr28tqy7hM";

    /**
     * demo测试通联公钥
     */
    private $allinpayPublicKeyStr = "MFkwEwYHKoZIzj0CAQYIKoEcz1UBgi0DQgAEu9LNkJlyLtjJxtQWIGlcZ/hyHt5eZ7LEH1nfOiK1H9HsE1cMPu5KK5jZVTtAyc7lPMXixUMirf6A3tMbuMbgqg==";

    //请求地址--测试环境
    private $url = "http://116.228.64.55:28082/yst-service-api/tx/handle";//交易
    private $memberUrl = "http://116.228.64.55:28082/yst-service-api/tm/handle";//会员认证
    private $queryUrl = "http://116.228.64.55:28082/yst-service-api/tq/handle";//结果查询
    private $cusUrl = "http://192.168.14.132:8888/ystcusapi/api";//进件

    private $fileUploadUrl = "http://116.228.64.55:28082/yst-service-api/file/upload";
    private $fileDownloadUrl = "http://116.228.64.55:28082/yst-service-api/file/download";

    //请求地址--生产环境
    /*
    private $url = "https://ibsapi.allinpay.com/yst-service-api/tx/handle";
    private $memberUrl = "https://ibsapi.allinpay.com/yst-service-api/tm/handle";
    private $queryUrl = "https://ibsapi.allinpay.com/yst-service-api/tq/handle";

    private $fileUploadUrl = "https://ibsapi.allinpay.com/yst-service-api/file/upload";
    private $fileDownloadUrl = "https://ibsapi.allinpay.com/yst-service-api/file/download";

    // 通联公钥
    private $allinpayPublicKeyStr ="MFkwEwYHKoZIzj0CAQYIKoEcz1UBgi0DQgAE/VKHBem28IXD30yuZN1QcNgGE4gzqgd/eX1ZEouUleLNfrnQJkOs7LzAag3q10uaH/e9+5JyJDx3ULfKS4QZPw==";
    */

    private $spAppId = "11879464722018709506";
//    private $spAppId = "";
//    private $appId = "21762000921804636162";
    private $appId = "21879470716783222786";
    private $secretKey = "878427523d3525e070298d44481b8d2e";
    private $format = "json";
    private $charset = "UTF-8";
    private $signType = "SM3withSM2";
    private $version = "1.0";
    private $notifyUrl;

    public function __construct($appId = null, $spAppId = null, $url = null)
    {
        if ($appId !== null) {
            $this->appId = $appId;
        }
        if ($spAppId !== null) {
            $this->spAppId = $spAppId;
        }
        if ($url !== null) {
            $this->url = $url;
        }
    }

    public function getFileUploadUrl()
    {
        return $this->fileUploadUrl;
    }

    public function setFileUploadUrl($fileUploadUrl)
    {
        $this->fileUploadUrl = $fileUploadUrl;
        return $this;
    }

    public function getPrivateKeyStr()
    {
        return $this->privateKeyStr;
    }

    public function setPrivateKeyStr($privateKeyStr)
    {
        $this->privateKeyStr = $privateKeyStr;
    }

    public function getAllinpayPublicKeyStr()
    {
        return $this->allinpayPublicKeyStr;
    }

    public function setAllinpayPublicKeyStr($allinpayPublicKeyStr)
    {
        $this->allinpayPublicKeyStr = $allinpayPublicKeyStr;
    }

    public function getUrl()
    {
        return $this->url;
    }

    public function setUrl($url)
    {
        $this->url = $url;
    }

    public function getMemberUrl()
    {
        return $this->memberUrl;
    }

    public function setMemberUrl($memberUrl)
    {
        $this->memberUrl = $memberUrl;
        return $this;
    }

    public function getQueryUrl()
    {
        return $this->queryUrl;
    }

    public function setQueryUrl($queryUrl)
    {
        $this->queryUrl = $queryUrl;
        return $this;
    }

    public function getFileDownloadUrl()
    {
        return $this->fileDownloadUrl;
    }

    public function setFileDownloadUrl($fileDownloadUrl)
    {
        $this->fileDownloadUrl = $fileDownloadUrl;
        return $this;
    }

    public function getSpAppId()
    {
        return $this->spAppId;
    }

    public function setSpAppId($spAppId)
    {
        $this->spAppId = $spAppId;
    }

    public function getAppId()
    {
        return $this->appId;
    }

    public function setAppId($appId)
    {
        $this->appId = $appId;
    }

    public function getSecretKey()
    {
        return $this->secretKey;
    }

    public function setSecretKey($secretKey)
    {
        $this->secretKey = $secretKey;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function setFormat($format)
    {
        $this->format = $format;
    }

    public function getCharset()
    {
        return $this->charset;
    }

    public function setCharset($charset)
    {
        $this->charset = $charset;
    }

    public function getSignType()
    {
        return $this->signType;
    }

    public function setSignType($signType)
    {
        $this->signType = $signType;
    }

    public function getVersion()
    {
        return $this->version;
    }

    public function setVersion($version)
    {
        $this->version = $version;
    }

    public function getNotifyUrl()
    {
        return $this->notifyUrl;
    }

    public function setNotifyUrl($notifyUrl)
    {
        $this->notifyUrl = $notifyUrl;
    }

    public function __toString()
    {
        return "DemoConfig:{" . "privateKeyStr='" . $this->privateKeyStr . "', allinpayPublicKeyStr='"
            . $this->allinpayPublicKeyStr . "', url='" . $this->url . "', appId='" . $this->appId . "', spAppId='"
            . $this->spAppId . "', secretKey='" . $this->secretKey . "', format='" . $this->format . "', charset='"
            . $this->charset . "', signType='" . $this->signType . "', version='" . $this->version . "', notifyUrl='"
            . $this->notifyUrl . "'}";
    }

    public function mainTest()
    {
        //测试公私钥提取
        $conf = new DemoConfig();
        $temp = base64_decode($conf->privateKeyStr);
        $a = ASNObject::fromBinary($temp)->getChildren()[2];
        $b = $a->getBinaryContent();
        $c0 = ASNObject::fromBinary($b)->getChildren()[3]->getContent()[0];
        $c1 = $c0->getContent();
        $c = ASNObject::fromBinary($b)->getChildren()[1];
        $d = bin2hex($c->getBinaryContent());
        $e = base64_encode(hex2bin($d));
        echo $a[0]->getContent();
    }

    public function getCusUrl(): string
    {
        return $this->cusUrl;
    }

    public function setCusUrl(string $cusUrl): void
    {
        $this->cusUrl = $cusUrl;
    }
}
//(new DemoConfig())->mainTest();
