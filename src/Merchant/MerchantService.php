<?php

namespace AllinpayNew\Merchant;

use AllinpayNew\Config\AppConfig;
use AllinpayNew\Utils\HttpClient;
use AllinpayNew\Utils\SignatureUtils;
use AllinpayNew\VO\BaseRequest;
use AllinpayNew\VO\BaseResponse;

/**
 * 商家服务类
 * 处理商家相关的业务逻辑，包括商户管理、终端管理等
 */
class MerchantService
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
     * 会员绑定收银宝商户
     * 接口代码：Member1017
     */
    public function bindCashierMerchant(array $bindInfo): BaseResponse
    {
        $request = new BaseRequest();
        $request->setAppId($this->config->get('appId'))
                ->setTransCode('Member1017')
                ->setBizData(json_encode($bindInfo, JSON_UNESCAPED_UNICODE));
        
        // 生成签名
        $sign = $this->signatureUtils->generateSign($request->toArray());
        $request->setSign($sign);
        
        // 发送请求
        $responseData = $this->httpClient->sendMember($request->toArray());
        
        return new BaseResponse($responseData);
    }
    
    /**
     * 终端信息管理
     * 接口代码：Member1018
     */
    public function manageTerminal(array $terminalInfo): BaseResponse
    {
        $request = new BaseRequest();
        $request->setAppId($this->config->get('appId'))
                ->setTransCode('Member1018')
                ->setBizData(json_encode($terminalInfo, JSON_UNESCAPED_UNICODE));
        
        // 生成签名
        $sign = $this->signatureUtils->generateSign($request->toArray());
        $request->setSign($sign);
        
        // 发送请求
        $responseData = $this->httpClient->sendMember($request->toArray());
        
        return new BaseResponse($responseData);
    }
    
    /**
     * 企业会员新增绑定对公户
     * 接口代码：Member1019
     */
    public function addCorporateAccount(array $accountInfo): BaseResponse
    {
        $request = new BaseRequest();
        $request->setAppId($this->config->get('appId'))
                ->setTransCode('Member1019')
                ->setBizData(json_encode($accountInfo, JSON_UNESCAPED_UNICODE));
        
        // 生成签名
        $sign = $this->signatureUtils->generateSign($request->toArray());
        $request->setSign($sign);
        
        // 发送请求
        $responseData = $this->httpClient->sendMember($request->toArray());
        
        return new BaseResponse($responseData);
    }
    
    /**
     * 企业会员信息修改
     * 接口代码：Member1023
     */
    public function updateEnterpriseMember(array $updateInfo): BaseResponse
    {
        $request = new BaseRequest();
        $request->setAppId($this->config->get('appId'))
                ->setTransCode('Member1023')
                ->setBizData(json_encode($updateInfo, JSON_UNESCAPED_UNICODE));
        
        // 生成签名
        $sign = $this->signatureUtils->generateSign($request->toArray());
        $request->setSign($sign);
        
        // 发送请求
        $responseData = $this->httpClient->sendMember($request->toArray());
        
        return new BaseResponse($responseData);
    }
    
    /**
     * 查询商户信息
     */
    public function queryMerchantInfo(string $merchantId): BaseResponse
    {
        $request = new BaseRequest();
        $request->setAppId($this->config->get('appId'))
                ->setTransCode('Member1030') // 使用会员查询接口
                ->setBizData(json_encode(['bizUserId' => $merchantId], JSON_UNESCAPED_UNICODE));
        
        // 生成签名
        $sign = $this->signatureUtils->generateSign($request->toArray());
        $request->setSign($sign);
        
        // 发送请求
        $responseData = $this->httpClient->sendMember($request->toArray());
        
        return new BaseResponse($responseData);
    }
    
    /**
     * 商户状态管理
     */
    public function updateMerchantStatus(string $merchantId, string $status): BaseResponse
    {
        $request = new BaseRequest();
        $request->setAppId($this->config->get('appId'))
                ->setTransCode('Member1023') // 使用会员信息修改接口
                ->setBizData(json_encode([
                    'bizUserId' => $merchantId,
                    'status' => $status
                ], JSON_UNESCAPED_UNICODE));
        
        // 生成签名
        $sign = $this->signatureUtils->generateSign($request->toArray());
        $request->setSign($sign);
        
        // 发送请求
        $responseData = $this->httpClient->sendMember($request->toArray());
        
        return new BaseResponse($responseData);
    }
}
