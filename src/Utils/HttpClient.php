<?php

namespace AllinpayYunshangtong\Utils;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use AllinpayYunshangtong\Config\AppConfig;

/**
 * HTTP客户端工具类
 * 基于Guzzle HTTP客户端，用于与通联支付API通信
 */
class HttpClient
{
    private Client $client;
    private AppConfig $config;
    
    public function __construct(AppConfig $config)
    {
        $this->config = $config;
        $this->client = new Client([
            'timeout' => $config->get('timeout'),
            'verify' => false, // 测试环境可能需要关闭SSL验证
            'headers' => [
                'Content-Type' => 'application/json',
                'Accept' => 'application/json'
            ]
        ]);
    }
    
    /**
     * 发送POST请求
     */
    public function post(string $url, array $data): array
    {
        try {
            $response = $this->client->post($url, [
                'json' => $data
            ]);
            
            $responseData = json_decode($response->getBody()->getContents(), true);
            
            if (json_last_error() !== JSON_ERROR_NONE) {
                throw new \RuntimeException('响应数据解析失败: ' . json_last_error_msg());
            }
            
            return $responseData;
            
        } catch (GuzzleException $e) {
            throw new \RuntimeException('HTTP请求失败: ' . $e->getMessage(), 0, $e);
        }
    }
    
    /**
     * 发送交易请求
     */
    public function sendTransaction(array $data): array
    {
        return $this->post($this->config->getTransactionUrl(), $data);
    }
    
    /**
     * 发送会员请求
     */
    public function sendMember(array $data): array
    {
        return $this->post($this->config->getMemberUrl(), $data);
    }
    
    /**
     * 发送查询请求
     */
    public function sendQuery(array $data): array
    {
        return $this->post($this->config->getQueryUrl(), $data);
    }
    
    /**
     * 发送文件上传请求
     */
    public function sendFileUpload(array $data): array
    {
        return $this->post($this->config->getFileUploadUrl(), $data);
    }
    
    /**
     * 发送文件下载请求
     */
    public function sendFileDownload(array $data): array
    {
        return $this->post($this->config->getFileDownloadUrl(), $data);
    }
    
    /**
     * 获取HTTP客户端实例
     */
    public function getClient(): Client
    {
        return $this->client;
    }
}
