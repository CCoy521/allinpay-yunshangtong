<?php
namespace config;

/**
 * AllinpayYST 配置管理类
 * 
 * 提供更灵活的配置管理，支持环境切换和配置验证
 * 
 * @author CodeBuddy
 * @version 1.0
 * @date 2025/01/13
 */
class AllinpayConfig
{
    /** @var string 环境类型 */
    private string $environment = 'test';
    
    /** @var array 测试环境配置 */
    private array $testConfig = [
        'url' => 'http://116.228.64.55:28082/yst-service-api/tx/handle',
        'memberUrl' => 'http://116.228.64.55:28082/yst-service-api/tm/handle',
        'queryUrl' => 'http://116.228.64.55:28082/yst-service-api/tq/handle',
        'cusUrl' => 'http://192.168.14.132:8888/ystcusapi/api',
        'fileUploadUrl' => 'http://116.228.64.55:28082/yst-service-api/file/upload',
        'fileDownloadUrl' => 'http://116.228.64.55:28082/yst-service-api/file/download',
        'allinpayPublicKey' => 'MFkwEwYHKoZIzj0CAQYIKoEcz1UBgi0DQgAEu9LNkJlyLtjJxtQWIGlcZ/hyHt5eZ7LEH1nfOiK1H9HsE1cMPu5KK5jZVTtAyc7lPMXixUMirf6A3tMbuMbgqg=='
    ];
    
    /** @var array 生产环境配置 */
    private array $prodConfig = [
        'url' => 'https://ibsapi.allinpay.com/yst-service-api/tx/handle',
        'memberUrl' => 'https://ibsapi.allinpay.com/yst-service-api/tm/handle',
        'queryUrl' => 'https://ibsapi.allinpay.com/yst-service-api/tq/handle',
        'cusUrl' => 'https://ibsapi.allinpay.com/ystcusapi/api',
        'fileUploadUrl' => 'https://ibsapi.allinpay.com/yst-service-api/file/upload',
        'fileDownloadUrl' => 'https://ibsapi.allinpay.com/yst-service-api/file/download',
        'allinpayPublicKey' => 'MFkwEwYHKoZIzj0CAQYIKoEcz1UBgi0DQgAE/VKHBem28IXD30yuZN1QcNgGE4gzqgd/eX1ZEouUleLNfrnQJkOs7LzAag3q10uaH/e9+5JyJDx3ULfKS4QZPw=='
    ];
    
    /** @var array 应用配置 */
    private array $appConfig = [
        'appId' => '',
        'spAppId' => '',
        'privateKey' => '',
        'secretKey' => '',
        'format' => 'json',
        'charset' => 'UTF-8',
        'signType' => 'SM3withSM2',
        'version' => '1.0',
        'notifyUrl' => ''
    ];

    /**
     * 构造函数
     * 
     * @param string $environment 环境类型 test|prod
     */
    public function __construct(string $environment = 'test')
    {
        $this->setEnvironment($environment);
    }

    /**
     * 设置环境
     * 
     * @param string $environment 环境类型
     * @return self
     * @throws \InvalidArgumentException
     */
    public function setEnvironment(string $environment): self
    {
        if (!in_array($environment, ['test', 'prod'])) {
            throw new \InvalidArgumentException("不支持的环境类型: {$environment}");
        }
        
        $this->environment = $environment;
        return $this;
    }

    /**
     * 获取当前环境
     * 
     * @return string
     */
    public function getEnvironment(): string
    {
        return $this->environment;
    }

    /**
     * 是否为生产环境
     * 
     * @return bool
     */
    public function isProduction(): bool
    {
        return $this->environment === 'prod';
    }

    /**
     * 设置应用配置
     * 
     * @param array $config 配置数组
     * @return self
     */
    public function setAppConfig(array $config): self
    {
        $this->appConfig = array_merge($this->appConfig, $config);
        return $this;
    }

    /**
     * 获取API地址
     * 
     * @param string $type 地址类型 url|memberUrl|queryUrl|cusUrl|fileUploadUrl|fileDownloadUrl
     * @return string
     */
    public function getApiUrl(string $type = 'url'): string
    {
        $config = $this->environment === 'prod' ? $this->prodConfig : $this->testConfig;
        return $config[$type] ?? '';
    }

    /**
     * 获取通联公钥
     * 
     * @return string
     */
    public function getAllinpayPublicKey(): string
    {
        $config = $this->environment === 'prod' ? $this->prodConfig : $this->testConfig;
        return $config['allinpayPublicKey'];
    }

    /**
     * 获取应用ID
     * 
     * @return string
     */
    public function getAppId(): string
    {
        return $this->appConfig['appId'];
    }

    /**
     * 设置应用ID
     * 
     * @param string $appId
     * @return self
     */
    public function setAppId(string $appId): self
    {
        $this->appConfig['appId'] = $appId;
        return $this;
    }

    /**
     * 获取服务商应用ID
     * 
     * @return string
     */
    public function getSpAppId(): string
    {
        return $this->appConfig['spAppId'];
    }

    /**
     * 设置服务商应用ID
     * 
     * @param string $spAppId
     * @return self
     */
    public function setSpAppId(string $spAppId): self
    {
        $this->appConfig['spAppId'] = $spAppId;
        return $this;
    }

    /**
     * 获取私钥
     * 
     * @return string
     */
    public function getPrivateKey(): string
    {
        return $this->appConfig['privateKey'];
    }

    /**
     * 设置私钥
     * 
     * @param string $privateKey
     * @return self
     */
    public function setPrivateKey(string $privateKey): self
    {
        $this->appConfig['privateKey'] = $privateKey;
        return $this;
    }

    /**
     * 获取密钥
     * 
     * @return string
     */
    public function getSecretKey(): string
    {
        return $this->appConfig['secretKey'];
    }

    /**
     * 设置密钥
     * 
     * @param string $secretKey
     * @return self
     */
    public function setSecretKey(string $secretKey): self
    {
        $this->appConfig['secretKey'] = $secretKey;
        return $this;
    }

    /**
     * 获取通知地址
     * 
     * @return string
     */
    public function getNotifyUrl(): string
    {
        return $this->appConfig['notifyUrl'];
    }

    /**
     * 设置通知地址
     * 
     * @param string $notifyUrl
     * @return self
     */
    public function setNotifyUrl(string $notifyUrl): self
    {
        $this->appConfig['notifyUrl'] = $notifyUrl;
        return $this;
    }

    /**
     * 获取格式
     * 
     * @return string
     */
    public function getFormat(): string
    {
        return $this->appConfig['format'];
    }

    /**
     * 获取字符集
     * 
     * @return string
     */
    public function getCharset(): string
    {
        return $this->appConfig['charset'];
    }

    /**
     * 获取签名类型
     * 
     * @return string
     */
    public function getSignType(): string
    {
        return $this->appConfig['signType'];
    }

    /**
     * 获取版本
     * 
     * @return string
     */
    public function getVersion(): string
    {
        return $this->appConfig['version'];
    }

    /**
     * 验证配置完整性
     * 
     * @return array 验证结果
     */
    public function validate(): array
    {
        $errors = [];
        
        $required = ['appId', 'spAppId', 'privateKey', 'secretKey'];
        
        foreach ($required as $field) {
            if (empty($this->appConfig[$field])) {
                $errors[] = "缺少必需配置: {$field}";
            }
        }
        
        return [
            'valid' => empty($errors),
            'errors' => $errors
        ];
    }

    /**
     * 转换为DemoConfig对象（兼容性）
     * 
     * @return \config\DemoConfig
     */
    public function toDemoConfig(): \config\DemoConfig
    {
        $demoConfig = new \config\DemoConfig(
            $this->getAppId(),
            $this->getSpAppId(),
            $this->getApiUrl('url')
        );
        
        $demoConfig->setPrivateKeyStr($this->getPrivateKey());
        $demoConfig->setAllinpayPublicKeyStr($this->getAllinpayPublicKey());
        $demoConfig->setSecretKey($this->getSecretKey());
        $demoConfig->setMemberUrl($this->getApiUrl('memberUrl'));
        $demoConfig->setQueryUrl($this->getApiUrl('queryUrl'));
        $demoConfig->setFileUploadUrl($this->getApiUrl('fileUploadUrl'));
        $demoConfig->setFileDownloadUrl($this->getApiUrl('fileDownloadUrl'));
        $demoConfig->setNotifyUrl($this->getNotifyUrl());
        
        return $demoConfig;
    }

    /**
     * 从环境变量加载配置
     * 
     * @return self
     */
    public static function fromEnv(): self
    {
        $config = new self(getenv('ALLINPAY_ENV') ?: 'test');
        
        $config->setAppConfig([
            'appId' => getenv('ALLINPAY_APP_ID') ?: '',
            'spAppId' => getenv('ALLINPAY_SP_APP_ID') ?: '',
            'privateKey' => getenv('ALLINPAY_PRIVATE_KEY') ?: '',
            'secretKey' => getenv('ALLINPAY_SECRET_KEY') ?: '',
            'notifyUrl' => getenv('ALLINPAY_NOTIFY_URL') ?: ''
        ]);
        
        return $config;
    }

    /**
     * 从配置文件加载配置
     * 
     * @param string $configFile 配置文件路径
     * @return self
     * @throws \Exception
     */
    public static function fromFile(string $configFile): self
    {
        if (!file_exists($configFile)) {
            throw new \Exception("配置文件不存在: {$configFile}");
        }
        
        $configData = json_decode(file_get_contents($configFile), true);
        
        if (json_last_error() !== JSON_ERROR_NONE) {
            throw new \Exception("配置文件格式错误: " . json_last_error_msg());
        }
        
        $config = new self($configData['environment'] ?? 'test');
        $config->setAppConfig($configData['app'] ?? []);
        
        return $config;
    }

    /**
     * 导出配置到数组
     * 
     * @return array
     */
    public function toArray(): array
    {
        return [
            'environment' => $this->environment,
            'app' => $this->appConfig,
            'api_urls' => $this->environment === 'prod' ? $this->prodConfig : $this->testConfig
        ];
    }

    /**
     * 导出配置到JSON
     * 
     * @return string
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
    }
}