<?php

namespace AllinpayYunshangtong\Utils;

use AllinpayYunshangtong\Common\AllinpayException;

/**
 * 数据验证器
 * 提供各种数据验证功能
 */
class Validator
{
    /**
     * 验证手机号
     */
    public static function validatePhone(string $phone): bool
    {
        return preg_match('/^1[3-9]\d{9}$/', $phone) === 1;
    }
    
    /**
     * 验证身份证号
     */
    public static function validateIdCard(string $idCard): bool
    {
        // 简单的身份证号格式验证
        return preg_match('/^\d{17}[\dXx]$/', $idCard) === 1;
    }
    
    /**
     * 验证邮箱
     */
    public static function validateEmail(string $email): bool
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * 验证金额（分）
     */
    public static function validateAmount(int $amount): bool
    {
        return $amount > 0 && $amount <= 999999999; // 最大9.99亿分
    }
    
    /**
     * 验证订单号
     */
    public static function validateOrderNo(string $orderNo): bool
    {
        // 订单号格式：字母数字下划线，长度6-64
        return preg_match('/^[a-zA-Z0-9_]{6,64}$/', $orderNo) === 1;
    }
    
    /**
     * 验证用户ID
     */
    public static function validateBizUserId(string $bizUserId): bool
    {
        // 用户ID格式：字母数字下划线，长度1-64
        return preg_match('/^[a-zA-Z0-9_]{1,64}$/', $bizUserId) === 1;
    }
    
    /**
     * 验证日期格式（YYYYMMDD）
     */
    public static function validateDate(string $date): bool
    {
        if (!preg_match('/^\d{8}$/', $date)) {
            return false;
        }
        
        $year = substr($date, 0, 4);
        $month = substr($date, 4, 2);
        $day = substr($date, 6, 2);
        
        return checkdate((int)$month, (int)$day, (int)$year);
    }
    
    /**
     * 验证时间格式（HHMMSS）
     */
    public static function validateTime(string $time): bool
    {
        if (!preg_match('/^\d{6}$/', $time)) {
            return false;
        }
        
        $hour = (int)substr($time, 0, 2);
        $minute = (int)substr($time, 2, 2);
        $second = (int)substr($time, 4, 2);
        
        return $hour >= 0 && $hour <= 23 && 
               $minute >= 0 && $minute <= 59 && 
               $second >= 0 && $second <= 59;
    }
    
    /**
     * 验证银行卡号
     */
    public static function validateBankCard(string $cardNo): bool
    {
        // 银行卡号：13-19位数字
        return preg_match('/^\d{13,19}$/', $cardNo) === 1;
    }
    
    /**
     * 验证必填字段
     */
    public static function validateRequired(array $data, array $requiredFields): array
    {
        $errors = [];
        
        foreach ($requiredFields as $field) {
            if (!isset($data[$field]) || $data[$field] === '' || $data[$field] === null) {
                $errors[] = "字段 '{$field}' 是必填的";
            }
        }
        
        return $errors;
    }
    
    /**
     * 验证字段长度
     */
    public static function validateLength(array $data, array $lengthRules): array
    {
        $errors = [];
        
        foreach ($lengthRules as $field => $rule) {
            if (!isset($data[$field])) {
                continue;
            }
            
            $value = $data[$field];
            $length = is_string($value) ? mb_strlen($value, 'UTF-8') : strlen((string)$value);
            
            if (isset($rule['min']) && $length < $rule['min']) {
                $errors[] = "字段 '{$field}' 长度不能少于 {$rule['min']} 个字符";
            }
            
            if (isset($rule['max']) && $length > $rule['max']) {
                $errors[] = "字段 '{$field}' 长度不能超过 {$rule['max']} 个字符";
            }
            
            if (isset($rule['exact']) && $length !== $rule['exact']) {
                $errors[] = "字段 '{$field}' 长度必须为 {$rule['exact']} 个字符";
            }
        }
        
        return $errors;
    }
    
    /**
     * 验证字段范围
     */
    public static function validateRange(array $data, array $rangeRules): array
    {
        $errors = [];
        
        foreach ($rangeRules as $field => $rule) {
            if (!isset($data[$field])) {
                continue;
            }
            
            $value = $data[$field];
            
            if (isset($rule['min']) && $value < $rule['min']) {
                $errors[] = "字段 '{$field}' 值不能小于 {$rule['min']}";
            }
            
            if (isset($rule['max']) && $value > $rule['max']) {
                $errors[] = "字段 '{$field}' 值不能大于 {$rule['max']}";
            }
            
            if (isset($rule['in']) && !in_array($value, $rule['in'])) {
                $allowedValues = implode(', ', $rule['in']);
                $errors[] = "字段 '{$field}' 值必须是以下之一: {$allowedValues}";
            }
        }
        
        return $errors;
    }
    
    /**
     * 验证会员信息
     */
    public static function validateMemberInfo(array $memberInfo): array
    {
        $errors = [];
        
        // 必填字段验证
        $requiredFields = ['bizUserId', 'memberType', 'source'];
        $errors = array_merge($errors, self::validateRequired($memberInfo, $requiredFields));
        
        // 字段长度验证
        $lengthRules = [
            'bizUserId' => ['min' => 1, 'max' => 64],
            'memberType' => ['exact' => 1],
            'source' => ['exact' => 1]
        ];
        $errors = array_merge($errors, self::validateLength($memberInfo, $lengthRules));
        
        // 字段范围验证
        $rangeRules = [
            'memberType' => ['in' => ['1', '2', '3']], // 1:个人 2:企业 3:个体工商户
            'source' => ['in' => ['1', '2', '3']] // 1:APP 2:H5 3:小程序
        ];
        $errors = array_merge($errors, self::validateRange($memberInfo, $rangeRules));
        
        // 用户ID格式验证
        if (isset($memberInfo['bizUserId']) && !self::validateBizUserId($memberInfo['bizUserId'])) {
            $errors[] = "用户ID格式不正确";
        }
        
        // 扩展参数验证
        if (isset($memberInfo['extendParam'])) {
            $extendParam = json_decode($memberInfo['extendParam'], true);
            if (json_last_error() !== JSON_ERROR_NONE) {
                $errors[] = "扩展参数JSON格式不正确";
            } else {
                $extendErrors = self::validateExtendParam($extendParam, $memberInfo['memberType']);
                $errors = array_merge($errors, $extendErrors);
            }
        }
        
        return $errors;
    }
    
    /**
     * 验证扩展参数
     */
    private static function validateExtendParam(array $extendParam, string $memberType): array
    {
        $errors = [];
        
        if ($memberType === '1') { // 个人会员
            if (isset($extendParam['realName'])) {
                if (empty($extendParam['realName'])) {
                    $errors[] = "真实姓名不能为空";
                }
            }
            
            if (isset($extendParam['idCard'])) {
                if (!self::validateIdCard($extendParam['idCard'])) {
                    $errors[] = "身份证号格式不正确";
                }
            }
            
            if (isset($extendParam['phone'])) {
                if (!self::validatePhone($extendParam['phone'])) {
                    $errors[] = "手机号格式不正确";
                }
            }
        } elseif ($memberType === '2') { // 企业会员
            if (isset($extendParam['companyName'])) {
                if (empty($extendParam['companyName'])) {
                    $errors[] = "企业名称不能为空";
                }
            }
            
            if (isset($extendParam['businessLicense'])) {
                if (empty($extendParam['businessLicense'])) {
                    $errors[] = "营业执照号不能为空";
                }
            }
        }
        
        return $errors;
    }
    
    /**
     * 验证订单信息
     */
    public static function validateOrderInfo(array $orderInfo): array
    {
        $errors = [];
        
        // 必填字段验证
        $requiredFields = ['bizOrderNo', 'bizUserId', 'amount'];
        $errors = array_merge($errors, self::validateRequired($orderInfo, $requiredFields));
        
        // 字段长度验证
        $lengthRules = [
            'bizOrderNo' => ['min' => 6, 'max' => 64],
            'bizUserId' => ['min' => 1, 'max' => 64]
        ];
        $errors = array_merge($errors, self::validateLength($orderInfo, $lengthRules));
        
        // 订单号格式验证
        if (isset($orderInfo['bizOrderNo']) && !self::validateOrderNo($orderInfo['bizOrderNo'])) {
            $errors[] = "订单号格式不正确";
        }
        
        // 用户ID格式验证
        if (isset($orderInfo['bizUserId']) && !self::validateBizUserId($orderInfo['bizUserId'])) {
            $errors[] = "用户ID格式不正确";
        }
        
        // 金额验证
        if (isset($orderInfo['amount'])) {
            if (!is_numeric($orderInfo['amount'])) {
                $errors[] = "金额必须是数字";
            } elseif (!self::validateAmount((int)$orderInfo['amount'])) {
                $errors[] = "金额格式不正确";
            }
        }
        
        // 手续费验证
        if (isset($orderInfo['fee'])) {
            if (!is_numeric($orderInfo['fee'])) {
                $errors[] = "手续费必须是数字";
            } elseif ((int)$orderInfo['fee'] < 0) {
                $errors[] = "手续费不能为负数";
            }
        }
        
        return $errors;
    }
    
    /**
     * 验证并抛出异常
     */
    public static function validateAndThrow(array $data, array $rules, string $context = '数据验证'): void
    {
        $errors = [];
        
        // 必填字段验证
        if (isset($rules['required'])) {
            $errors = array_merge($errors, self::validateRequired($data, $rules['required']));
        }
        
        // 长度验证
        if (isset($rules['length'])) {
            $errors = array_merge($errors, self::validateLength($data, $rules['length']));
        }
        
        // 范围验证
        if (isset($rules['range'])) {
            $errors = array_merge($errors, self::validateRange($data, $rules['range']));
        }
        
        // 如果有错误，抛出异常
        if (!empty($errors)) {
            $errorMessage = $context . "失败:\n" . implode("\n", $errors);
            throw AllinpayException::validationError($errorMessage, [
                'data' => $data,
                'rules' => $rules,
                'errors' => $errors
            ]);
        }
    }
}
