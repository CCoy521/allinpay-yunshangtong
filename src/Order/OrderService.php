<?php

namespace AllinpayNew\Order;

use AllinpayNew\Config\AppConfig;
use AllinpayNew\Utils\HttpClient;
use AllinpayNew\Utils\SignatureUtils;
use AllinpayNew\VO\BaseRequest;
use AllinpayNew\VO\BaseResponse;

/**
 * 订单服务类
 * 处理订单相关的业务逻辑，包括支付、退款、查询等
 */
class OrderService
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
     * 消费申请
     * 接口代码：Tx3010
     */
    public function createConsumptionOrder(array $orderInfo): BaseResponse
    {
        $request = new BaseRequest();
        $request->setAppId($this->config->get('appId'))
                ->setTransCode('Tx3010')
                ->setBizData(json_encode($orderInfo, JSON_UNESCAPED_UNICODE));
        
        // 生成签名
        $sign = $this->signatureUtils->generateSign($request->toArray());
        $request->setSign($sign);
        
        // 发送请求
        $responseData = $this->httpClient->sendTransaction($request->toArray());
        
        return new BaseResponse($responseData);
    }
    
    /**
     * 担保消费
     * 接口代码：Tx3011
     */
    public function createGuaranteedOrder(array $orderInfo): BaseResponse
    {
        $request = new BaseRequest();
        $request->setAppId($this->config->get('appId'))
                ->setTransCode('Tx3011')
                ->setBizData(json_encode($orderInfo, JSON_UNESCAPED_UNICODE));
        
        // 生成签名
        $sign = $this->signatureUtils->generateSign($request->toArray());
        $request->setSign($sign);
        
        // 发送请求
        $responseData = $this->httpClient->sendTransaction($request->toArray());
        
        return new BaseResponse($responseData);
    }
    
    /**
     * 转账申请
     * 接口代码：Tx3012
     */
    public function createTransferOrder(array $transferInfo): BaseResponse
    {
        $request = new BaseRequest();
        $request->setAppId($this->config->get('appId'))
                ->setTransCode('Tx3012')
                ->setBizData(json_encode($transferInfo, JSON_UNESCAPED_UNICODE));
        
        // 生成签名
        $sign = $this->signatureUtils->generateSign($request->toArray());
        $request->setSign($sign);
        
        // 发送请求
        $responseData = $this->httpClient->sendTransaction($request->toArray());
        
        return new BaseResponse($responseData);
    }
    
    /**
     * 提现申请
     * 接口代码：Tx3013
     */
    public function createWithdrawOrder(array $withdrawInfo): BaseResponse
    {
        $request = new BaseRequest();
        $request->setAppId($this->config->get('appId'))
                ->setTransCode('Tx3013')
                ->setBizData(json_encode($withdrawInfo, JSON_UNESCAPED_UNICODE));
        
        // 生成签名
        $sign = $this->signatureUtils->generateSign($request->toArray());
        $request->setSign($sign);
        
        // 发送请求
        $responseData = $this->httpClient->sendTransaction($request->toArray());
        
        return new BaseResponse($responseData);
    }
    
    /**
     * 充值申请
     * 接口代码：Tx3014
     */
    public function createRechargeOrder(array $rechargeInfo): BaseResponse
    {
        $request = new BaseRequest();
        $request->setAppId($this->config->get('appId'))
                ->setTransCode('Tx3014')
                ->setBizData(json_encode($rechargeInfo, JSON_UNESCAPED_UNICODE));
        
        // 生成签名
        $sign = $this->signatureUtils->generateSign($request->toArray());
        $request->setSign($sign);
        
        // 发送请求
        $responseData = $this->httpClient->sendTransaction($request->toArray());
        
        return new BaseResponse($responseData);
    }
    
    /**
     * 退款申请
     * 接口代码：Tx3015
     */
    public function createRefundOrder(array $refundInfo): BaseResponse
    {
        $request = new BaseRequest();
        $request->setAppId($this->config->get('appId'))
                ->setTransCode('Tx3015')
                ->setBizData(json_encode($refundInfo, JSON_UNESCAPED_UNICODE));
        
        // 生成签名
        $sign = $this->signatureUtils->generateSign($request->toArray());
        $request->setSign($sign);
        
        // 发送请求
        $responseData = $this->httpClient->sendTransaction($request->toArray());
        
        return new BaseResponse($responseData);
    }
    
    /**
     * 订单关闭
     * 接口代码：Tx3016
     */
    public function closeOrder(string $orderId, string $reason = ''): BaseResponse
    {
        $request = new BaseRequest();
        $request->setAppId($this->config->get('appId'))
                ->setTransCode('Tx3016')
                ->setBizData(json_encode([
                    'orderId' => $orderId,
                    'reason' => $reason
                ], JSON_UNESCAPED_UNICODE));
        
        // 生成签名
        $sign = $this->signatureUtils->generateSign($request->toArray());
        $request->setSign($sign);
        
        // 发送请求
        $responseData = $this->httpClient->sendTransaction($request->toArray());
        
        return new BaseResponse($responseData);
    }
    
    /**
     * 确认支付（后台+短信验证码确认）
     * 接口代码：Tx3017
     */
    public function confirmPayment(array $confirmInfo): BaseResponse
    {
        $request = new BaseRequest();
        $request->setAppId($this->config->get('appId'))
                ->setTransCode('Tx3017')
                ->setBizData(json_encode($confirmInfo, JSON_UNESCAPED_UNICODE));
        
        // 生成签名
        $sign = $this->signatureUtils->generateSign($request->toArray());
        $request->setSign($sign);
        
        // 发送请求
        $responseData = $this->httpClient->sendTransaction($request->toArray());
        
        return new BaseResponse($responseData);
    }
    
    /**
     * 储值卡订单核销
     * 接口代码：Tx3018
     */
    public function consumeStoredValueCard(array $consumeInfo): BaseResponse
    {
        $request = new BaseRequest();
        $request->setAppId($this->config->get('appId'))
                ->setTransCode('Tx3018')
                ->setBizData(json_encode($consumeInfo, JSON_UNESCAPED_UNICODE));
        
        // 生成签名
        $sign = $this->signatureUtils->generateSign($request->toArray());
        $request->setSign($sign);
        
        // 发送请求
        $responseData = $this->httpClient->sendTransaction($request->toArray());
        
        return new BaseResponse($responseData);
    }
}
