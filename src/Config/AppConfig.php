<?php

namespace AllinpayYunshangtong\Config;

/**
 * 应用配置文件
 * 基于通联支付KA客户版接口规范
 */
class AppConfig
{
    // 环境配置
    const ENV_TEST = 'test';
    const ENV_PROD = 'production';
    
    // 当前环境
    private $environment = self::ENV_TEST;
    
    // 测试环境配置
    private array $testConfig = [
        'url' => 'https://ibstest.allinpay.com/yst/yst-service-api',
        'appId' => '21803774682041868289',
        'spAppId' => '11879464722018709506',
        'secretKey' => '878427523d3525e070298d44481b8d2e',
        'privateKey' => 'MIGTAgEAMBMGByqGSM49AgEGCCqBHM9VAYItBHkwdwIBAQQgE12E+yZ6co3ZmT48Aslar9+TFu3/zrppSGTbq4nodYegCgYIKoEcz1UBgi2hRANCAATUryMfvHPyvqJe8UPzCNmswayea/u3X0TAAqbMExG2nZLywNwOG4ht33B0CHjobvX5efWtDxJl0A+SaH+pW1Ji',
        'publicKey' => 'MFkwEwYHKoZIzj0CAQYIKoEcz1UBgi0DQgAEu9LNkJlyLtjJxtQWIGlcZ/hyHt5eZ7LEH1nfOiK1H9HsE1cMPu5KK5jZVTtAyc7lPMXixUMirf6A3tMbuMbgqg=='
    ];
    
    // 生产环境配置
    private array $prodConfig = [
        'url' => 'https://ibsapi.allinpay.com/yst-service-api',
        'appId' => '',
        'spAppId' => '',
        'secretKey' => '',
        'privateKey' => '',
        'publicKey' => 'MFkwEwYHKoZIzj0CAQYIKoEcz1UBgi0DQgAE/VKHBem28IXD30yuZN1QcNgGE4gzqgd/eX1ZEouUleLNfrnQJkOs7LzAag3q10uaH/e9+5JyJDx3ULfKS4QZPw=='
    ];
    
    // 通用配置
    private array $commonConfig = [
        'format' => 'json',
        'charset' => 'UTF-8',
        'signType' => 'SM3withSM2',
        'version' => '1.0',
        'timeout' => 30
    ];
    
    public function __construct(string $environment = self::ENV_TEST)
    {
        $this->environment = $environment;
    }
    
    /**
     * 获取配置值
     */
    public function get(string $key): string
    {
        $config = $this->environment === self::ENV_TEST ? $this->testConfig : $this->prodConfig;
        
        if (isset($config[$key])) {
            return $config[$key];
        }
        
        if (isset($this->commonConfig[$key])) {
            return $this->commonConfig[$key];
        }
        
        throw new \InvalidArgumentException("配置键 '{$key}' 不存在");
    }
    
    /**
     * 获取API基础URL
     */
    public function getBaseUrl(): string
    {
        return $this->get('url');
    }
    
    /**
     * 获取交易接口URL
     */
    public function getTransactionUrl(): string
    {
        return $this->getBaseUrl() . '/tx/handle';
    }
    
    /**
     * 获取会员接口URL
     */
    public function getMemberUrl(): string
    {
        return $this->getBaseUrl() . '/tm/handle';
    }
    
    /**
     * 获取查询接口URL
     */
    public function getQueryUrl(): string
    {
        return $this->getBaseUrl() . '/tq/handle';
    }
    
    /**
     * 获取文件上传URL
     */
    public function getFileUploadUrl(): string
    {
        return $this->getBaseUrl() . '/file/upload';
    }
    
    /**
     * 获取文件下载URL
     */
    public function getFileDownloadUrl(): string
    {
        return $this->getBaseUrl() . '/file/download';
    }
    
    /**
     * 获取当前环境
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }
    
    /**
     * 设置环境
     */
    public function setEnvironment(string $environment): void
    {
        if (!in_array($environment, [self::ENV_TEST, self::ENV_PROD])) {
            throw new \InvalidArgumentException("无效的环境值: {$environment}");
        }
        $this->environment = $environment;
    }
    
    /**
     * 是否为测试环境
     */
    public function isTest(): bool
    {
        return $this->environment === self::ENV_TEST;
    }
    
    /**
     * 是否为生产环境
     */
    public function isProduction(): bool
    {
        return $this->environment === self::ENV_PROD;
    }
}
