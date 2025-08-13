# AllinpayYST SDK - 通联支付云商通PHP SDK

一个功能完整、企业级的通联支付云商通PHP SDK，按照业务流程图分离设计，提供安全、易用的API接口。

## 特性

- ✅ **角色分离**: 按照业务流程图分离会员服务和支付服务，职责清晰
- ✅ **功能完整**: 支持会员管理、交易处理、查询服务、文件操作等全部业务场景
- ✅ **安全可靠**: 内置SM2签名验签、SM4数据加密，确保数据安全
- ✅ **易于使用**: 简洁的API设计，支持链式调用和工厂模式
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

## 架构设计

根据通联支付会员开户业务流程图，SDK按照不同角色职责进行了模块化设计：

### 🏗️ 架构图

```
┌─────────────────────────────────────────────────────────────┐
│                   AllinpayServiceFactory                    │
│                      (服务工厂)                              │
├─────────────────────────────────────────────────────────────┤
│  ┌─────────────────┐              ┌─────────────────┐       │
│  │  MemberService  │              │ PaymentService  │       │
│  │   (会员服务)     │              │   (支付服务)     │       │
│  │                 │              │                 │       │
│  │ • 个人开户       │              │ • 转账交易       │       │
│  │ • 企业开户       │              │ • 充值提现       │       │
│  │ • 会员管理       │              │ • 协议支付       │       │
│  │ • 绑卡解绑       │              │ • 混合云支付     │       │
│  │ • 信息查询       │              │ • 交易查询       │       │
│  └─────────────────┘              └─────────────────┘       │
└─────────────────────────────────────────────────────────────┘
                              │
                    ┌─────────┴─────────┐
                    │    TxClient       │
                    │   (交易客户端)     │
                    │                   │
                    │ • SM2签名验签      │
                    │ • SM4数据加密      │
                    │ • HTTP请求处理     │
                    │ • 日志记录         │
                    └───────────────────┘
```

### 📋 业务流程对应

| 流程图角色 | SDK模块 | 主要功能 |
|-----------|---------|----------|
| **买方用户** | MemberService | 个人/企业用户开户申请 |
| **平台商户** | MemberService + PaymentService | 会员管理、支付处理 |
| **云商通二代** | AllinpayServiceFactory | 统一服务入口、流程编排 |

## 快速开始

### 1. 使用服务工厂（推荐）

```php
<?php
use src\AllinpayServiceFactory;

// 创建服务工厂
$factory = AllinpayServiceFactory::create();

// 获取会员服务
$memberService = $factory->getMemberService();

// 获取支付服务
$paymentService = $factory->getPaymentService();
```

### 2. 个人用户开户流程

```php
// 个人用户数据
$personalData = [
    'signNum' => 'personal_001',         // 会员编号
    'name' => '张三',                    // 姓名
    'cerNum' => '110101199001011234',    // 身份证号
    'acctNum' => '6217858000141669850',  // 银行卡号
    'phone' => '13800138000',            // 手机号
    'bindType' => '8'                    // 绑卡类型：8-银行卡四要素
];

// 完整开户流程
$result = $factory->personalUserAccountOpening($personalData);

if ($result['success']) {
    echo "个人用户开户成功: " . $result['message'];
} else {
    echo "个人用户开户失败: " . $result['message'];
}
```

### 3. 企业用户开户流程

```php
// 企业用户数据
$companyData = [
    'signNum' => 'company_001',
    'companyName' => '测试科技有限公司',
    'licenseNo' => '91110000123456789X',  // 统一社会信用代码
    'legalName' => '李四',                // 法人姓名
    'legalCerNum' => '110101198001011234', // 法人身份证号
];

// 完整开户流程
$result = $factory->companyUserAccountOpening($companyData);

if ($result['success']) {
    echo "企业用户开户成功: " . $result['message'];
}
```

### 4. 支付业务处理

```php
// 转账申请
$transferData = [
    'signNum' => 'member_001',        // 转出方会员编号
    'inSignNum' => 'member_002',      // 转入方会员编号
    'orderAmount' => 10000,           // 转账金额（分）
    'summary' => '转账备注'
];

$result = $paymentService->transfer($transferData);

// 余额查询
$balanceResult = $paymentService->queryBalance('member_001', 1);
```

## 完整功能列表

### 会员服务 (MemberService)

| 方法 | 交易码 | 说明 | 对应流程 |
|------|--------|------|----------|
| `registerPersonalMember()` | 1010 | 个人会员注册 | 个人用户起草开通通联记账户申请 |
| `bindPersonalCard()` | 1011 | 个人会员绑卡 | 实名信息认证成功，银行卡信息验证成功 |
| `registerCompanyMember()` | 1020 | 企业会员注册 | 企业用户起草开通通联记账户申请 |
| `getCompanyAccountResult()` | 1022 | 获取企业开户结果 | 企业工商信息验证成功 |
| `openCompanyPaymentAccount()` | 1023 | 企业开通支付账户 | 企业会员开通支付账户 |
| `getMemberAccountStatus()` | 1024 | 获取开户受理状态 | 获取会员开账户受理状态 |
| `getMemberPaymentAccountNum()` | 1025 | 获取支付账户号 | 获取会员支付账户号 |
| `getPaymentAccountAuditDetail()` | 1026 | 支付账户审核详情 | 支付账户开户审核详情 |
| `getMemberInfo()` | 1027 | 会员信息查询 | 会员信息查询 |
| `getMemberBindCardInfo()` | 1030 | 绑卡信息查询 | 会员绑卡信息查询 |
| `unbindMemberCard()` | 1031 | 会员解绑银行卡 | 会员解绑银行卡 |
| `closeMemberAccount()` | 1032 | 会员销户 | 会员销户申请 |

### 支付服务 (PaymentService)

| 方法 | 交易码 | 说明 | 对应流程 |
|------|--------|------|----------|
| `transfer()` | 2084 | 转账申请 | 对公账户转入打款支付账户号 |
| `recharge()` | 2085 | 充值申请 | 充值到会员账户 |
| `withdraw()` | 2089 | 提现申请 | 从会员账户提现 |
| `batchTransfer()` | 2090 | 批量转账 | 批量转账申请 |
| `batchPay()` | 2091 | 批量代付 | 批量代付申请 |
| `queryTransaction()` | 3001 | 交易查询 | 划款入账验证 |
| `queryBalance()` | 3002 | 余额查询 | 查询账户余额 |
| `queryTransactionDetail()` | 4003 | 交易明细查询 | 查询交易明细 |
| `agreementSign()` | 1050 | 协议支付签约 | 协议支付签约申请 |
| `agreementUnsign()` | 1051 | 协议支付解约 | 协议支付解约申请 |
| `cashierPay()` | 3010 | 收银台支付 | 收银台支付申请 |
| `hybridCloudUnifiedOrder()` | 1070 | 混合云统一下单 | 混合云支付统一下单 |
| `queryHybridCloudPay()` | 1072 | 混合云支付查询 | 混合云支付订单查询 |

### 服务工厂 (AllinpayServiceFactory)

| 方法 | 说明 | 使用场景 |
|------|------|----------|
| `personalUserAccountOpening()` | 个人用户完整开户流程 | 买方用户开户 |
| `companyUserAccountOpening()` | 企业用户完整开户流程 | 买方用户开户 |
| `companyPaymentAccountOpening()` | 企业支付账户开通流程 | 平台商户业务 |
| `publicAccountTransfer()` | 对公账户转入打款 | 平台商户业务 |
| `verifyTransferAccount()` | 划款入账验证 | 平台商户业务 |

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
    $result = $memberService->registerPersonalMember($personalData);
    
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

### 高级配置管理

```php
use config\AllinpayConfig;

// 创建高级配置
$config = new AllinpayConfig('prod'); // 生产环境
$config->setAppId('your_app_id')
       ->setSecretKey('your_secret_key')
       ->setNotifyUrl('http://your-domain.com/notify');

// 转换为DemoConfig使用
$factory = AllinpayServiceFactory::createWithConfig($config->toDemoConfig());
```

## 安全说明

1. **数据加密**: 身份证号、银行卡号等敏感数据自动使用SM4算法加密
2. **签名验证**: 所有请求自动使用SM2算法签名，响应自动验签
3. **HTTPS传输**: 生产环境强制使用HTTPS协议
4. **参数校验**: 严格的业务参数合法性检查
5. **防重放**: 基于时间戳和随机数的防重放机制

## 日志记录

SDK内置完整的日志记录功能：

```php
// 日志会自动记录到 stderr
// 包含请求参数、响应结果、错误信息等
```

## 示例代码

### 完整业务流程示例

```bash
# 运行完整业务流程示例
php src/BusinessFlowExample.php
```

### 单独功能测试示例

```bash
# 运行原有功能示例
php src/AllinpayYSTExample.php
```

## 文件结构

```
├── src/
│   ├── test.php                    # 原统一SDK类（兼容性保留）
│   ├── AllinpayServiceFactory.php  # 服务工厂类
│   ├── BusinessFlowExample.php     # 业务流程示例
│   ├── AllinpayYSTExample.php      # 功能示例
│   ├── member/
│   │   └── MemberService.php       # 会员服务类
│   ├── payment/
│   │   └── PaymentService.php      # 支付服务类
│   └── config/
│       └── AllinpayConfig.php      # 高级配置管理类
├── README.md                       # 使用文档
└── config.example.json            # 配置文件示例
```

## 技术支持

- 通联支付官方文档: [https://prodoc.allinpay.com/doc/640/](https://prodoc.allinpay.com/doc/640/)
- 技术支持邮箱: support@allinpay.com

## 版本历史

### v2.1.0 (2025-01-13)
- 🎯 **重大更新**: 按照业务流程图重构架构，分离会员服务和支付服务
- ✨ 新增 `MemberService` 类，专门处理会员开户和管理业务
- ✨ 新增 `PaymentService` 类，专门处理支付交易业务
- ✨ 新增 `AllinpayServiceFactory` 服务工厂，提供统一服务管理
- ✨ 新增完整的业务流程示例和文档
- 🔧 优化错误处理和参数验证机制
- 📚 完善文档和使用示例

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