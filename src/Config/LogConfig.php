<?php

namespace AllinpayYunshangtong\Config;

/**
 * 日志配置文件
 * 管理日志记录的各种配置选项
 */
class LogConfig
{
    // 日志级别
    const LEVEL_DEBUG = 'debug';
    const LEVEL_INFO = 'info';
    const LEVEL_WARNING = 'warning';
    const LEVEL_ERROR = 'error';
    const LEVEL_CRITICAL = 'critical';
    
    // 日志格式
    const FORMAT_JSON = 'json';
    const FORMAT_TEXT = 'text';
    
    // 默认配置
    private array $defaultConfig = [
        'enabled' => true,
        'level' => self::LEVEL_INFO,
        'format' => self::FORMAT_JSON,
        'maxFiles' => 30,
        'maxSize' => '10MB',
        'path' => './logs',
        'filename' => 'allinpay_{date}.log',
        'dateFormat' => 'Y-m-d',
        'includeContext' => true,
        'includeTrace' => false
    ];
    
    // 当前配置
    private array $config;
    
    public function __construct(array $config = [])
    {
        $this->config = array_merge($this->defaultConfig, $config);
    }
    
    /**
     * 获取配置值
     */
    public function get(string $key): mixed
    {
        return $this->config[$key] ?? null;
    }
    
    /**
     * 设置配置值
     */
    public function set(string $key, mixed $value): self
    {
        $this->config[$key] = $value;
        return $this;
    }
    
    /**
     * 是否启用日志
     */
    public function isEnabled(): bool
    {
        return $this->config['enabled'];
    }
    
    /**
     * 获取日志级别
     */
    public function getLevel(): string
    {
        return $this->config['level'];
    }
    
    /**
     * 获取日志格式
     */
    public function getFormat(): string
    {
        return $this->config['format'];
    }
    
    /**
     * 获取日志路径
     */
    public function getLogPath(): string
    {
        return $this->config['path'];
    }
    
    /**
     * 获取日志文件名
     */
    public function getLogFilename(): string
    {
        $date = date($this->config['dateFormat']);
        return str_replace('{date}', $date, $this->config['filename']);
    }
    
    /**
     * 获取完整日志文件路径
     */
    public function getFullLogPath(): string
    {
        return $this->getLogPath() . DIRECTORY_SEPARATOR . $this->getLogFilename();
    }
    
    /**
     * 是否包含上下文信息
     */
    public function includeContext(): bool
    {
        return $this->config['includeContext'];
    }
    
    /**
     * 是否包含堆栈跟踪
     */
    public function includeTrace(): bool
    {
        return $this->config['includeTrace'];
    }
    
    /**
     * 获取最大文件数量
     */
    public function getMaxFiles(): int
    {
        return $this->config['maxFiles'];
    }
    
    /**
     * 获取最大文件大小
     */
    public function getMaxSize(): string
    {
        return $this->config['maxSize'];
    }
    
    /**
     * 检查是否应该记录指定级别的日志
     */
    public function shouldLog(string $level): bool
    {
        if (!$this->isEnabled()) {
            return false;
        }
        
        $levels = [
            self::LEVEL_DEBUG => 1,
            self::LEVEL_INFO => 2,
            self::LEVEL_WARNING => 3,
            self::LEVEL_ERROR => 4,
            self::LEVEL_CRITICAL => 5
        ];
        
        $currentLevel = $levels[$this->getLevel()] ?? 2;
        $requestedLevel = $levels[$level] ?? 2;
        
        return $requestedLevel >= $currentLevel;
    }
    
    /**
     * 获取所有配置
     */
    public function getAll(): array
    {
        return $this->config;
    }
}
