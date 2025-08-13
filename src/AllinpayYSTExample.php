<?php
namespace src;

require_once __DIR__ . '/../vendor/autoload.php';
require_once 'test.php';

use config\DemoConfig;
use Exception;
use src\AllinpayYST;

/**
 * AllinpayYST SDK 使用示例
 * 
 * 展示如何使用云商通SDK进行各种业务操作
 * 
 * @author CodeBuddy
 * @version 1.0
 * @date 2025/01/13
 */
class AllinpayYSTExample
{
    private AllinpayYST $yst;

    public function __construct()
    {
        try {
            // 初始化SDK，可以传入自定义配置
            $config = new DemoConfig();
            $this->yst = new AllinpayYST($config);
            
            // 设置通知地址
            $this->yst->setNotifyUrl("http://your-domain.com/notify");
            
            echo "=== AllinpayYST SDK 初始化成功 ===\n";
            
        } catch (Exception $e) {
            echo "SDK初始化失败: " . $e->getMessage() . "\n";
            exit(1);
        }
    }

    /**
     * 个人会员实名认证示例
     */
    public function testPersonalMemberAuth(): void
    {
        echo "\n=== 个人会员实名认证测试 ===\n";
        
        try {
            $memberData = [
                'signNum' => 'test_member_' . time(),
                'name' => '张三',
                'cerNum' => '110101199001011234',  // 身份证号
                'acctNum' => '6217858000141669850', // 银行卡号
                'phone' => '13800138000',
                'memberRole' => '分销方',
                'bindType' => '8' // 银行卡四要素
            ];
            
            $result = $this->yst->memberPersonalAuth($memberData);
            
            if ($result['success']) {
                echo "个人会员认证成功！\n";
                echo "业务数据: " . $result['bizData'] . "\n";
            } else {
                echo "个人会员认证失败: " . $result['message'] . "\n";
            }
            
        } catch (Exception $e) {
            echo "个人会员认证异常: " . $e->getMessage() . "\n";
        }
    }

    /**
     * 企业会员注册示例
     */
    public function testCompanyMemberRegister(): void
    {
        echo "\n=== 企业会员注册测试 ===\n";
        
        try {
            $companyData = [
                'signNum' => 'company_' . time(),
                'companyName' => '测试科技有限公司',
                'licenseNo' => '91110000123456789X',
                'legalName' => '李四',
                'legalCerNum' => '110101198001011234',
                'memberRole' => '分销方'
            ];
            
            $result = $this->yst->memberCompanyRegister($companyData);
            
            if ($result['success']) {
                echo "企业会员注册成功！\n";
                echo "业务数据: " . $result['bizData'] . "\n";
            } else {
                echo "企业会员注册失败: " . $result['message'] . "\n";
            }
            
        } catch (Exception $e) {
            echo "企业会员注册异常: " . $e->getMessage() . "\n";
        }
    }

    /**
     * 转账交易示例
     */
    public function testTransfer(): void
    {
        echo "\n=== 转账交易测试 ===\n";
        
        try {
            $transferData = [
                'signNum' => 'test_member_001',      // 转出方会员编号
                'inSignNum' => 'test_member_002',    // 转入方会员编号
                'orderAmount' => 10000,              // 转账金额（分）
                'summary' => '测试转账',
                'respUrl' => 'http://your-domain.com/transfer/notify'
            ];
            
            $result = $this->yst->transfer($transferData);
            
            if ($result['success']) {
                echo "转账申请成功！\n";
                echo "业务数据: " . $result['bizData'] . "\n";
            } else {
                echo "转账申请失败: " . $result['message'] . "\n";
            }
            
        } catch (Exception $e) {
            echo "转账申请异常: " . $e->getMessage() . "\n";
        }
    }

    /**
     * 充值示例
     */
    public function testRecharge(): void
    {
        echo "\n=== 充值测试 ===\n";
        
        try {
            $rechargeData = [
                'signNum' => 'test_member_001',
                'orderAmount' => 50000, // 充值金额（分）
                'respUrl' => 'http://your-domain.com/recharge/notify'
            ];
            
            $result = $this->yst->recharge($rechargeData);
            
            if ($result['success']) {
                echo "充值申请成功！\n";
                echo "业务数据: " . $result['bizData'] . "\n";
            } else {
                echo "充值申请失败: " . $result['message'] . "\n";
            }
            
        } catch (Exception $e) {
            echo "充值申请异常: " . $e->getMessage() . "\n";
        }
    }

    /**
     * 提现示例
     */
    public function testWithdraw(): void
    {
        echo "\n=== 提现测试 ===\n";
        
        try {
            $withdrawData = [
                'signNum' => 'test_member_001',
                'orderAmount' => 20000, // 提现金额（分）
                'respUrl' => 'http://your-domain.com/withdraw/notify'
            ];
            
            $result = $this->yst->withdraw($withdrawData);
            
            if ($result['success']) {
                echo "提现申请成功！\n";
                echo "业务数据: " . $result['bizData'] . "\n";
            } else {
                echo "提现申请失败: " . $result['message'] . "\n";
            }
            
        } catch (Exception $e) {
            echo "提现申请异常: " . $e->getMessage() . "\n";
        }
    }

    /**
     * 交易查询示例
     */
    public function testQueryTransaction(): void
    {
        echo "\n=== 交易查询测试 ===\n";
        
        try {
            $reqTraceNum = $this->yst->generateTraceNum() . "transfer";
            
            $result = $this->yst->queryTransaction($reqTraceNum, 'test_member_001');
            
            if ($result['success']) {
                echo "交易查询成功！\n";
                echo "业务数据: " . $result['bizData'] . "\n";
            } else {
                echo "交易查询失败: " . $result['message'] . "\n";
            }
            
        } catch (Exception $e) {
            echo "交易查询异常: " . $e->getMessage() . "\n";
        }
    }

    /**
     * 余额查询示例
     */
    public function testQueryBalance(): void
    {
        echo "\n=== 余额查询测试 ===\n";
        
        try {
            $result = $this->yst->queryBalance('test_member_001', 1);
            
            if ($result['success']) {
                echo "余额查询成功！\n";
                echo "业务数据: " . $result['bizData'] . "\n";
            } else {
                echo "余额查询失败: " . $result['message'] . "\n";
            }
            
        } catch (Exception $e) {
            echo "余额查询异常: " . $e->getMessage() . "\n";
        }
    }

    /**
     * 文件上传示例
     */
    public function testFileUpload(): void
    {
        echo "\n=== 文件上传测试 ===\n";
        
        try {
            // 创建测试文件
            $testFile = __DIR__ . '/test_upload.txt';
            file_put_contents($testFile, "这是一个测试文件内容\n测试时间: " . date('Y-m-d H:i:s'));
            
            $result = $this->yst->uploadFile($testFile, '0');
            
            if ($result['code'] === '0000') {
                echo "文件上传成功！\n";
                echo "响应结果: " . json_encode($result, JSON_UNESCAPED_UNICODE) . "\n";
            } else {
                echo "文件上传失败: " . ($result['msg'] ?? '未知错误') . "\n";
            }
            
            // 清理测试文件
            if (file_exists($testFile)) {
                unlink($testFile);
            }
            
        } catch (Exception $e) {
            echo "文件上传异常: " . $e->getMessage() . "\n";
        }
    }

    /**
     * 工具方法示例
     */
    public function testUtilityMethods(): void
    {
        echo "\n=== 工具方法测试 ===\n";
        
        try {
            // 生成交易流水号
            $traceNum = $this->yst->generateTraceNum();
            echo "生成的交易流水号: " . $traceNum . "\n";
            
            // 加密敏感数据
            $originalData = "6217858000141669850";
            $encryptedData = $this->yst->encryptSensitiveData($originalData);
            echo "原始数据: " . $originalData . "\n";
            echo "加密后数据: " . $encryptedData . "\n";
            
            // 解密数据
            $decryptedData = $this->yst->decryptSensitiveData($encryptedData);
            echo "解密后数据: " . $decryptedData . "\n";
            
            // 验证加解密是否正确
            if ($originalData === $decryptedData) {
                echo "加解密验证成功！\n";
            } else {
                echo "加解密验证失败！\n";
            }
            
        } catch (Exception $e) {
            echo "工具方法测试异常: " . $e->getMessage() . "\n";
        }
    }

    /**
     * 链式调用示例
     */
    public function testChainedCalls(): void
    {
        echo "\n=== 链式调用测试 ===\n";
        
        try {
            // 演示链式调用设置通知地址
            $config = $this->yst
                ->setNotifyUrl("http://new-domain.com/notify")
                ->getConfig();
            
            echo "当前通知地址: " . $config->getNotifyUrl() . "\n";
            
        } catch (Exception $e) {
            echo "链式调用测试异常: " . $e->getMessage() . "\n";
        }
    }

    /**
     * 运行所有测试
     */
    public function runAllTests(): void
    {
        echo "开始运行 AllinpayYST SDK 完整测试...\n";
        
        // 会员管理测试
        $this->testPersonalMemberAuth();
        $this->testCompanyMemberRegister();
        
        // 交易处理测试
        $this->testTransfer();
        $this->testRecharge();
        $this->testWithdraw();
        
        // 查询服务测试
        $this->testQueryTransaction();
        $this->testQueryBalance();
        
        // 文件服务测试
        $this->testFileUpload();
        
        // 工具方法测试
        $this->testUtilityMethods();
        
        // 链式调用测试
        $this->testChainedCalls();
        
        echo "\n=== 所有测试完成 ===\n";
    }
}

// 运行示例
if (basename(__FILE__) === basename($_SERVER['SCRIPT_NAME'])) {
    try {
        $example = new AllinpayYSTExample();
        $example->runAllTests();
    } catch (Exception $e) {
        echo "示例运行失败: " . $e->getMessage() . "\n";
    }
}