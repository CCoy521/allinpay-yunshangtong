# AllinpayYST SDK - 通联支付云商通PHP SDK

一个功能完整、企业级的通联支付云商通PHP SDK，提供安全、易用的API接口。

## 特性

- ✅ **功能完整**: 支持会员管理、交易处理、查询服务、文件操作等全部业务场景
- ✅ **安全可靠**: 内置SM2签名验签、SM4数据加密，确保数据安全
- ✅ **易于使用**: 简洁的API设计，支持链式调用
- ✅ **类型安全**: 严格的参数类型检查和异常处理
- ✅ **日志完整**: 完善的日志记录，便于问题排查
- ✅ **配置灵活**: 支持多环境配置，测试/生产环境一键切换

## 系统要求

- PHP >= 8.0
- Composer
- 扩展: openssl, curl, json

## 安装

```bash
composer install
```

## 快速开始

### 1. 基础配置

```php
<?php
use src\AllinpayYST;
use config\DemoConfig;

// 使用默认配置
$yst = new AllinpayYST();

// 或使用自定义配置
$config = new DemoConfig();
$config->setAppId('your_app_id');
$config->setSecretKey('your_secret_key');
$yst = new AllinpayYST($config);

// 设置通知地址
$yst->setNotifyUrl('http://your-domain.com/notify');
```

### 2. 个人会员实名认证

```php
$memberData = [
    'signNum' => 'member_001',           // 会员编号
    'name' => '张三',                    // 姓名
    'cerNum' => '110101199001011234',    // 身份证号
    'acctNum' => '6217858000141669850',  // 银行卡号
    'phone' => '13800138000',            // 手机号
    'bindType' => '8'                    // 绑卡类型：8-银行卡四要素
];

$result = $yst->memberPersonalAuth($memberData);

if ($result['success']) {
    echo "认证成功: " . $result['bizData'];
} else {
    echo "认证失败: " . $result['message'];
}
```

### 3. 转账交易

```php
$transferData = [
    'signNum' => 'member_001',        // 转出方会员编号
    'inSignNum' => 'member_002',      // 转入方会员编号
    'orderAmount' => 10000,           // 转账金额（分）
    'summary' => '转账备注'
];

$result = $yst->transfer($transferData);

if ($result['success']) {
    echo "转账成功: " . $result['bizData'];
} else {
    echo "转账失败: " . $result['message'];
}
```

### 4. 余额查询

```php
$result = $yst->queryBalance('member_001', 1); // 1-簿记账户

if ($result['success']) {
    echo "余额查询成功: " . $result['bizData'];
} else {
    echo "余额查询失败: " . $result['message'];
}
```

## 完整功能列表

### 会员管理

| 方法 | 说明 | 参数 |
|------|------|------|
| `memberPersonalAuth()` | 个人会员实名认证 | 会员信息数组 |
| `memberCompanyRegister()` | 企业会员注册 | 企业信息数组 |
| `memberBindCard()` | 会员绑卡 | 绑卡信息数组 |

### 交易处理

| 方法 | 说明 | 参数 |
|------|------|------|
| `transfer()` | 转账申请 | 转账信息数组 |
| `recharge()` | 充值申请 | 充值信息数组 |
| `withdraw()` | 提现申请 | 提现信息数组 |

### 查询服务

| 方法 | 说明 | 参数 |
|------|------|------|
| `queryTransaction()` | 交易查询 | 订单号, 会员编号(可选) |
| `queryBalance()` | 余额查询 | 会员编号, 账户类型 |

### 文件服务

| 方法 | 说明 | 参数 |
|------|------|------|
| `uploadFile()` | 文件上传 | 文件路径, 文件类型 |
| `downloadFile()` | 文件下载 | 文件ID, 保存路径 |

### 工具方法

| 方法 | 说明 | 返回值 |
|------|------|--------|
| `generateTraceNum()` | 生成交易流水号 | string |
| `encryptSensitiveData()` | 加密敏感数据 | string |
| `decryptSensitiveData()` | 解密敏感数据 | string |
| `setNotifyUrl()` | 设置通知地址 | self (支持链式调用) |

## 响应格式

所有业务方法都返回统一的响应格式：

```php
[
    'success' => true,                    // 是否成功
    'code' => '0000',                     // 响应码
    'message' => '成功',                  // 响应消息
    'transCode' => '1010',                // 交易码
    'bizData' => '{"key":"value"}',       // 业务数据(JSON字符串)
    'timestamp' => '2025-01-13 15:30:00'  // 响应时间
]
```

## 错误处理

SDK提供完善的异常处理机制：

```php
try {
    $result = $yst->transfer($transferData);
    
    if (!$result['success']) {
        // 业务失败处理
        echo "业务失败: " . $result['message'];
        echo "错误码: " . $result['code'];
    }
    
} catch (Exception $e) {
    // 系统异常处理
    echo "系统异常: " . $e->getMessage();
}
```

## 常见错误码

| 错误码 | 说明 |
|--------|------|
| 0000 | 成功 |
| 1001 | 参数错误 |
| 1002 | 签名验证失败 |
| 1003 | 系统异常 |
| 2001 | 会员不存在 |
| 2002 | 账户余额不足 |
| 3001 | 交易失败 |
| 3002 | 交易超时 |

## 配置说明

### 环境配置

SDK支持测试环境和生产环境配置：

```php
// 测试环境（默认）
$config = new DemoConfig();

// 生产环境
$config = new DemoConfig();
$config->setUrl('https://ibsapi.allinpay.com/yst-service-api/tx/handle');
$config->setMemberUrl('https://ibsapi.allinpay.com/yst-service-api/tm/handle');
$config->setQueryUrl('https://ibsapi.allinpay.com/yst-service-api/tq/handle');
```

### 密钥配置

```php
$config->setAppId('your_app_id');                    // 应用ID
$config->setSpAppId('your_sp_app_id');               // 服务商应用ID
$config->setSecretKey('your_secret_key');            // 密钥
$config->setPrivateKeyStr('your_private_key');       // 私钥
$config->setAllinpayPublicKeyStr('tl_public_key');   // 通联公钥
```

## 安全说明

1. **数据加密**: 身份证号、银行卡号等敏感数据自动使用SM4算法加密
2. **签名验证**: 所有请求自动使用SM2算法签名，响应自动验签
3. **HTTPS传输**: 生产环境强制使用HTTPS协议
4. **密钥安全**: 私钥和密钥请妥善保管，不要提交到代码仓库

## 日志记录

SDK内置完整的日志记录功能：

```php
// 日志会自动记录到 stderr
// 包含请求参数、响应结果、错误信息等
```

## 示例代码

完整的使用示例请参考 `src/AllinpayYSTExample.php` 文件。

运行示例：

```bash
php src/AllinpayYSTExample.php
```

## 技术支持

- 通联支付官方文档: [https://open.allinpay.com](https://open.allinpay.com)
- 技术支持邮箱: support@allinpay.com

## 版本历史

### v2.0.0 (2025-01-13)
- 重构SDK架构，提供统一的API入口
- 增加完善的异常处理和日志记录
- 支持链式调用和批量操作
- 优化安全机制和参数验证

### v1.0.0
- 基础功能实现
- 支持基本的交易和查询功能

## 许可证

MIT License

## 贡献

欢迎提交Issue和Pull Request来改进这个SDK。

---

**注意**: 本SDK仅供学习和测试使用，生产环境使用前请充分测试并确保符合相关法规要求。