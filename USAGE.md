# 通联支付KA客户版 - 简单使用说明

## 项目特点
- ✅ **无需PHPUnit框架** - 这是一个简单的Composer库
- ✅ **直接RPC调用** - 通过HTTP请求直接调用通联支付API
- ✅ **轻量设计** - 专注于核心功能，避免过度设计

## 快速开始

### 1. 安装依赖
```bash
composer install
```

### 2. 基本使用
```php
<?php
require_once 'vendor/autoload.php';

use AllinpayYunshangtong\AllinpayClient;
use AllinpayYunshangtong\Config\AppConfig;

// 创建客户端（测试环境）
$client = new AllinpayClient(AppConfig::ENV_TEST);

// 获取配置
$config = $client->getConfig();

// 使用各种服务
$userService = $client->getUserService();      // 用户服务
$orderService = $client->getOrderService();    // 订单服务
$queryService = $client->getQueryService();    // 查询服务
```

### 3. 运行示例
```bash
php example.php
```

## 核心服务

| 服务 | 说明 | 主要方法 |
|------|------|----------|
| **UserService** | 用户管理 | `createPersonalMemberApply()`, `createEnterpriseMember()` |
| **OrderService** | 订单处理 | `createConsumeOrder()`, `createRefundOrder()` |
| **QueryService** | 查询服务 | `queryOrder()`, `queryMember()` |
| **MerchantService** | 商家服务 | `createMerchant()`, `updateMerchant()` |
| **FileService** | 文件服务 | `uploadFile()`, `downloadFile()` |

## 环境配置

- **测试环境**: `AppConfig::ENV_TEST`
- **生产环境**: `AppConfig::ENV_PROD`

## 注意事项

1. **无需测试框架** - 直接使用即可
2. **配置已内置** - 测试环境配置已预设
3. **RPC调用** - 通过HTTP直接调用通联支付API
4. **简单为主** - 专注于核心业务逻辑

## 示例输出

运行 `php example.php` 会显示：
- 当前环境配置
- 可用的API接口URL
- 所有可用的服务类
- 基本使用方法

这就是一个简单的Composer库，无需复杂的测试框架！
