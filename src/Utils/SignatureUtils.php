<?php

namespace AllinpayYunshangtong\Utils;

use AllinpayYunshangtong\Config\AppConfig;

/**
 * 签名工具类
 * 基于通联支付KA客户版接口规范的签名机制
 */
class SignatureUtils
{
    private AppConfig $config;
    
    public function __construct(AppConfig $config)
    {
        $this->config = $config;
    }
    
    /**
     * 生成签名
     */
    public function generateSign(array $data): string
    {
        // 移除sign字段
        unset($data['sign']);
        
        // 按字段名排序
        ksort($data);
        
        // 构建签名字符串
        $signString = '';
        foreach ($data as $key => $value) {
            if ($value !== null && $value !== '') {
                $signString .= $key . '=' . $value . '&';
            }
        }
        
        // 移除最后的&符号
        $signString = rtrim($signString, '&');
        
        // 根据签名类型生成签名
        switch ($this->config->get('signType')) {
            case 'SM3withSM2':
                return $this->generateSM2Sign($signString);
            case 'RSA':
                return $this->generateRSASign($signString);
            default:
                throw new \InvalidArgumentException('不支持的签名类型: ' . $this->config->get('signType'));
        }
    }
    
    /**
     * 生成SM2签名
     */
    private function generateSM2Sign(string $signString): string
    {
        // 这里需要根据具体的SM2库实现
        // 暂时返回一个示例签名
        $privateKey = $this->config->get('privateKey');
        
        // 使用SM2算法生成签名
        // 实际实现需要调用相应的SM2库
        return hash('sha256', $signString . $privateKey);
    }
    
    /**
     * 生成RSA签名
     */
    private function generateRSASign(string $signString): string
    {
        $privateKey = $this->config->get('privateKey');
        
        // 创建私钥资源
        $privateKeyResource = openssl_pkey_get_private($privateKey);
        if (!$privateKeyResource) {
            throw new \RuntimeException('私钥格式错误');
        }
        
        // 生成签名
        $signature = '';
        if (!openssl_sign($signString, $signature, $privateKeyResource, OPENSSL_ALGO_SHA256)) {
            throw new \RuntimeException('签名生成失败');
        }
        
        // 释放私钥资源
        openssl_free_key($privateKeyResource);
        
        return base64_encode($signature);
    }
    
    /**
     * 验证签名
     */
    public function verifySign(array $data, string $sign): bool
    {
        $expectedSign = $this->generateSign($data);
        return $expectedSign === $sign;
    }
    
    /**
     * 验证通联支付响应签名
     */
    public function verifyAllinpaySign(array $responseData): bool
    {
        if (!isset($responseData['sign'])) {
            return false;
        }
        
        $sign = $responseData['sign'];
        $publicKey = $this->config->get('publicKey');
        
        // 移除sign字段进行验签
        $dataForVerify = $responseData;
        unset($dataForVerify['sign']);
        
        // 按字段名排序
        ksort($dataForVerify);
        
        // 构建签名字符串
        $signString = '';
        foreach ($dataForVerify as $key => $value) {
            if ($value !== null && $value !== '') {
                $signString .= $key . '=' . $value . '&';
            }
        }
        
        $signString = rtrim($signString, '&');
        
        // 根据签名类型验证签名
        switch ($this->config->get('signType')) {
            case 'SM3withSM2':
                return $this->verifySM2Sign($signString, $sign, $publicKey);
            case 'RSA':
                return $this->verifyRSASign($signString, $sign, $publicKey);
            default:
                return false;
        }
    }
    
    /**
     * 验证SM2签名
     */
    private function verifySM2Sign(string $signString, string $sign, string $publicKey): bool
    {
        // 实际实现需要调用相应的SM2库
        // 暂时返回true作为示例
        return true;
    }
    
    /**
     * 验证RSA签名
     */
    private function verifyRSASign(string $signString, string $sign, string $publicKey): bool
    {
        // 创建公钥资源
        $publicKeyResource = openssl_pkey_get_public($publicKey);
        if (!$publicKeyResource) {
            return false;
        }
        
        // 验证签名
        $result = openssl_verify($signString, base64_decode($sign), $publicKeyResource, OPENSSL_ALGO_SHA256);
        
        // 释放公钥资源
        openssl_free_key($publicKeyResource);
        
        return $result === 1;
    }
}
