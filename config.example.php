<?php

/**
 * 通联支付配置文件示例
 * 复制此文件为 config.php 并修改相应的配置值
 */

return [
    // 环境配置
    'environment' => 'test', // 'test' 或 'production'
    
    // 测试环境配置
    'test' => [
        'url' => 'https://ibstest.allinpay.com/yst/yst-service-api',
        'appId' => '21803774682041868289',
        'spAppId' => '11879464722018709506',
        'secretKey' => '878427523d3525e070298d44481b8d2e',
        'privateKey' => 'MIGTAgEAMBMGByqGSM49AgEGCCqBHM9VAYItBHkwdwIBAQQgE12E+yZ6co3ZmT48Aslar9+TFu3/zrppSGTbq4nodYegCgYIKoEcz1UBgi2hRANCAATUryMfvHPyvqJe8UPzCNmswayea/u3X0TAAqbMExG2nZLywNwOG4ht33B0CHjobvX5efWtDxJl0A+SaH+pW1Ji',
        'publicKey' => 'MFkwEwYHKoZIzj0CAQYIKoEcz1UBgi0DQgAEu9LNkJlyLtjJxtQWIGlcZ/hyHt5eZ7LEH1nfOiK1H9HsE1cMPu5KK5jZVTtAyc7lPMXixUMirf6A3tMbuMbgqg=='
    ],
    
    // 生产环境配置
    'production' => [
        'url' => 'https://ibsapi.allinpay.com/yst-service-api',
        'appId' => '', // 请填写您的生产环境应用ID
        'spAppId' => '', // 请填写您的生产环境服务商应用ID
        'secretKey' => '', // 请填写您的生产环境密钥
        'privateKey' => '', // 请填写您的生产环境私钥
        'publicKey' => 'MFkwEwYHKoZIzj0CAQYIKoEcz1UBgi0DQgAE/VKHBem28IXD30yuZN1QcNgGE4gzqgd/eX1ZEouUleLNfrnQJkOs7LzAag3q10uaH/e9+5JyJDx3ULfKS4QZPw=='
    ],
    
    // 通用配置
    'common' => [
        'format' => 'json',
        'charset' => 'UTF-8',
        'signType' => 'SM3withSM2',
        'version' => '1.0',
        'timeout' => 30
    ],
    
    // 日志配置
    'logging' => [
        'enabled' => true,
        'level' => 'info', // debug, info, warning, error, critical
        'format' => 'json', // json 或 text
        'maxFiles' => 30,
        'maxSize' => '10MB',
        'path' => './logs',
        'filename' => 'allinpay_{date}.log',
        'includeContext' => true,
        'includeTrace' => false
    ],
    
    // HTTP客户端配置
    'http' => [
        'timeout' => 30,
        'verify' => false, // 测试环境可能需要关闭SSL验证
        'retry' => [
            'enabled' => true,
            'maxAttempts' => 3,
            'delay' => 1000 // 毫秒
        ]
    ],
    
    // 缓存配置
    'cache' => [
        'enabled' => true,
        'driver' => 'file', // file, redis, memory
        'path' => './cache',
        'ttl' => 3600 // 默认缓存时间（秒）
    ],
    
    // 安全配置
    'security' => [
        'signatureVerification' => true,
        'requestValidation' => true,
        'rateLimit' => [
            'enabled' => true,
            'maxRequests' => 100,
            'window' => 60 // 秒
        ]
    ]
];
