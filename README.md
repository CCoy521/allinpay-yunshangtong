# 通联支付KA客户版 (AllinpayNew)

**简单、轻量的PHP Composer库** - 基于通联支付KA客户版接口规范，无需复杂的测试框架，直接通过RPC方式使用。

## 项目特性

- 🚀 **简单易用**: 无需PHPUnit等测试框架，直接使用
- 🛡️ **安全可靠**: 内置签名验证、数据验证、异常处理等安全机制
- 📝 **轻量设计**: 专注于核心功能，避免过度设计
- 🔧 **RPC调用**: 直接通过RPC方式请求，简单直接
- 🌍 **环境支持**: 支持测试和生产环境，可动态切换
- 📊 **数据验证**: 内置完整的数据验证器，确保数据安全
- 🎯 **最佳实践**: 遵循PHP PSR标准，代码结构清晰

## 系统要求

- PHP 7.4 或更高版本
- Composer
- 支持 cURL 扩展
- 支持 OpenSSL 扩展

## 安装

### 1. 通过 Composer 安装

```bash
composer require allinpay/new-sdk
```

### 2. 手动安装

```bash
git clone https://github.com/your-username/allinpay-new-sdk.git
cd allinpay-new-sdk
composer install
```

## 快速开始

### 基本使用

```php
<?php

require_once 'vendor/autoload.php';

use AllinpayNew\AllinpayClient;

// 创建客户端实例（默认测试环境）
$client = new AllinpayClient();

// 创建个人会员
$memberInfo = [
    'bizUserId' => 'user_' . time(),
    'memberType' => '1', // 个人会员
    'source' => '1', // APP来源
    'extendParam' => json_encode([
        'realName' => '张三',
        'idCard' => '110101199001011234',
        'phone' => '13800138000'
    ])
];

try {
    $response = $client->quickCreatePersonalMember($memberInfo);
    if ($response->isSuccess()) {
        echo "会员创建成功！\n";
    } else {
        echo "会员创建失败: " . $response->getErrorMessage() . "\n";
    }
} catch (Exception $e) {
    echo "异常: " . $e->getMessage() . "\n";
}
```

### 创建消费订单

```php
// 创建消费订单
$orderInfo = [
    'bizOrderNo' => 'ORDER_' . time(),
    'bizUserId' => 'user_' . time(),
    'amount' => 10000, // 100元（分）
    'fee' => 100, // 1元手续费
    'payMethod' => [
        'payMethodType' => 'BALANCE' // 余额支付
    ]
];

$response = $client->quickCreateConsumptionOrder($orderInfo);
echo "订单创建结果: " . $response->toJson() . "\n";
```

## 核心功能

### 1. 会员管理

- **个人会员开户**: `Member1020` - 个人会员实名及绑卡（申请）
- **企业会员开户**: `Member1010` - 企业会员实名开户
- **会员信息查询**: `Member1030` - 查询会员信息
- **会员信息修改**: `Member1023` - 企业会员信息修改
- **手机号绑定**: `Member1026/1027` - 会员绑定手机号申请/确认

### 2. 订单处理

- **消费申请**: `Tx3010` - 消费申请
- **担保消费**: `Tx3011` - 担保消费
- **转账申请**: `Tx3012` - 转账申请
- **提现申请**: `Tx3013` - 提现申请
- **充值申请**: `Tx3014` - 充值申请
- **退款申请**: `Tx3015` - 退款申请
- **订单关闭**: `Tx3016` - 订单关闭
- **确认支付**: `Tx3017` - 确认支付

### 3. 查询服务

- **订单查询**: `Tq3001/3002` - 订单状态/详情查询
- **账户查询**: `Tq4001/4002` - 账户余额/明细查询
- **平台资金**: `Tq4003` - 平台资金查询
- **银行账户**: `Tq4004` - 银行账户收支明细查询
- **对账文件**: `Tq4005` - 应用集合对账文件下载
- **电子回单**: `Tq4006` - 电子回单下载

### 4. 文件服务

- **文件上传**: 支持身份证、营业执照、协议文件等
- **文件下载**: 支持各种文件类型的下载
- **批量处理**: 支持批量文件上传和下载

## 配置说明

### 环境配置

```php
// 测试环境
$client = new AllinpayClient(AppConfig::ENV_TEST);

// 生产环境
$client = new AllinpayClient(AppConfig::ENV_PROD);

// 动态切换环境
$client->setEnvironment(AppConfig::ENV_PROD);
```

### 日志配置

```php
use AllinpayNew\Config\LogConfig;
use AllinpayNew\Utils\Logger;

$logConfig = new LogConfig([
    'level' => LogConfig::LEVEL_DEBUG,
    'format' => LogConfig::FORMAT_JSON,
    'includeTrace' => true,
    'maxFiles' => 30,
    'maxSize' => '10MB'
]);

$logger = new Logger($logConfig, 'my_app');
$logger->info('应用启动成功');
```

## 数据验证

### 内置验证器

```php
use AllinpayNew\Utils\Validator;

// 验证会员信息
$errors = Validator::validateMemberInfo($memberInfo);
if (!empty($errors)) {
    foreach ($errors as $error) {
        echo "验证错误: {$error}\n";
    }
}

// 验证订单信息
$errors = Validator::validateOrderInfo($orderInfo);

// 验证并抛出异常
Validator::validateAndThrow($data, $rules, '数据验证');
```

### 自定义验证规则

```php
$rules = [
    'required' => ['field1', 'field2'],
    'length' => [
        'field1' => ['min' => 1, 'max' => 100],
        'field2' => ['exact' => 10]
    ],
    'range' => [
        'field3' => ['min' => 0, 'max' => 1000],
        'field4' => ['in' => ['value1', 'value2']]
    ]
];

Validator::validateAndThrow($data, $rules);
```

## 异常处理

### 异常类型

- **配置异常**: `AllinpayException::configError()`
- **网络异常**: `AllinpayException::networkError()`
- **签名异常**: `AllinpayException::signatureError()`
- **业务异常**: `AllinpayException::businessError()`
- **验证异常**: `AllinpayException::validationError()`
- **系统异常**: `AllinpayException::systemError()`

### 异常处理示例

```php
try {
    $response = $client->getUserService()->createPersonalMemberApply($memberInfo);
} catch (AllinpayException $e) {
    if ($e->isBusinessError()) {
        echo "业务错误: " . $e->getDetailedMessage() . "\n";
        echo "响应代码: " . $e->getResponseCode() . "\n";
    } elseif ($e->isValidationError()) {
        echo "验证错误: " . $e->getMessage() . "\n";
        echo "错误详情: " . $e->toJson() . "\n";
    } else {
        echo "系统错误: " . $e->getMessage() . "\n";
    }
}
```

## 高级功能

### 批量处理

```php
// 批量会员开户
$batchMembers = [/* 会员信息数组 */];
$successCount = 0;
$failCount = 0;

foreach ($batchMembers as $memberInfo) {
    try {
        $response = $client->getUserService()->createPersonalMemberApply($memberInfo);
        if ($response->isSuccess()) {
            $successCount++;
        } else {
            $failCount++;
        }
    } catch (Exception $e) {
        $failCount++;
    }
}

echo "批量处理完成: 成功 {$successCount} 个，失败 {$failCount} 个\n";
```

### 复杂业务流程

```php
// 1. 创建会员
$memberResponse = $client->getUserService()->createPersonalMemberApply($memberInfo);
if (!$memberResponse->isSuccess()) {
    throw new Exception("会员创建失败");
}

// 2. 创建订单
$orderResponse = $client->getOrderService()->createConsumptionOrder($orderInfo);

// 3. 查询订单状态
$statusResponse = $client->getQueryService()->queryOrderStatus($orderInfo['bizOrderNo']);

// 4. 查询账户余额
$balanceResponse = $client->getQueryService()->queryAccountBalance($memberInfo['bizUserId']);
```

## 测试

### 运行测试

```bash
# 运行所有测试
php run_tests.php

# 运行基本示例
php examples/basic_usage.php

# 运行高级示例
php examples/advanced_usage.php
```

### 测试覆盖

- ✅ 客户端创建和配置
- ✅ 服务访问和初始化
- ✅ 环境切换功能
- ✅ 配置值验证
- ✅ API URL验证
- ✅ 数据验证功能
- ✅ 异常处理机制

## 日志系统

### 日志级别

- `DEBUG`: 调试信息
- `INFO`: 一般信息
- `WARNING`: 警告信息
- `ERROR`: 错误信息
- `CRITICAL`: 严重错误

### 日志格式

支持JSON和文本两种格式，可配置是否包含上下文信息和堆栈跟踪。

### 日志轮转

自动日志轮转功能，支持按大小和时间轮转，自动清理旧日志文件。

## 安全特性

- **签名验证**: 支持SM3withSM2和RSA签名算法
- **数据验证**: 完整的数据格式和业务规则验证
- **异常处理**: 统一的异常处理机制，避免敏感信息泄露
- **日志安全**: 日志记录不包含敏感信息，支持日志轮转

## 性能优化

- **连接复用**: HTTP客户端支持连接复用
- **超时控制**: 可配置的请求超时时间
- **重试机制**: 支持网络请求重试
- **内存管理**: 合理的内存使用和释放

## 最佳实践

### 1. 错误处理

```php
// 总是检查响应状态
$response = $client->getUserService()->createPersonalMemberApply($memberInfo);
if (!$response->isSuccess()) {
    // 处理业务错误
    $logger->error('会员创建失败', [
        'bizUserId' => $memberInfo['bizUserId'],
        'error' => $response->getErrorMessage()
    ]);
    return false;
}
```

### 2. 数据验证

```php
// 在发送请求前验证数据
$errors = Validator::validateMemberInfo($memberInfo);
if (!empty($errors)) {
    // 处理验证错误
    return false;
}
```

### 3. 日志记录

```php
// 记录关键操作
$logger->info('会员创建请求', [
    'bizUserId' => $memberInfo['bizUserId'],
    'memberType' => $memberInfo['memberType']
]);
```

### 4. 异常处理

```php
try {
    $response = $client->getOrderService()->createConsumptionOrder($orderInfo);
} catch (AllinpayException $e) {
    $logger->error('订单创建异常', [
        'orderNo' => $orderInfo['bizOrderNo'],
        'exception' => $e->toArray()
    ]);
    // 根据异常类型进行相应处理
}
```

## 常见问题

### Q: 如何处理网络超时？

A: 可以通过配置调整超时时间，并实现重试机制：

```php
$config = $client->getConfig();
$config->set('timeout', 60); // 设置60秒超时
```

### Q: 如何调试API请求？

A: 启用调试日志并查看日志文件：

```php
$logConfig = new LogConfig(['level' => LogConfig::LEVEL_DEBUG]);
$logger = new Logger($logConfig, 'api_debug');
```

### Q: 如何验证签名？

A: 签名验证是自动进行的，也可以通过配置控制：

```php
// 在SignatureUtils中实现具体的签名验证逻辑
```

## 贡献指南

欢迎提交Issue和Pull Request来改进这个项目。

### 开发环境设置

1. Fork项目
2. 创建特性分支
3. 提交更改
4. 推送到分支
5. 创建Pull Request

### 代码规范

- 遵循PSR-12编码标准
- 添加适当的注释和文档
- 编写测试用例
- 确保代码覆盖率

## 许可证

本项目采用MIT许可证，详见LICENSE文件。

## 联系方式

- 项目主页: [GitHub Repository]
- 问题反馈: [Issues]
- 邮箱: [your-email@example.com]

## 更新日志

### v1.0.0 (2024-01-01)
- 初始版本发布
- 支持基本的会员管理、订单处理、查询服务
- 完整的日志系统和异常处理
- 数据验证和配置管理

---

**注意**: 本项目仅用于学习和开发目的，在生产环境中使用前请确保充分测试和验证。
