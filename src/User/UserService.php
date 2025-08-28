<?php

namespace AllinpayYunshangtong\User;

use AllinpayYunshangtong\Config\AppConfig;
use AllinpayYunshangtong\Utils\HttpClient;
use AllinpayYunshangtong\Utils\SignatureUtils;
use AllinpayYunshangtong\VO\BaseRequest;
use AllinpayYunshangtong\VO\BaseResponse;

/**
 * 用户服务类
 * 处理用户相关的业务逻辑，包括会员开户、实名认证等
 */
class UserService
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
     * 企业会员实名开户
     * 接口代码：Member1010
     */
    public function createEnterpriseMember(array $memberInfo): BaseResponse
    {
        $request = new BaseRequest();
        $request->setAppId($this->config->get('appId'))
                ->setTransCode('Member1010')
                ->setBizData(json_encode($memberInfo, JSON_UNESCAPED_UNICODE));
        
        // 生成签名
        $sign = $this->signatureUtils->generateSign($request->toArray());
        $request->setSign($sign);
        
        // 发送请求
        $responseData = $this->httpClient->sendMember($request->toArray());
        
        return new BaseResponse($responseData);
    }
    
    /**
     * 个人会员实名及绑卡（申请）
     * 接口代码：Member1020
     */
    public function createPersonalMemberApply(array $memberInfo): BaseResponse
    {
        $request = new BaseRequest();
        $request->setAppId($this->config->get('appId'))
                ->setTransCode('Member1020')
                ->setBizData(json_encode($memberInfo, JSON_UNESCAPED_UNICODE));
        
        // 生成签名
        $sign = $this->signatureUtils->generateSign($request->toArray());
        $request->setSign($sign);
        
        // 发送请求
        $responseData = $this->httpClient->sendMember($request->toArray());
        
        return new BaseResponse($responseData);
    }
    
    /**
     * 个人会员实名及绑卡（确认）
     * 接口代码：Member1021
     */
    public function createPersonalMemberConfirm(array $confirmInfo): BaseResponse
    {
        $request = new BaseRequest();
        $request->setAppId($this->config->get('appId'))
                ->setTransCode('Member1021')
                ->setBizData(json_encode($confirmInfo, JSON_UNESCAPED_UNICODE));
        
        // 生成签名
        $sign = $this->signatureUtils->generateSign($request->toArray());
        $request->setSign($sign);
        
        // 发送请求
        $responseData = $this->httpClient->sendMember($request->toArray());
        
        return new BaseResponse($responseData);
    }
    
    /**
     * 查询会员信息
     * 接口代码：Member1030
     */
    public function queryMemberInfo(string $bizUserId): BaseResponse
    {
        $request = new BaseRequest();
        $request->setAppId($this->config->get('appId'))
                ->setTransCode('Member1030')
                ->setBizData(json_encode(['bizUserId' => $bizUserId], JSON_UNESCAPED_UNICODE));
        
        // 生成签名
        $sign = $this->signatureUtils->generateSign($request->toArray());
        $request->setSign($sign);
        
        // 发送请求
        $responseData = $this->httpClient->sendMember($request->toArray());
        
        return new BaseResponse($responseData);
    }
    
    /**
     * 会员绑定手机号申请
     * 接口代码：Member1026
     */
    public function bindPhoneApply(array $bindInfo): BaseResponse
    {
        $request = new BaseRequest();
        $request->setAppId($this->config->get('appId'))
                ->setTransCode('Member1026')
                ->setBizData(json_encode($bindInfo, JSON_UNESCAPED_UNICODE));
        
        // 生成签名
        $sign = $this->signatureUtils->generateSign($request->toArray());
        $request->setSign($sign);
        
        // 发送请求
        $responseData = $this->httpClient->sendMember($request->toArray());
        
        return new BaseResponse($responseData);
    }
    
    /**
     * 确认绑定/解绑手机号
     * 接口代码：Member1027
     */
    public function confirmBindPhone(array $confirmInfo): BaseResponse
    {
        $request = new BaseRequest();
        $request->setAppId($this->config->get('appId'))
                ->setTransCode('Member1027')
                ->setBizData(json_encode($confirmInfo, JSON_UNESCAPED_UNICODE));
        
        // 生成签名
        $sign = $this->signatureUtils->generateSign($request->toArray());
        $request->setSign($sign);
        
        // 发送请求
        $responseData = $this->httpClient->sendMember($request->toArray());
        
        return new BaseResponse($responseData);
    }
    
    /**
     * 会员线上协议签约申请
     * 接口代码：Member1029
     */
    public function signAgreementApply(array $agreementInfo): BaseResponse
    {
        $request = new BaseRequest();
        $request->setAppId($this->config->get('appId'))
                ->setTransCode('Member1029')
                ->setBizData(json_encode($agreementInfo, JSON_UNESCAPED_UNICODE));
        
        // 生成签名
        $sign = $this->signatureUtils->generateSign($request->toArray());
        $request->setSign($sign);
        
        // 发送请求
        $responseData = $this->httpClient->sendMember($request->toArray());
        
        return new BaseResponse($responseData);
    }
}
