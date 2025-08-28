<?php

namespace AllinpayYunshangtong;

use AllinpayYunshangtong\Config\AppConfig;
use AllinpayYunshangtong\User\UserService;
use AllinpayYunshangtong\Merchant\MerchantService;
use AllinpayYunshangtong\Order\OrderService;
use AllinpayYunshangtong\Common\QueryService;
use AllinpayYunshangtong\Common\FileService;

/**
 * 通联支付客户端主类
 * 整合所有服务，提供统一的接口
 */
class AllinpayClient
{
    private $config;
    private $userService;
    private $merchantService;
    private $orderService;
    private $queryService;
    private $fileService;
    
    public function __construct(string $environment = AppConfig::ENV_TEST)
    {
        $this->config = new AppConfig($environment);
        $this->userService = new UserService($this->config);
        $this->merchantService = new MerchantService($this->config);
        $this->orderService = new OrderService($this->config);
        $this->queryService = new QueryService($this->config);
        $this->fileService = new FileService($this->config);
    }
    
    /**
     * 获取配置
     */
    public function getConfig(): AppConfig
    {
        return $this->config;
    }
    
    /**
     * 获取用户服务
     */
    public function getUserService(): UserService
    {
        return $this->userService;
    }
    
    /**
     * 获取商家服务
     */
    public function getMerchantService(): MerchantService
    {
        return $this->merchantService;
    }
    
    /**
     * 获取订单服务
     */
    public function getOrderService(): OrderService
    {
        return $this->orderService;
    }
    
    /**
     * 获取查询服务
     */
    public function getQueryService(): QueryService
    {
        return $this->queryService;
    }
    
    /**
     * 获取文件服务
     */
    public function getFileService(): FileService
    {
        return $this->fileService;
    }
    
    /**
     * 快速创建个人会员
     */
    public function quickCreatePersonalMember(array $memberInfo)
    {
        return $this->userService->createPersonalMemberApply($memberInfo);
    }
    
    /**
     * 快速创建企业会员
     */
    public function quickCreateEnterpriseMember(array $memberInfo)
    {
        return $this->userService->createEnterpriseMember($memberInfo);
    }
    
    /**
     * 快速创建消费订单
     */
    public function quickCreateConsumeOrder(array $orderInfo)
    {
        return $this->orderService->createConsumptionOrder($orderInfo);
    }
    
    /**
     * 快速查询订单
     */
    public function quickQueryOrder(string $orderNo)
    {
        return $this->queryService->queryOrderStatus($orderNo);
    }
    
    /**
     * 快速查询账户余额
     */
    public function quickQueryAccountBalance(string $bizUserId)
    {
        return $this->queryService->queryAccountBalance($bizUserId);
    }
    
    /**
     * 快速上传文件
     */
    public function quickUploadFile(string $filePath, string $fileType, string $bizUserId = '')
    {
        return $this->fileService->uploadFile($filePath, $fileType, $bizUserId);
    }
    
    /**
     * 设置环境
     */
    public function setEnvironment(string $environment): self
    {
        $this->config = new AppConfig($environment);
        // 重新初始化所有服务
        $this->userService = new UserService($this->config);
        $this->merchantService = new MerchantService($this->config);
        $this->orderService = new OrderService($this->config);
        $this->queryService = new QueryService($this->config);
        $this->fileService = new FileService($this->config);
        
        return $this;
    }
    
    /**
     * 获取当前环境
     */
    public function getEnvironment(): string
    {
        return $this->config->getEnvironment();
    }
    
    /**
     * 是否为测试环境
     */
    public function isTest(): bool
    {
        return $this->config->isTest();
    }
    
    /**
     * 是否为生产环境
     */
    public function isProduction(): bool
    {
        return $this->config->isProduction();
    }
}
