<?php
/**
 * 通联支付KA客户版简单使用示例
 * 不需要PHPUnit框架，直接使用
 */

require_once 'vendor/autoload.php';

use AllinpayNew\AllinpayClient;
use AllinpayNew\Config\AppConfig;

// 1. 创建客户端（默认测试环境）
$client = new AllinpayClient(AppConfig::ENV_TEST);

// 2. 获取配置
$config = $client->getConfig();

// 3. 显示配置信息
echo "=== 通联支付KA客户版配置信息 ===\n";
echo "环境: " . $config->getEnvironment() . "\n";
echo "是否测试环境: " . ($config->isTest() ? '是' : '否') . "\n";
echo "API基础URL: " . $config->getBaseUrl() . "\n";
echo "交易接口URL: " . $config->getTransactionUrl() . "\n";
echo "会员接口URL: " . $config->getMemberUrl() . "\n";
echo "查询接口URL: " . $config->getQueryUrl() . "\n";
echo "签名类型: " . $config->get('signType') . "\n";
echo "版本: " . $config->get('version') . "\n";

// 4. 获取各种服务
echo "\n=== 可用服务 ===\n";
echo "用户服务: " . get_class($client->getUserService()) . "\n";
echo "商家服务: " . get_class($client->getMerchantService()) . "\n";
echo "订单服务: " . get_class($client->getOrderService()) . "\n";
echo "查询服务: " . get_class($client->getQueryService()) . "\n";
echo "文件服务: " . get_class($client->getFileService()) . "\n";

// 5. 简单的RPC调用示例
echo "\n=== 使用示例 ===\n";
echo "客户端初始化成功！\n";
echo "现在可以通过以下方式使用：\n";
echo "- client->getUserService()->createPersonalMemberApply()\n";
echo "- client->getOrderService()->createConsumeOrder()\n";
echo "- client->getQueryService()->queryOrder()\n";
echo "\n这是一个简单的Composer库，无需PHPUnit框架！\n";
