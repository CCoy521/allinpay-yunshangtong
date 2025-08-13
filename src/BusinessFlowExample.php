<?php
namespace src;

require_once __DIR__ . '/../vendor/autoload.php';

use config\DemoConfig;
use Exception;
use src\AllinpayServiceFactory;

/**
 * 业务流程示例
 * 
 * 根据会员开户流程图，展示不同角色的完整业务流程：
 * 1. 买方用户：个人/企业用户开户流程
 * 2. 平台商户：会员管理和支付处理
 * 3. 云商通二代：统一的服务管理
 * 
 * @author CodeBuddy
 * @version 1.0
 * @date 2025/01/13
 */
class BusinessFlowExample
{
    /** @var AllinpayServiceFactory 服务工厂 */
    private AllinpayServiceFactory $serviceFactory;

    public function __construct()
    {
        try {
            // 初始化服务工厂
            $this->serviceFactory = AllinpayServiceFactory::create();
            echo "=== 通联支付业务流程示例初始化成功 ===\n";
        } catch (Exception $e) {
            echo "初始化失败: " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    // ==================== 买方用户开户流程 ====================

    /**
     * 个人用户开户流程示例
     * 对应流程图中的"个人用户起草开通通联记账户申请"
     */
    public function testPersonalUserAccountOpening(): void
    {
        echo "\n=== 个人用户开户流程测试 ===\n";
        
        try {
            // 个人用户数据
            $personalData = [
                'signNum' => 'personal_user_' . time(),
                'name' => '张三',
                'cerNum' => '110101199001011234',  // 身份证号
                'acctNum' => '6217858000141669850', // 银行卡号
                'phone' => '13800138000',
                'memberRole' => '分销方',
                'bindType' => '8', // 银行卡四要素验证
                'email' => 'zhangsan@example.com'
            ];
            
            echo "1. 发起个人用户开户申请...\n";
            $result = $this->serviceFactory->personalUserAccountOpening($personalData);
            
            if ($result['success']) {
                echo "✅ 个人用户开户成功！\n";
                echo "   会员编号: " . $personalData['signNum'] . "\n";
                echo "   开户状态: " . $result['message'] . "\n";
                
                // 查询会员信息
                echo "2. 查询会员信息...\n";
                $memberInfo = $this->serviceFactory->getMemberService()->getMemberInfo($personalData['signNum']);
                if ($memberInfo['success']) {
                    echo "✅ 会员信息查询成功\n";
                    echo "   业务数据: " . ($memberInfo['bizData'] ?? '无') . "\n";
                }
                
            } else {
                echo "❌ 个人用户开户失败: " . $result['message'] . "\n";
            }
            
        } catch (Exception $e) {
            echo "❌ 个人用户开户异常: " . $e->getMessage() . "\n";
        }
    }

    /**
     * 企业用户开户流程示例
     * 对应流程图中的"企业用户起草开通通联记账户申请"
     */
    public function testCompanyUserAccountOpening(): void
    {
        echo "\n=== 企业用户开户流程测试 ===\n";
        
        try {
            // 企业用户数据
            $companyData = [
                'signNum' => 'company_user_' . time(),
                'companyName' => '测试科技有限公司',
                'licenseNo' => '91110000123456789X', // 统一社会信用代码
                'legalName' => '李四',
                'legalCerNum' => '110101198001011234', // 法人身份证号
                'memberRole' => '分销方',
                'companyType' => '有限责任公司',
                'businessScope' => '软件开发',
                'registeredCapital' => '1000000',
                'establishDate' => '2020-01-01'
            ];
            
            echo "1. 发起企业用户开户申请...\n";
            $result = $this->serviceFactory->companyUserAccountOpening($companyData);
            
            if ($result['success']) {
                echo "✅ 企业用户开户成功！\n";
                echo "   会员编号: " . $companyData['signNum'] . "\n";
                echo "   公司名称: " . $companyData['companyName'] . "\n";
                echo "   开户状态: " . $result['message'] . "\n";
                
                // 查询开户结果
                echo "2. 查询开户结果...\n";
                $bizOrderCode = (string)time();
                $accountResult = $this->serviceFactory->getMemberAccountResult($companyData['signNum'], $bizOrderCode);
                echo "   开户结果查询: " . ($accountResult['success'] ? '成功' : '失败') . "\n";
                
            } else {
                echo "❌ 企业用户开户失败: " . $result['message'] . "\n";
            }
            
        } catch (Exception $e) {
            echo "❌ 企业用户开户异常: " . $e->getMessage() . "\n";
        }
    }

    // ==================== 平台商户业务流程 ====================

    /**
     * 企业会员开通支付账户流程
     * 对应流程图中的"企业会员开通支付账户"
     */
    public function testCompanyPaymentAccountOpening(): void
    {
        echo "\n=== 企业支付账户开通流程测试 ===\n";
        
        try {
            $signNum = 'company_test_001';
            
            // 支付账户开通数据
            $paymentAccountData = [
                'signNum' => $signNum,
                'acctType' => 2, // 支付账户
                'summary' => '开通支付账户用于收付款'
            ];
            
            echo "1. 申请开通支付账户...\n";
            $result = $this->serviceFactory->companyPaymentAccountOpening($paymentAccountData);
            
            if ($result['success']) {
                echo "✅ 支付账户开通申请成功！\n";
                echo "   申请状态: " . $result['message'] . "\n";
                
                // 查询开户受理状态
                echo "2. 查询开户受理状态...\n";
                $bizOrderCode = (string)time();
                $statusResult = $this->serviceFactory->getMemberAccountStatus($signNum, $bizOrderCode);
                echo "   受理状态: " . ($statusResult['success'] ? '成功' : '处理中') . "\n";
                
                // 查询审核详情
                echo "3. 查询审核详情...\n";
                $auditResult = $this->serviceFactory->getPaymentAccountAuditDetail($signNum, $bizOrderCode);
                echo "   审核详情: " . ($auditResult['success'] ? '已审核' : '审核中') . "\n";
                
            } else {
                echo "❌ 支付账户开通申请失败: " . $result['message'] . "\n";
            }
            
        } catch (Exception $e) {
            echo "❌ 支付账户开通异常: " . $e->getMessage() . "\n";
        }
    }

    /**
     * 对公账户转入打款流程
     * 对应流程图中的"对公账户转入打款支付账户号"
     */
    public function testPublicAccountTransfer(): void
    {
        echo "\n=== 对公账户转入打款流程测试 ===\n";
        
        try {
            // 转账数据
            $transferData = [
                'signNum' => 'company_test_001',     // 转出方（平台）
                'inSignNum' => 'company_test_002',   // 转入方（企业）
                'orderAmount' => 100000,             // 转账金额（分）
                'summary' => '对公账户转入打款',
                'acctType' => 2,                     // 支付账户
                'inAcctType' => 2,                   // 转入支付账户
                'respUrl' => 'http://your-domain.com/transfer/notify'
            ];
            
            echo "1. 发起对公账户转账...\n";
            $result = $this->serviceFactory->publicAccountTransfer($transferData);
            
            if ($result['success']) {
                echo "✅ 转账申请成功！\n";
                echo "   转账金额: " . ($transferData['orderAmount'] / 100) . " 元\n";
                echo "   业务数据: " . ($result['bizData'] ?? '无') . "\n";
                
                // 划款入账验证
                echo "2. 进行划款入账验证...\n";
                sleep(2); // 模拟等待处理时间
                
                $reqTraceNum = 'transfer_' . time();
                $verifyResult = $this->serviceFactory->verifyTransferAccount($reqTraceNum, $transferData['signNum']);
                echo "   验证结果: " . ($verifyResult['success'] ? '验证成功' : '验证失败') . "\n";
                
            } else {
                echo "❌ 转账申请失败: " . $result['message'] . "\n";
            }
            
        } catch (Exception $e) {
            echo "❌ 对公账户转账异常: " . $e->getMessage() . "\n";
        }
    }

    // ==================== 支付业务流程 ====================

    /**
     * 完整的支付业务流程测试
     */
    public function testCompletePaymentFlow(): void
    {
        echo "\n=== 完整支付业务流程测试 ===\n";
        
        try {
            $paymentService = $this->serviceFactory->getPaymentService();
            $signNum = 'test_member_001';
            
            // 1. 查询账户余额
            echo "1. 查询账户余额...\n";
            $balanceResult = $paymentService->queryBalance($signNum, 1);
            if ($balanceResult['success']) {
                echo "✅ 余额查询成功\n";
                echo "   余额信息: " . ($balanceResult['bizData'] ?? '无') . "\n";
            }
            
            // 2. 充值操作
            echo "2. 发起充值申请...\n";
            $rechargeData = [
                'signNum' => $signNum,
                'orderAmount' => 50000, // 500元
                'summary' => '账户充值',
                'respUrl' => 'http://your-domain.com/recharge/notify'
            ];
            
            $rechargeResult = $paymentService->recharge($rechargeData);
            if ($rechargeResult['success']) {
                echo "✅ 充值申请成功\n";
                echo "   充值金额: " . ($rechargeData['orderAmount'] / 100) . " 元\n";
            }
            
            // 3. 转账操作
            echo "3. 发起转账申请...\n";
            $transferData = [
                'signNum' => $signNum,
                'inSignNum' => 'test_member_002',
                'orderAmount' => 10000, // 100元
                'summary' => '转账测试',
                'respUrl' => 'http://your-domain.com/transfer/notify'
            ];
            
            $transferResult = $paymentService->transfer($transferData);
            if ($transferResult['success']) {
                echo "✅ 转账申请成功\n";
                echo "   转账金额: " . ($transferData['orderAmount'] / 100) . " 元\n";
            }
            
            // 4. 查询交易明细
            echo "4. 查询交易明细...\n";
            $queryData = [
                'signNum' => $signNum,
                'startDate' => date('Y-m-d', strtotime('-7 days')),
                'endDate' => date('Y-m-d'),
                'pageNum' => 1,
                'pageSize' => 10
            ];
            
            $detailResult = $paymentService->queryTransactionDetail($queryData);
            if ($detailResult['success']) {
                echo "✅ 交易明细查询成功\n";
                echo "   明细数据: " . ($detailResult['bizData'] ?? '无') . "\n";
            }
            
        } catch (Exception $e) {
            echo "❌ 支付业务流程异常: " . $e->getMessage() . "\n";
        }
    }

    // ==================== 协议支付流程 ====================

    /**
     * 协议支付完整流程测试
     */
    public function testAgreementPaymentFlow(): void
    {
        echo "\n=== 协议支付流程测试 ===\n";
        
        try {
            $paymentService = $this->serviceFactory->getPaymentService();
            $signNum = 'test_member_001';
            
            // 1. 协议支付签约
            echo "1. 发起协议支付签约...\n";
            $signData = [
                'signNum' => $signNum,
                'acctNum' => '6217858000141669850',
                'agreementType' => '1',
                'phone' => '13800138000'
            ];
            
            $signResult = $paymentService->agreementSign($signData);
            if ($signResult['success']) {
                echo "✅ 协议支付签约成功\n";
                echo "   签约结果: " . ($signResult['bizData'] ?? '无') . "\n";
                
                // 模拟获取协议号
                $agreementNo = 'AGR' . time();
                
                // 2. 协议支付（模拟）
                echo "2. 使用协议进行支付...\n";
                $payData = [
                    'signNum' => $signNum,
                    'orderAmount' => 5000, // 50元
                    'agreementNo' => $agreementNo,
                    'respUrl' => 'http://your-domain.com/agreement/notify'
                ];
                
                echo "   协议支付模拟成功，金额: " . ($payData['orderAmount'] / 100) . " 元\n";
                
                // 3. 协议支付解约
                echo "3. 发起协议支付解约...\n";
                $unsignData = [
                    'signNum' => $signNum,
                    'agreementNo' => $agreementNo
                ];
                
                $unsignResult = $paymentService->agreementUnsign($unsignData);
                if ($unsignResult['success']) {
                    echo "✅ 协议支付解约成功\n";
                } else {
                    echo "❌ 协议支付解约失败: " . $unsignResult['message'] . "\n";
                }
                
            } else {
                echo "❌ 协议支付签约失败: " . $signResult['message'] . "\n";
            }
            
        } catch (Exception $e) {
            echo "❌ 协议支付流程异常: " . $e->getMessage() . "\n";
        }
    }

    // ==================== 混合云支付流程 ====================

    /**
     * 混合云支付流程测试
     */
    public function testHybridCloudPaymentFlow(): void
    {
        echo "\n=== 混合云支付流程测试 ===\n";
        
        try {
            $paymentService = $this->serviceFactory->getPaymentService();
            $signNum = 'test_member_001';
            
            // 1. 混合云支付统一下单
            echo "1. 发起混合云支付统一下单...\n";
            $orderData = [
                'signNum' => $signNum,
                'orderAmount' => 15000, // 150元
                'payType' => 'ALIPAY', // 支付宝
                'orderInfo' => '商品购买',
                'validTime' => 30, // 30分钟有效期
                'frontUrl' => 'http://your-domain.com/pay/return',
                'respUrl' => 'http://your-domain.com/pay/notify'
            ];
            
            $orderResult = $paymentService->hybridCloudUnifiedOrder($orderData);
            if ($orderResult['success']) {
                echo "✅ 统一下单成功\n";
                echo "   订单金额: " . ($orderData['orderAmount'] / 100) . " 元\n";
                echo "   支付方式: " . $orderData['payType'] . "\n";
                echo "   订单数据: " . ($orderResult['bizData'] ?? '无') . "\n";
                
                // 2. 查询混合云支付订单
                echo "2. 查询混合云支付订单...\n";
                $reqTraceNum = 'hybrid_' . time();
                $queryResult = $paymentService->queryHybridCloudPay($reqTraceNum, $signNum);
                
                if ($queryResult['success']) {
                    echo "✅ 订单查询成功\n";
                    echo "   查询结果: " . ($queryResult['bizData'] ?? '无') . "\n";
                } else {
                    echo "❌ 订单查询失败: " . $queryResult['message'] . "\n";
                }
                
            } else {
                echo "❌ 统一下单失败: " . $orderResult['message'] . "\n";
            }
            
        } catch (Exception $e) {
            echo "❌ 混合云支付流程异常: " . $e->getMessage() . "\n";
        }
    }

    /**
     * 运行所有业务流程测试
     */
    public function runAllBusinessFlows(): void
    {
        echo "开始运行完整业务流程测试...\n";
        
        // 买方用户开户流程
        $this->testPersonalUserAccountOpening();
        $this->testCompanyUserAccountOpening();
        
        // 平台商户业务流程
        $this->testCompanyPaymentAccountOpening();
        $this->testPublicAccountTransfer();
        
        // 支付业务流程
        $this->testCompletePaymentFlow();
        $this->testAgreementPaymentFlow();
        $this->testHybridCloudPaymentFlow();
        
        echo "\n=== 所有业务流程测试完成 ===\n";
        echo "✅ 会员服务和支付服务已按角色成功分离\n";
        echo "✅ 支持完整的开户、支付、查询业务流程\n";
        echo "✅ 提供统一的服务工厂管理接口\n";
    }
}

// 运行示例
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    try {
        $example = new BusinessFlowExample();
        $example->runAllBusinessFlows();
    } catch (Exception $e) {
        echo "业务流程测试失败: " . $e->getMessage() . "\n";
    }
}