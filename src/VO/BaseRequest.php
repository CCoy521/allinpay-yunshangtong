<?php

namespace AllinpayNew\VO;

/**
 * 基础请求VO类
 * 基于通联支付KA客户版接口规范的公共请求参数
 */
class BaseRequest
{
    /**
     * 云商通二代分配的应用ID
     */
    protected string $appId;
    
    /**
     * 云商通二代分配的服务商应用ID
     */
    protected ?string $spAppId = null;
    
    /**
     * 接口代码
     */
    protected string $transCode;
    
    /**
     * 仅支持JSON
     */
    protected string $format = 'json';
    
    /**
     * 请求使用的编码格式，utf-8
     */
    protected string $charset = 'UTF-8';
    
    /**
     * 商户生成签名字符串所使用的签名算法类型
     */
    protected string $signType = 'SM3withSM2';
    
    /**
     * 商户请求参数的签名串
     */
    protected string $sign;
    
    /**
     * 发送请求的日期，格式"yyyyMMdd"
     */
    protected string $transDate;
    
    /**
     * 请求时间，格式"HHmmss"
     */
    protected string $transTime;
    
    /**
     * 调用的接口版本
     */
    protected string $version = '1.0';
    
    /**
     * 请求参数的集合，最大长度不限
     */
    protected string $bizData;
    
    public function __construct()
    {
        $this->transDate = date('Ymd');
        $this->transTime = date('His');
    }
    
    // Getters and Setters
    public function getAppId(): string
    {
        return $this->appId;
    }
    
    public function setAppId(string $appId): self
    {
        $this->appId = $appId;
        return $this;
    }
    
    public function getSpAppId(): ?string
    {
        return $this->spAppId;
    }
    
    public function setSpAppId(?string $spAppId): self
    {
        $this->spAppId = $spAppId;
        return $this;
    }
    
    public function getTransCode(): string
    {
        return $this->transCode;
    }
    
    public function setTransCode(string $transCode): self
    {
        $this->transCode = $transCode;
        return $this;
    }
    
    public function getFormat(): string
    {
        return $this->format;
    }
    
    public function setFormat(string $format): self
    {
        $this->format = $format;
        return $this;
    }
    
    public function getCharset(): string
    {
        return $this->charset;
    }
    
    public function setCharset(string $charset): self
    {
        $this->charset = $charset;
        return $this;
    }
    
    public function getSignType(): string
    {
        return $this->signType;
    }
    
    public function setSignType(string $signType): self
    {
        $this->signType = $signType;
        return $this;
    }
    
    public function getSign(): string
    {
        return $this->sign;
    }
    
    public function setSign(string $sign): self
    {
        $this->sign = $sign;
        return $this;
    }
    
    public function getTransDate(): string
    {
        return $this->transDate;
    }
    
    public function setTransDate(string $transDate): self
    {
        $this->transDate = $transDate;
        return $this;
    }
    
    public function getTransTime(): string
    {
        return $this->transTime;
    }
    
    public function setTransTime(string $transTime): self
    {
        $this->transTime = $transTime;
        return $this;
    }
    
    public function getVersion(): string
    {
        return $this->version;
    }
    
    public function setVersion(string $version): self
    {
        $this->version = $version;
        return $this;
    }
    
    public function getBizData(): string
    {
        return $this->bizData;
    }
    
    public function setBizData(string $bizData): self
    {
        $this->bizData = $bizData;
        return $this;
    }
    
    /**
     * 转换为数组
     */
    public function toArray(): array
    {
        $data = [
            'appId' => $this->appId,
            'transCode' => $this->transCode,
            'format' => $this->format,
            'charset' => $this->charset,
            'signType' => $this->signType,
            'transDate' => $this->transDate,
            'transTime' => $this->transTime,
            'version' => $this->version,
            'bizData' => $this->bizData
        ];
        
        if ($this->spAppId !== null) {
            $data['spAppId'] = $this->spAppId;
        }
        
        if ($this->sign !== null) {
            $data['sign'] = $this->sign;
        }
        
        return $data;
    }
    
    /**
     * 转换为JSON字符串
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }
}
