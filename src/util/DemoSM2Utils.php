<?php

namespace util;

include "../../vendor/autoload.php";

use FG\ASN1\ASNObject;
use FG\ASN1\Exception\ParserException;
use Rtgm\sm\RtSm2;

/**
 * SM2工具类
 *
* @author 邵鉴
* @version 1.0
* @date 2024/10/22
 * */
class DemoSM2Utils
{
    /**
     * json对象转字符串
     *
     * @param $jsonObject
     * @return string
     */
    public static function jsonMapToStr($jsonObject)
    {
        $keys = json_decode($jsonObject,true);
        ksort($keys);
        $raw = "";
        foreach ($keys as $key => $value) {
            if ($key == "sign"){
                continue;
            }
            if (!empty($value)) {
                $raw .= $key . "=" . $value . "&";
            }
        }

        if (strlen($raw) > 0) {
            $raw = rtrim($raw, "&");
        }
        return $raw;
    }

    /**
     * 从字符串读取私钥-目前支持PKCS8(keyStr为ASN.1数据结构，以BASE64格式存储)
     * @throws ParserException
     */
    public static function privKeySM2FromBase64Str($keyStr)
    {
        $b64Key = base64_decode($keyStr);
        // ASN.1解析私钥
        // 应用私钥的封装
        $asn1_decoded = ASNObject::fromBinary($b64Key)->getChildren()[2]->getBinaryContent();
        $privateKey = ASNObject::fromBinary($asn1_decoded)->getChildren()[1]->getBinaryContent();
        echo base64_encode($privateKey);
        return bin2hex($privateKey);
    }

    /** 从字符串读取公钥-目前支持PKCS8(keyStr为ASN.1数据结构，以BASE64格式存储)
     * @throws ParserException
     */
    public static function pubKeySM2FromBase64Str($keyStr)
    {
        $b64Key = base64_decode($keyStr);
        $asn1_decoded = ASNObject::fromBinary($b64Key)->getChildren()[1]->getBinaryContent();
        $publicKey = bin2hex($asn1_decoded);
        //去除公钥开头多余的00
        //请根据调试过程中公钥的格式修改此处代码
        if (substr( $publicKey, 0, 2 ) == '00'){
            $publicKey = substr($publicKey, 2);
        }
        return $publicKey;
    }

    // 加签
    public static function sign($privateKey, $text)
    {
        $sm2 = new RtSm2("base64");
        return $sm2->doSign($text, $privateKey);
    }

    // 验签
    public static function verify($publicKey, $text, $sign)
    {
        if (self::isEmpty($sign)) {
            return false;
        }
        $sm2 = new RtSm2("base64");
        return $sm2->verifySign($text, $sign, $publicKey);
    }

    public static function isEmpty($str)
    {
        return $str === null || $str === "" || trim($str) === "";
    }

}

