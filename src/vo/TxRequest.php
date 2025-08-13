<?php

namespace Allinpay\YunshangTong\Vo;

class TxRequest
{
    private String $appId;
    private String $spAppId;
    private String $transCode;
    private String $transDate;
    private String $transTime;
    private String $format;
    private String $charset;
    private String $signType;
    private String $sign;
    private String $version;
    private String $bizData;

    public function getAppId(): string
    {
        return $this->appId;
    }

    public function setAppId(string $appId): void
    {
        $this->appId = $appId;
    }

    public function getSpAppId(): string
    {
        return $this->spAppId;
    }

    public function setSpAppId(string $spAppId): void
    {
        $this->spAppId = $spAppId;
    }

    public function getTransCode(): string
    {
        return $this->transCode;
    }

    public function setTransCode(string $transCode): void
    {
        $this->transCode = $transCode;
    }

    public function getTransDate(): string
    {
        return $this->transDate;
    }

    public function setTransDate(string $transDate): void
    {
        $this->transDate = $transDate;
    }

    public function getTransTime(): string
    {
        return $this->transTime;
    }

    public function setTransTime(string $transTime): void
    {
        $this->transTime = $transTime;
    }

    public function getFormat(): string
    {
        return $this->format;
    }

    public function setFormat(string $format): void
    {
        $this->format = $format;
    }

    public function getCharset(): string
    {
        return $this->charset;
    }

    public function setCharset(string $charset): void
    {
        $this->charset = $charset;
    }

    public function getSignType(): string
    {
        return $this->signType;
    }

    public function setSignType(string $signType): void
    {
        $this->signType = $signType;
    }

    public function getSign(): string
    {
        return $this->sign;
    }

    public function setSign(string $sign): void
    {
        $this->sign = $sign;
    }

    public function getVersion(): string
    {
        return $this->version;
    }

    public function setVersion(string $version): void
    {
        $this->version = $version;
    }

    public function getBizData(): string
    {
        return $this->bizData;
    }

    public function setBizData(string $bizData): void
    {
        $this->bizData = $bizData;
    }

    public function toString()
    {
        return "{" . "\"appId\": \"" . $this->appId . "\", \"spAppId\": \"" . $this->spAppId
            . "\", \"transCode\": \"" . $this->transCode . "\", \"transDate\": \"" . $this->transDate
            . "\", \"transTime\": \"" . $this->transTime . "\", \"format\": \"" . $this->format
            . "\", \"charset\": \"" . $this->charset . "\", \"signType\": \"" . $this->signType
            . "\", \"sign\": \"" . $this->sign . "\", \"version\": \"" . $this->version
            . "\", \"bizData\": \"" . $this->bizData . "\"}";
    }

}