<?php

require_once __DIR__ . '/../vendor/autoload.php';

use AllinpayYunshangtong\AllinpayClient;
use AllinpayYunshangtong\Config\AppConfig;
use AllinpayYunshangtong\Config\LogConfig;
use AllinpayYunshangtong\Utils\Logger;
use AllinpayYunshangtong\Utils\Validator;

/**
 * 高级使用示例
 * 展示更多高级功能和最佳实践
 */

// 创建日志配置
$logConfig = new LogConfig([
    'level' => LogConfig::LEVEL_DEBUG,
    'format' => LogConfig::FORMAT_JSON,
    'includeTrace' => true
]);

// 创建日志记录器
$logger = new Logger($logConfig, 'advanced_example');

echo "=== 通联支付高级使用示例 ===\n\n";

// 创建客户端实例
$client = new AllinpayClient(AppConfig::ENV_TEST);
$logger->info('客户端创建成功', ['environment' => $client->getEnvironment()]);

// 示例1: 企业会员开户（带验证）
echo "=== 示例1: 企业会员开户（带验证） ===\n";
try {
    $memberInfo = [
        'bizUserId' => 'enterprise_' . time(),
        'memberType' => '2', // 企业会员
        'source' => '1', // APP来源
        'extendParam' => json_encode([
            'companyName' => '测试企业有限公司',
            'businessLicense' => '91110000123456789X',
            'legalPerson' => '张三',
            'legalPersonIdCard' => '110101199001011234',
            'contactPhone' => '13800138000',
            'contactEmail' => 'test@example.com',
            'address' => '北京市朝阳区测试街道123号'
        ], JSON_UNESCAPED_UNICODE)
    ];
    
    // 数据验证
    $errors = Validator::validateMemberInfo($memberInfo);
    if (!empty($errors)) {
        echo "数据验证失败:\n";
        foreach ($errors as $error) {
            echo "  - {$error}\n";
        }
    } else {
        echo "数据验证通过\n";
        
        $response = $client->getUserService()->createEnterpriseMember($memberInfo);
        echo "响应结果: " . $response->toJson() . "\n";
        
        $logger->info('企业会员开户成功', [
            'bizUserId' => $memberInfo['bizUserId'],
            'response' => $response->toArray()
        ]);
    }
    
} catch (Exception $e) {
    echo "企业会员开户失败: " . $e->getMessage() . "\n";
    $logger->error('企业会员开户失败', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}

echo "\n";

// 示例2: 批量会员开户
echo "=== 示例2: 批量会员开户 ===\n";
try {
    $batchMembers = [];
    for ($i = 1; $i <= 3; $i++) {
        $batchMembers[] = [
            'bizUserId' => 'batch_user_' . time() . '_' . $i,
            'memberType' => '1', // 个人会员
            'source' => '1',
            'extendParam' => json_encode([
                'realName' => "测试用户{$i}",
                'idCard' => '11010119900101123' . $i,
                'phone' => '1380013800' . $i
            ], JSON_UNESCAPED_UNICODE)
        ];
    }
    
    $successCount = 0;
    $failCount = 0;
    
    foreach ($batchMembers as $memberInfo) {
        try {
            $response = $client->getUserService()->createPersonalMemberApply($memberInfo);
            if ($response->isSuccess()) {
                $successCount++;
                echo "用户 {$memberInfo['bizUserId']} 创建成功\n";
            } else {
                $failCount++;
                echo "用户 {$memberInfo['bizUserId']} 创建失败: {$response->getErrorMessage()}\n";
            }
        } catch (Exception $e) {
            $failCount++;
            echo "用户 {$memberInfo['bizUserId']} 创建异常: {$e->getMessage()}\n";
        }
    }
    
    echo "批量创建完成: 成功 {$successCount} 个，失败 {$failCount} 个\n";
    
    $logger->info('批量会员开户完成', [
        'total' => count($batchMembers),
        'success' => $successCount,
        'fail' => $failCount
    ]);
    
} catch (Exception $e) {
    echo "批量会员开户失败: " . $e->getMessage() . "\n";
}

echo "\n";

// 示例3: 复杂订单流程
echo "=== 示例3: 复杂订单流程 ===\n";
try {
    $bizUserId = 'order_user_' . time();
    
    // 1. 创建个人会员
    $memberInfo = [
        'bizUserId' => $bizUserId,
        'memberType' => '1',
        'source' => '1',
        'extendParam' => json_encode([
            'realName' => '订单测试用户',
            'idCard' => '110101199001011234',
            'phone' => '13800138000'
        ], JSON_UNESCAPED_UNICODE)
    ];
    
    $memberResponse = $client->getUserService()->createPersonalMemberApply($memberInfo);
    if (!$memberResponse->isSuccess()) {
        throw new Exception("会员创建失败: " . $memberResponse->getErrorMessage());
    }
    
    echo "会员创建成功: {$bizUserId}\n";
    
    // 2. 创建消费订单
    $orderInfo = [
        'bizOrderNo' => 'ORDER_' . time(),
        'bizUserId' => $bizUserId,
        'amount' => 10000, // 100元
        'fee' => 100, // 1元手续费
        'payMethod' => [
            'payMethodType' => 'BALANCE'
        ],
        'goodsInfo' => [
            'goodsName' => '测试商品',
            'goodsPrice' => 10000,
            'goodsQuantity' => 1
        ]
    ];
    
    // 订单数据验证
    $orderErrors = Validator::validateOrderInfo($orderInfo);
    if (!empty($orderErrors)) {
        echo "订单数据验证失败:\n";
        foreach ($orderErrors as $error) {
            echo "  - {$error}\n";
        }
    } else {
        echo "订单数据验证通过\n";
        
        $orderResponse = $client->getOrderService()->createConsumptionOrder($orderInfo);
        echo "订单创建结果: " . $orderResponse->toJson() . "\n";
        
        if ($orderResponse->isSuccess()) {
            $orderId = $orderInfo['bizOrderNo'];
            
            // 3. 查询订单状态
            sleep(1); // 等待一下再查询
            $statusResponse = $client->getQueryService()->queryOrderStatus($orderId);
            echo "订单状态查询结果: " . $statusResponse->toJson() . "\n";
            
            // 4. 查询账户余额
            $balanceResponse = $client->getQueryService()->queryAccountBalance($bizUserId);
            echo "账户余额查询结果: " . $balanceResponse->toJson() . "\n";
        }
    }
    
    $logger->info('复杂订单流程执行完成', [
        'bizUserId' => $bizUserId,
        'orderNo' => $orderInfo['bizOrderNo'] ?? null
    ]);
    
} catch (Exception $e) {
    echo "复杂订单流程失败: " . $e->getMessage() . "\n";
    $logger->error('复杂订单流程失败', [
        'error' => $e->getMessage(),
        'trace' => $e->getTraceAsString()
    ]);
}

echo "\n";

// 示例4: 文件上传和下载
echo "=== 示例4: 文件上传和下载 ===\n";
try {
    // 创建测试文件
    $testFile = __DIR__ . '/test_document.txt';
    $fileContent = "这是一个测试文档内容\n创建时间: " . date('Y-m-d H:i:s') . "\n用于测试文件上传功能";
    file_put_contents($testFile, $fileContent);
    
    echo "测试文件创建成功: {$testFile}\n";
    
    // 上传文件
    $uploadResponse = $client->getFileService()->uploadFile(
        $testFile,
        'TEST_DOCUMENT',
        'file_user_' . time()
    );
    
    echo "文件上传结果: " . $uploadResponse->toJson() . "\n";
    
    if ($uploadResponse->isSuccess()) {
        $fileId = $uploadResponse->getData()['fileId'] ?? null;
        if ($fileId) {
            // 下载文件
            $downloadPath = __DIR__ . '/downloaded_' . basename($testFile);
            $downloadResponse = $client->getFileService()->downloadFile($fileId, $downloadPath);
            
            echo "文件下载结果: " . $downloadResponse->toJson() . "\n";
            
            if ($downloadResponse->isSuccess() && file_exists($downloadPath)) {
                echo "文件下载成功: {$downloadPath}\n";
                echo "文件内容: " . file_get_contents($downloadPath) . "\n";
                
                // 清理下载的文件
                unlink($downloadPath);
            }
        }
    }
    
    // 清理测试文件
    unlink($testFile);
    
    $logger->info('文件上传下载测试完成');
    
} catch (Exception $e) {
    echo "文件上传下载测试失败: " . $e->getMessage() . "\n";
}

echo "\n";

// 示例5: 环境切换和配置管理
echo "=== 示例5: 环境切换和配置管理 ===\n";
try {
    echo "当前环境: " . $client->getEnvironment() . "\n";
    echo "当前配置:\n";
    
    $config = $client->getConfig();
    $configInfo = [
        '应用ID' => $config->get('appId'),
        '基础URL' => $config->getBaseUrl(),
        '签名类型' => $config->get('signType'),
        '超时时间' => $config->get('timeout') . '秒'
    ];
    
    foreach ($configInfo as $key => $value) {
        echo "  {$key}: {$value}\n";
    }
    
    // 切换到生产环境
    echo "\n切换到生产环境...\n";
    $client->setEnvironment(AppConfig::ENV_PROD);
    echo "当前环境: " . $client->getEnvironment() . "\n";
    echo "生产环境基础URL: " . $client->getConfig()->getBaseUrl() . "\n";
    
    // 切换回测试环境
    echo "\n切换回测试环境...\n";
    $client->setEnvironment(AppConfig::ENV_TEST);
    echo "当前环境: " . $client->getEnvironment() . "\n";
    
    $logger->info('环境切换测试完成');
    
} catch (Exception $e) {
    echo "环境切换测试失败: " . $e->getMessage() . "\n";
}

echo "\n";

// 示例6: 错误处理和日志记录
echo "=== 示例6: 错误处理和日志记录 ===\n";
try {
    // 故意传入无效数据
    $invalidOrderInfo = [
        'bizOrderNo' => '', // 空的订单号
        'bizUserId' => 'invalid_user_id_with_very_long_name_that_exceeds_the_maximum_length_limit',
        'amount' => -100 // 负数金额
    ];
    
    echo "测试无效数据验证...\n";
    
    // 验证订单信息
    $errors = Validator::validateOrderInfo($invalidOrderInfo);
    if (!empty($errors)) {
        echo "验证发现以下错误:\n";
        foreach ($errors as $error) {
            echo "  - {$error}\n";
        }
    }
    
    // 尝试创建无效订单
    try {
        $response = $client->getOrderService()->createConsumptionOrder($invalidOrderInfo);
        echo "意外成功创建订单: " . $response->toJson() . "\n";
    } catch (Exception $e) {
        echo "预期的错误: " . $e->getMessage() . "\n";
        $logger->warning('预期的验证错误', [
            'error' => $e->getMessage(),
            'invalidData' => $invalidOrderInfo
        ]);
    }
    
} catch (Exception $e) {
    echo "错误处理测试失败: " . $e->getMessage() . "\n";
}

echo "\n=== 高级示例执行完成 ===\n";

// 记录最终日志
$logger->info('高级使用示例执行完成', [
    'timestamp' => date('Y-m-d H:i:s'),
    'memory_usage' => memory_get_usage(true),
    'peak_memory' => memory_get_peak_usage(true)
]);
