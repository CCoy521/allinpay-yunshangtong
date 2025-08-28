<?php

namespace AllinpayYunshangtong\Common;

/**
 * 通联支付异常基类
 * 提供统一的异常处理机制
 */
class AllinpayException extends \Exception
{
    // 异常类型
    const TYPE_CONFIG = 'config';
    const TYPE_NETWORK = 'network';
    const TYPE_SIGNATURE = 'signature';
    const TYPE_BUSINESS = 'business';
    const TYPE_VALIDATION = 'validation';
    const TYPE_SYSTEM = 'system';
    
    // 异常代码
    const CODE_CONFIG_ERROR = 1001;
    const CODE_NETWORK_ERROR = 2001;
    const CODE_SIGNATURE_ERROR = 3001;
    const CODE_BUSINESS_ERROR = 4001;
    const CODE_VALIDATION_ERROR = 5001;
    const CODE_SYSTEM_ERROR = 6001;
    
    protected string $type;
    protected array $context;
    protected ?string $requestId;
    protected ?string $responseCode;
    protected ?string $responseMessage;
    
    public function __construct(
        string $message = '',
        int $code = 0,
        string $type = self::TYPE_SYSTEM,
        array $context = [],
        ?string $requestId = null,
        ?string $responseCode = null,
        ?string $responseMessage = null,
        ?\Throwable $previous = null
    ) {
        parent::__construct($message, $code, $previous);
        
        $this->type = $type;
        $this->context = $context;
        $this->requestId = $requestId;
        $this->responseCode = $responseCode;
        $this->responseMessage = $responseMessage;
    }
    
    /**
     * 创建配置异常
     */
    public static function configError(string $message, array $context = [], ?\Throwable $previous = null): self
    {
        return new self(
            $message,
            self::CODE_CONFIG_ERROR,
            self::TYPE_CONFIG,
            $context,
            null,
            null,
            null,
            $previous
        );
    }
    
    /**
     * 创建网络异常
     */
    public static function networkError(string $message, array $context = [], ?\Throwable $previous = null): self
    {
        return new self(
            $message,
            self::CODE_NETWORK_ERROR,
            self::TYPE_NETWORK,
            $context,
            null,
            null,
            null,
            $previous
        );
    }
    
    /**
     * 创建签名异常
     */
    public static function signatureError(string $message, array $context = [], ?\Throwable $previous = null): self
    {
        return new self(
            $message,
            self::CODE_SIGNATURE_ERROR,
            self::TYPE_SIGNATURE,
            $context,
            null,
            null,
            null,
            $previous
        );
    }
    
    /**
     * 创建业务异常
     */
    public static function businessError(
        string $message,
        string $responseCode,
        string $responseMessage,
        array $context = [],
        ?string $requestId = null,
        ?\Throwable $previous = null
    ): self {
        return new self(
            $message,
            self::CODE_BUSINESS_ERROR,
            self::TYPE_BUSINESS,
            $context,
            $requestId,
            $responseCode,
            $responseMessage,
            $previous
        );
    }
    
    /**
     * 创建验证异常
     */
    public static function validationError(string $message, array $context = [], ?\Throwable $previous = null): self
    {
        return new self(
            $message,
            self::CODE_VALIDATION_ERROR,
            self::TYPE_VALIDATION,
            $context,
            null,
            null,
            null,
            $previous
        );
    }
    
    /**
     * 创建系统异常
     */
    public static function systemError(string $message, array $context = [], ?\Throwable $previous = null): self
    {
        return new self(
            $message,
            self::CODE_SYSTEM_ERROR,
            self::TYPE_SYSTEM,
            $context,
            null,
            null,
            null,
            $previous
        );
    }
    
    /**
     * 获取异常类型
     */
    public function getType(): string
    {
        return $this->type;
    }
    
    /**
     * 获取上下文信息
     */
    public function getContext(): array
    {
        return $this->context;
    }
    
    /**
     * 获取请求ID
     */
    public function getRequestId(): ?string
    {
        return $this->requestId;
    }
    
    /**
     * 获取响应代码
     */
    public function getResponseCode(): ?string
    {
        return $this->responseCode;
    }
    
    /**
     * 获取响应消息
     */
    public function getResponseMessage(): ?string
    {
        return $this->responseMessage;
    }
    
    /**
     * 是否为配置异常
     */
    public function isConfigError(): bool
    {
        return $this->type === self::TYPE_CONFIG;
    }
    
    /**
     * 是否为网络异常
     */
    public function isNetworkError(): bool
    {
        return $this->type === self::TYPE_NETWORK;
    }
    
    /**
     * 是否为签名异常
     */
    public function isSignatureError(): bool
    {
        return $this->type === self::TYPE_SIGNATURE;
    }
    
    /**
     * 是否为业务异常
     */
    public function isBusinessError(): bool
    {
        return $this->type === self::TYPE_BUSINESS;
    }
    
    /**
     * 是否为验证异常
     */
    public function isValidationError(): bool
    {
        return $this->type === self::TYPE_VALIDATION;
    }
    
    /**
     * 是否为系统异常
     */
    public function isSystemError(): bool
    {
        return $this->type === self::TYPE_SYSTEM;
    }
    
    /**
     * 获取详细的错误信息
     */
    public function getDetailedMessage(): string
    {
        $message = $this->getMessage();
        
        if ($this->responseCode) {
            $message .= " (响应代码: {$this->responseCode})";
        }
        
        if ($this->responseMessage) {
            $message .= " (响应消息: {$this->responseMessage})";
        }
        
        if ($this->requestId) {
            $message .= " (请求ID: {$this->requestId})";
        }
        
        return $message;
    }
    
    /**
     * 转换为数组
     */
    public function toArray(): array
    {
        return [
            'type' => $this->type,
            'code' => $this->code,
            'message' => $this->message,
            'detailedMessage' => $this->getDetailedMessage(),
            'context' => $this->context,
            'requestId' => $this->requestId,
            'responseCode' => $this->responseCode,
            'responseMessage' => $this->responseMessage,
            'file' => $this->file,
            'line' => $this->line,
            'trace' => $this->getTraceAsString()
        ];
    }
    
    /**
     * 转换为JSON字符串
     */
    public function toJson(): string
    {
        return json_encode($this->toArray(), JSON_UNESCAPED_UNICODE);
    }
}
