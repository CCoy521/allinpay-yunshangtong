<?php

namespace AllinpayYunshangtong\VO;

/**
 * 基础响应VO类
 * 基于通联支付KA客户版接口规范的公共响应参数
 */
class BaseResponse
{
    /**
     * 响应码
     */
    protected string $code;
    
    /**
     * 响应消息
     */
    protected string $msg;
    
    /**
     * 业务响应码
     */
    protected ?string $respCode = null;
    
    /**
     * 业务响应消息
     */
    protected ?string $respMsg = null;
    
    /**
     * 响应数据
     */
    protected ?array $data = null;
    
    /**
     * 签名
     */
    protected ?string $sign = null;
    
    /**
     * 原始响应数据
     */
    protected ?array $rawData = null;
    
    public function __construct(array $responseData = [])
    {
        $this->rawData = $responseData;
        $this->parseResponse($responseData);
    }
    
    /**
     * 解析响应数据
     */
    protected function parseResponse(array $responseData): void
    {
        $this->code = $responseData['code'] ?? '';
        $this->msg = $responseData['msg'] ?? '';
        $this->respCode = $responseData['respCode'] ?? null;
        $this->respMsg = $responseData['respMsg'] ?? null;
        $this->data = $responseData['data'] ?? null;
        $this->sign = $responseData['sign'] ?? null;
    }
    
    /**
     * 是否成功
     */
    public function isSuccess(): bool
    {
        return $this->code === '000000';
    }
    
    /**
     * 是否业务成功
     */
    public function isBusinessSuccess(): bool
    {
        return $this->isSuccess() && $this->respCode === '000000';
    }
    
    /**
     * 获取响应码
     */
    public function getCode(): string
    {
        return $this->code;
    }
    
    /**
     * 获取响应消息
     */
    public function getMsg(): string
    {
        return $this->msg;
    }
    
    /**
     * 获取业务响应码
     */
    public function getRespCode(): ?string
    {
        return $this->respCode;
    }
    
    /**
     * 获取业务响应消息
     */
    public function getRespMsg(): ?string
    {
        return $this->respMsg;
    }
    
    /**
     * 获取响应数据
     */
    public function getData(): ?array
    {
        return $this->data;
    }
    
    /**
     * 获取签名
     */
    public function getSign(): ?string
    {
        return $this->sign;
    }
    
    /**
     * 获取原始响应数据
     */
    public function getRawData(): ?array
    {
        return $this->rawData;
    }
    
    /**
     * 获取错误信息
     */
    public function getErrorMessage(): string
    {
        if (!$this->isSuccess()) {
            return "系统错误: {$this->code} - {$this->msg}";
        }
        
        if ($this->respCode !== '000000') {
            return "业务错误: {$this->respCode} - {$this->respMsg}";
        }
        
        return '';
    }
    
    /**
     * 转换为数组
     */
    public function toArray(): array
    {
        return [
            'code' => $this->code,
            'msg' => $this->msg,
            'respCode' => $this->respCode,
            'respMsg' => $this->respMsg,
            'data' => $this->data,
            'sign' => $this->sign,
            'success' => $this->isSuccess(),
            'businessSuccess' => $this->isBusinessSuccess()
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
