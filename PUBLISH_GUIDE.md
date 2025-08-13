# 通联支付云商通PHP SDK - Composer发布指南

## 当前状态

✅ **已完成的准备工作：**
- 重构所有类的命名空间为PSR-4标准 (`Allinpay\YunshangTong`)
- 更新composer.json配置，添加必要的依赖和脚本
- 创建Git标签 v1.0.0
- 代码已推送到GitHub仓库

## 发布到Composer的步骤

### 1. 确认GitHub仓库信息

请确认您的GitHub仓库地址是否正确：
- 当前配置的仓库：`https://github.com/your-username/KA-Demo-PHP`
- 如果需要更新，请修改composer.json中的homepage和support.source字段

### 2. 注册Packagist账号

1. 访问 [https://packagist.org](https://packagist.org)
2. 使用GitHub账号登录或注册新账号
3. 确保您有权限管理包

### 3. 提交包到Packagist

1. 登录Packagist后，点击右上角的 "Submit"
2. 输入您的GitHub仓库URL：`https://github.com/your-username/KA-Demo-PHP`
3. 点击 "Check" 验证仓库
4. 如果验证通过，点击 "Submit" 提交

### 4. 配置自动更新（推荐）

1. 在Packagist包页面，找到 "GitHub Service Hook" 部分
2. 点击 "Enable" 启用自动更新
3. 这样每次推送新标签时，Packagist会自动更新

### 5. 验证发布

发布成功后，用户可以通过以下命令安装：

```bash
composer require allinpay/yunshangtong
```

## 包信息

- **包名**: `allinpay/yunshangtong`
- **版本**: `v1.0.0`
- **许可证**: MIT
- **PHP版本要求**: >= 7.4

## 使用示例

```php
<?php
require_once 'vendor/autoload.php';

use Allinpay\YunshangTong\AllinpayServiceFactory;

// 创建服务工厂
$factory = AllinpayServiceFactory::create();

// 获取会员服务
$memberService = $factory->getMemberService();

// 获取支付服务
$paymentService = $factory->getPaymentService();

// 个人用户开户
$personalData = [
    'signNum' => 'personal_001',
    'name' => '张三',
    'cerNum' => '110101199001011234',
    'acctNum' => '6217858000141669850',
    'phone' => '13800138000'
];

$result = $factory->personalUserAccountOpening($personalData);
```

## 后续维护

### 发布新版本

1. 修改代码后提交到Git
2. 创建新的版本标签：
   ```bash
   git tag -a v1.0.1 -m "Release v1.0.1 - 修复bug"
   git push origin v1.0.1
   ```
3. 如果启用了自动更新，Packagist会自动检测新版本

### 版本规范

请遵循语义化版本规范：
- **主版本号**：不兼容的API修改
- **次版本号**：向下兼容的功能性新增
- **修订号**：向下兼容的问题修正

## 注意事项

1. **命名空间**：所有类都使用 `Allinpay\YunshangTong` 命名空间
2. **依赖管理**：确保所有依赖包都在composer.json中正确声明
3. **文档更新**：每次发布新版本时更新README.md
4. **测试**：建议在发布前进行充分测试

## 故障排除

### 常见问题

1. **包名冲突**：如果包名已存在，需要选择不同的包名
2. **版本标签**：确保Git标签格式正确（如v1.0.0）
3. **composer.json验证**：使用 `composer validate` 检查配置文件

### 验证命令

```bash
# 验证composer.json
composer validate

# 检查autoload
composer dump-autoload

# 安装依赖
composer install
```

## 联系支持

如果在发布过程中遇到问题：
1. 检查Packagist的错误信息
2. 确认GitHub仓库的可访问性
3. 验证composer.json的格式正确性

---

**祝您发布成功！** 🎉