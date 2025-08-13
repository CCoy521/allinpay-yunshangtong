<?php
require_once __DIR__ . '/../vendor/autoload.php';
require_once __DIR__ . '/config/DemoConfig.php';
require_once __DIR__ . '/util/DemoSM2Utils.php';
require_once __DIR__ . '/util/DemoSM4Utils.php';
require_once __DIR__ . '/util/TxUtils.php';
require_once __DIR__ . '/vo/BizParameter.php';
require_once __DIR__ . '/vo/TxRequest.php';
require_once __DIR__ . '/vo/TxResponse.php';
require_once __DIR__ . '/tx/TxClient.php';
require_once __DIR__ . '/AllinpayYST.php';

use config\DemoConfig;
use src\AllinpayYST;

/**
 * 通联支付云商通SDK测试Demo
 * 
 * 重要!!!  记得要开IP白名单
 * 
 * 包含会员开户和企业开户的示例代码
 * 
 * @author CodeBuddy
 * @version 1.0
 * @date 2025/08/13
 */

// 设置错误显示
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

try {
    echo "========== 通联支付云商通SDK测试 ==========\n\n";
    
    // 初始化SDK
    $config = new DemoConfig();
    $allinpay = new AllinpayYST($config);
    
    echo "SDK初始化成功\n\n";
    
    // ========== 个人会员开户示例 ==========
    echo "========== 个人会员开户示例 ==========\n";
    
    // 生成会员唯一标识
    $personalSignNum = 'P' . date('YmdHis') . rand(1000, 9999);
    
    // 个人会员数据
    $personalMemberData = [
        'signNum' => $personalSignNum,                // 会员唯一标识
        'name' => '张三',                             // 姓名
        'cerNum' => '110101199001011234',            // 身份证号
        'acctNum' => '6222021234567890123',          // 银行卡号
        'phone' => '13800138000',                    // 手机号
        'memberRole' => '分销方',                     // 会员角色
        'cerType' => '1',                            // 证件类型：1-身份证
        'bindType' => '8'                            // 绑定类型：8-快捷支付
    ];
    
    echo "开始个人会员实名认证及绑卡...\n";
    echo "会员标识: {$personalSignNum}\n";
    
    try {
        // 调用个人会员实名认证及绑卡接口
        $personalResult = $allinpay->memberPersonalAuth($personalMemberData);
        
        // 输出结果
        echo "个人会员开户结果: " . ($personalResult['success'] ? '成功' : '失败') . "\n";
        echo "返回码: {$personalResult['code']}\n";
        echo "返回信息: {$personalResult['message']}\n";
        
        if ($personalResult['success']) {
            echo "业务数据: " . json_encode($personalResult['bizData'], JSON_UNESCAPED_UNICODE) . "\n";
        }
    } catch (Exception $e) {
        echo "个人会员开户异常: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
    
    // ========== 企业会员开户示例 ==========
    echo "========== 企业会员开户示例 ==========\n";
    
    // 生成会员唯一标识
    $companySignNum = 'C' . date('YmdHis') . rand(1000, 9999);
    
    // 企业会员数据
    $companyData = [
        'signNum' => $companySignNum,                // 会员唯一标识
        'companyName' => '北京科技有限公司',          // 企业名称
        'licenseNo' => '91110105MA01C2CC33',         // 营业执照号
        'legalName' => '李四',                       // 法人姓名
        'legalCerType' => '1',                       // 法人证件类型：1-身份证
        'legalCerNum' => '110101199001011235',       // 法人证件号
        'memberRole' => '分销方'                      // 会员角色
    ];
    
    echo "开始企业会员注册...\n";
    echo "会员标识: {$companySignNum}\n";
    
    try {
        // 调用企业会员注册接口
        $companyResult = $allinpay->memberCompanyRegister($companyData);
        
        // 输出结果
        echo "企业会员开户结果: " . ($companyResult['success'] ? '成功' : '失败') . "\n";
        echo "返回码: {$companyResult['code']}\n";
        echo "返回信息: {$companyResult['message']}\n";
        
        if ($companyResult['success']) {
            echo "业务数据: " . json_encode($companyResult['bizData'], JSON_UNESCAPED_UNICODE) . "\n";
        }
    } catch (Exception $e) {
        echo "企业会员开户异常: " . $e->getMessage() . "\n";
    }
    
    echo "\n";
    echo "========== 测试完成 ==========\n";
    
} catch (Exception $e) {
    echo "程序异常: " . $e->getMessage() . "\n";
}