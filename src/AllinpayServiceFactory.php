<?php
namespace Allinpay\YunshangTong;

require_once __DIR__ . '/../vendor/autoload.php';

use Allinpay\YunshangTong\Config\DemoConfig;
use Exception;
use Allinpay\YunshangTong\Member\MemberService;
use Allinpay\YunshangTong\Payment\PaymentService;

/**
 * 通联支付服务工厂类
 * 
 * 统一管理会员服务和支付服务，提供便捷的服务实例获取方法
 * 根据业务流程图分离不同角色的开户流程：
 * - 买方用户：个人/企业用户开户流程
 * - 平台商户：会员管理和支付处理
 * - 云商通二代：统一的服务入口
 * 
 * @author CodeBuddy
 * @version 1.0
 * @date 2025/01/13
 */
class AllinpayServiceFactory
{
    /** @var DemoConfig 配置对象 */
    private DemoConfig $config;
    
    /** @var MemberService|null 会员服务实例 */
    private ?MemberService $memberService = null;
    
    /** @var PaymentService|null 支付服务实例 */
    private ?PaymentService $paymentService = null;

    /**
     * 构造函数
     * 
     * @param DemoConfig|null $config 配置对象，为空时使用默认配置
     * @throws Exception
     */
    public function __construct(?DemoConfig $config = null)
    {
        $this->config = $config ?? new DemoConfig();
    }

    /**
     * 获取会员服务实例
     * 
     * @return MemberService
     * @throws Exception
     */
    public function getMemberService(): MemberService
    {
        if ($this->memberService === null) {
            $this->memberService = new MemberService($this->config);
        }
        
        return $this->memberService;
    }

    /**
     * 获取支付服务实例
     * 
     * @return PaymentService
     * @throws Exception
     */
    public function getPaymentService(): PaymentService
    {
        if ($this->paymentService === null) {
            $this->paymentService = new PaymentService($this->config);
        }
        
        return $this->paymentService;
    }

    /**
     * 获取配置对象
     * 
     * @return DemoConfig
     */
    public function getConfig(): DemoConfig
    {
        return $this->config;
    }

    /**
     * 设置配置对象
     * 
     * @param DemoConfig $config
     * @return self
     */
    public function setConfig(DemoConfig $config): self
    {
        $this->config = $config;
        
        // 重置服务实例，使其使用新配置
        $this->memberService = null;
        $this->paymentService = null;
        
        return $this;
    }

    // ==================== 便捷方法 - 买方用户开户流程 ====================

    /**
     * 个人用户完整开户流程
     * 
     * @param array $personalData 个人用户数据
     * @return array 开户结果
     * @throws Exception
     */
    public function personalUserAccountOpening(array $personalData): array
    {
        $memberService = $this->getMemberService();
        
        // 1. 个人用户起草开通通联记账户申请
        $registerResult = $memberService->registerPersonalMember($personalData);
        
        if (!$registerResult['success']) {
            return [
                'success' => false,
                'step' => 'register',
                'message' => '个人会员注册失败: ' . $registerResult['message'],
                'data' => $registerResult
            ];
        }

        // 2. 实名信息认证成功，银行卡信息验证成功，开通记账户
        // 注册成功后，系统会自动进行实名认证和银行卡验证
        
        return [
            'success' => true,
            'step' => 'completed',
            'message' => '个人用户开户流程完成',
            'data' => $registerResult
        ];
    }

    /**
     * 企业用户完整开户流程
     * 
     * @param array $companyData 企业数据
     * @return array 开户结果
     * @throws Exception
     */
    public function companyUserAccountOpening(array $companyData): array
    {
        $memberService = $this->getMemberService();
        
        // 1. 企业用户起草开通通联记账户申请
        $registerResult = $memberService->registerCompanyMember($companyData);
        
        if (!$registerResult['success']) {
            return [
                'success' => false,
                'step' => 'register',
                'message' => '企业会员注册失败: ' . $registerResult['message'],
                'data' => $registerResult
            ];
        }

        // 2. 企业工商信息验证成功，法人实名信息验证成功，开通记账户
        // 注册成功后，系统会自动进行工商信息验证和法人实名验证
        
        return [
            'success' => true,
            'step' => 'completed',
            'message' => '企业用户开户流程完成',
            'data' => $registerResult
        ];
    }

    // ==================== 便捷方法 - 平台商户业务流程 ====================

    /**
     * 获取会员开户结果
     * 
     * @param string $signNum 会员编号
     * @param string $bizOrderCode 业务订单号
     * @return array 开户结果
     * @throws Exception
     */
    public function getMemberAccountResult(string $signNum, string $bizOrderCode): array
    {
        $memberService = $this->getMemberService();
        return $memberService->getCompanyAccountResult($signNum, $bizOrderCode);
    }

    /**
     * 企业会员开通支付账户流程
     * 
     * @param array $paymentAccountData 支付账户数据
     * @return array 开通结果
     * @throws Exception
     */
    public function companyPaymentAccountOpening(array $paymentAccountData): array
    {
        $memberService = $this->getMemberService();
        
        // 1. 企业会员开通支付账户申请
        $openResult = $memberService->openCompanyPaymentAccount($paymentAccountData);
        
        if (!$openResult['success']) {
            return [
                'success' => false,
                'step' => 'apply',
                'message' => '支付账户开通申请失败: ' . $openResult['message'],
                'data' => $openResult
            ];
        }

        return [
            'success' => true,
            'step' => 'applied',
            'message' => '支付账户开通申请成功，等待审核',
            'data' => $openResult
        ];
    }

    /**
     * 获取会员开户受理状态
     * 
     * @param string $signNum 会员编号
     * @param string $bizOrderCode 业务订单号
     * @return array 受理状态
     * @throws Exception
     */
    public function getMemberAccountStatus(string $signNum, string $bizOrderCode): array
    {
        $memberService = $this->getMemberService();
        return $memberService->getMemberAccountStatus($signNum, $bizOrderCode);
    }

    // ==================== 便捷方法 - 支付业务流程 ====================

    /**
     * 对公账户转入打款支付账户号
     * 
     * @param array $transferData 转账数据
     * @return array 转账结果
     * @throws Exception
     */
    public function publicAccountTransfer(array $transferData): array
    {
        $paymentService = $this->getPaymentService();
        return $paymentService->transfer($transferData);
    }

    /**
     * 划款入账验证
     * 
     * @param string $reqTraceNum 商户订单号
     * @param string|null $signNum 会员编号
     * @return array 验证结果
     * @throws Exception
     */
    public function verifyTransferAccount(string $reqTraceNum, ?string $signNum = null): array
    {
        $paymentService = $this->getPaymentService();
        return $paymentService->queryTransaction($reqTraceNum, $signNum);
    }

    /**
     * 支付账户开户审核详情
     * 
     * @param string $signNum 会员编号
     * @param string $bizOrderCode 业务订单号
     * @return array 审核详情
     * @throws Exception
     */
    public function getPaymentAccountAuditDetail(string $signNum, string $bizOrderCode): array
    {
        $memberService = $this->getMemberService();
        return $memberService->getPaymentAccountAuditDetail($signNum, $bizOrderCode);
    }

    // ==================== 静态工厂方法 ====================

    /**
     * 创建默认服务工厂实例
     * 
     * @return self
     * @throws Exception
     */
    public static function create(): self
    {
        return new self();
    }

    /**
     * 使用自定义配置创建服务工厂实例
     * 
     * @param DemoConfig $config 配置对象
     * @return self
     * @throws Exception
     */
    public static function createWithConfig(DemoConfig $config): self
    {
        return new self($config);
    }

    /**
     * 从环境变量创建服务工厂实例
     * 
     * @return self
     * @throws Exception
     */
    public static function createFromEnv(): self
    {
        $config = new DemoConfig();
        
        // 从环境变量读取配置
        if ($appId = getenv('ALLINPAY_APP_ID')) {
            $config->setAppId($appId);
        }
        if ($spAppId = getenv('ALLINPAY_SP_APP_ID')) {
            $config->setSpAppId($spAppId);
        }
        if ($secretKey = getenv('ALLINPAY_SECRET_KEY')) {
            $config->setSecretKey($secretKey);
        }
        if ($privateKey = getenv('ALLINPAY_PRIVATE_KEY')) {
            $config->setPrivateKeyStr($privateKey);
        }
        if ($notifyUrl = getenv('ALLINPAY_NOTIFY_URL')) {
            $config->setNotifyUrl($notifyUrl);
        }
        
        return new self($config);
    }
}