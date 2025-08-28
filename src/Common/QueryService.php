<?php

namespace AllinpayNew\Common;

use AllinpayNew\Config\AppConfig;
use AllinpayNew\Utils\HttpClient;
use AllinpayNew\Utils\SignatureUtils;
use AllinpayNew\VO\BaseRequest;
use AllinpayNew\VO\BaseResponse;

/**
 * 公共查询服务类
 * 处理各种查询业务逻辑，包括订单查询、账户查询、对账文件下载等
 */
class QueryService
{
    private AppConfig $config;
    private HttpClient $httpClient;
    private SignatureUtils $signatureUtils;
    
    public function __construct(AppConfig $config)
    {
        $this->config = $config;
        $this->httpClient = new HttpClient($config);
        $this->signatureUtils = new SignatureUtils($config);
    }
    
    /**
     * 订单状态查询
     * 接口代码：Tq3001
     */
    public function queryOrderStatus(string $orderId): BaseResponse
    {
        $request = new BaseRequest();
        $request->setAppId($this->config->get('appId'))
                ->setTransCode('Tq3001')
                ->setBizData(json_encode(['orderId' => $orderId], JSON_UNESCAPED_UNICODE));
        
        // 生成签名
        $sign = $this->signatureUtils->generateSign($request->toArray());
        $request->setSign($sign);
        
        // 发送请求
        $responseData = $this->httpClient->sendQuery($request->toArray());
        
        return new BaseResponse($responseData);
    }
    
    /**
     * 订单详情查询
     * 接口代码：Tq3002
     */
    public function queryOrderDetail(string $orderId): BaseResponse
    {
        $request = new BaseRequest();
        $request->setAppId($this->config->get('appId'))
                ->setTransCode('Tq3002')
                ->setBizData(json_encode(['orderId' => $orderId], JSON_UNESCAPED_UNICODE));
        
        // 生成签名
        $sign = $this->signatureUtils->generateSign($request->toArray());
        $request->setSign($sign);
        
        // 发送请求
        $responseData = $this->httpClient->sendQuery($request->toArray());
        
        return new BaseResponse($responseData);
    }
    
    /**
     * 账户余额查询
     * 接口代码：Tq4001
     */
    public function queryAccountBalance(string $bizUserId): BaseResponse
    {
        $request = new BaseRequest();
        $request->setAppId($this->config->get('appId'))
                ->setTransCode('Tq4001')
                ->setBizData(json_encode(['bizUserId' => $bizUserId], JSON_UNESCAPED_UNICODE));
        
        // 生成签名
        $sign = $this->signatureUtils->generateSign($request->toArray());
        $request->setSign($sign);
        
        // 发送请求
        $responseData = $this->httpClient->sendQuery($request->toArray());
        
        return new BaseResponse($responseData);
    }
    
    /**
     * 会员账户明细查询
     * 接口代码：Tq4002
     */
    public function queryAccountDetail(string $bizUserId, string $startDate, string $endDate, int $page = 1, int $pageSize = 20): BaseResponse
    {
        $request = new BaseRequest();
        $request->setAppId($this->config->get('appId'))
                ->setTransCode('Tq4002')
                ->setBizData(json_encode([
                    'bizUserId' => $bizUserId,
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'page' => $page,
                    'pageSize' => $pageSize
                ], JSON_UNESCAPED_UNICODE));
        
        // 生成签名
        $sign = $this->signatureUtils->generateSign($request->toArray());
        $request->setSign($sign);
        
        // 发送请求
        $responseData = $this->httpClient->sendQuery($request->toArray());
        
        return new BaseResponse($responseData);
    }
    
    /**
     * 平台资金查询
     * 接口代码：Tq4003
     */
    public function queryPlatformFunds(): BaseResponse
    {
        $request = new BaseRequest();
        $request->setAppId($this->config->get('appId'))
                ->setTransCode('Tq4003')
                ->setBizData(json_encode([], JSON_UNESCAPED_UNICODE));
        
        // 生成签名
        $sign = $this->signatureUtils->generateSign($request->toArray());
        $request->setSign($sign);
        
        // 发送请求
        $responseData = $this->httpClient->sendQuery($request->toArray());
        
        return new BaseResponse($responseData);
    }
    
    /**
     * 银行账户收支明细查询
     * 接口代码：Tq4004
     */
    public function queryBankAccountDetail(string $bizUserId, string $startDate, string $endDate, int $page = 1, int $pageSize = 20): BaseResponse
    {
        $request = new BaseRequest();
        $request->setAppId($this->config->get('appId'))
                ->setTransCode('Tq4004')
                ->setBizData(json_encode([
                    'bizUserId' => $bizUserId,
                    'startDate' => $startDate,
                    'endDate' => $endDate,
                    'page' => $page,
                    'pageSize' => $pageSize
                ], JSON_UNESCAPED_UNICODE));
        
        // 生成签名
        $sign = $this->signatureUtils->generateSign($request->toArray());
        $request->setSign($sign);
        
        // 发送请求
        $responseData = $this->httpClient->sendQuery($request->toArray());
        
        return new BaseResponse($responseData);
    }
    
    /**
     * 应用集合对账文件下载
     * 接口代码：Tq4005
     */
    public function downloadReconciliationFile(string $fileDate, string $fileType): BaseResponse
    {
        $request = new BaseRequest();
        $request->setAppId($this->config->get('appId'))
                ->setTransCode('Tq4005')
                ->setBizData(json_encode([
                    'fileDate' => $fileDate,
                    'fileType' => $fileType
                ], JSON_UNESCAPED_UNICODE));
        
        // 生成签名
        $sign = $this->signatureUtils->generateSign($request->toArray());
        $request->setSign($sign);
        
        // 发送请求
        $responseData = $this->httpClient->sendQuery($request->toArray());
        
        return new BaseResponse($responseData);
    }
    
    /**
     * 电子回单下载
     * 接口代码：Tq4006
     */
    public function downloadElectronicReceipt(string $orderId): BaseResponse
    {
        $request = new BaseRequest();
        $request->setAppId($this->config->get('appId'))
                ->setTransCode('Tq4006')
                ->setBizData(json_encode(['orderId' => $orderId], JSON_UNESCAPED_UNICODE));
        
        // 生成签名
        $sign = $this->signatureUtils->generateSign($request->toArray());
        $request->setSign($sign);
        
        // 发送请求
        $responseData = $this->httpClient->sendQuery($request->toArray());
        
        return new BaseResponse($responseData);
    }
    
    /**
     * 根据授权码(付款码)获取用户ID
     * 接口代码：Tq4007
     */
    public function getUserByAuthCode(string $authCode): BaseResponse
    {
        $request = new BaseRequest();
        $request->setAppId($this->config->get('appId'))
                ->setTransCode('Tq4007')
                ->setBizData(json_encode(['authCode' => $authCode], JSON_UNESCAPED_UNICODE));
        
        // 生成签名
        $sign = $this->signatureUtils->generateSign($request->toArray());
        $request->setSign($sign);
        
        // 发送请求
        $responseData = $this->httpClient->sendQuery($request->toArray());
        
        return new BaseResponse($responseData);
    }
}
