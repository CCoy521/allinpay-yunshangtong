<?php

namespace AllinpayYunshangtong\Utils;

use AllinpayYunshangtong\Config\LogConfig;

/**
 * 日志记录器
 * 提供统一的日志记录功能
 */
class Logger
{
    private LogConfig $config;
    private string $context;
    
    public function __construct(LogConfig $config, string $context = 'default')
    {
        $this->config = $config;
        $this->context = $context;
        
        // 确保日志目录存在
        $this->ensureLogDirectory();
    }
    
    /**
     * 记录调试日志
     */
    public function debug(string $message, array $context = []): void
    {
        $this->log(LogConfig::LEVEL_DEBUG, $message, $context);
    }
    
    /**
     * 记录信息日志
     */
    public function info(string $message, array $context = []): void
    {
        $this->log(LogConfig::LEVEL_INFO, $message, $context);
    }
    
    /**
     * 记录警告日志
     */
    public function warning(string $message, array $context = []): void
    {
        $this->log(LogConfig::LEVEL_WARNING, $message, $context);
    }
    
    /**
     * 记录错误日志
     */
    public function error(string $message, array $context = []): void
    {
        $this->log(LogConfig::LEVEL_ERROR, $message, $context);
    }
    
    /**
     * 记录严重错误日志
     */
    public function critical(string $message, array $context = []): void
    {
        $this->log(LogConfig::LEVEL_CRITICAL, $message, $context);
    }
    
    /**
     * 记录日志的核心方法
     */
    private function log(string $level, string $message, array $context = []): void
    {
        if (!$this->config->shouldLog($level)) {
            return;
        }
        
        $logEntry = $this->formatLogEntry($level, $message, $context);
        $this->writeLog($logEntry);
    }
    
    /**
     * 格式化日志条目
     */
    private function formatLogEntry(string $level, string $message, array $context = []): string
    {
        $timestamp = date('Y-m-d H:i:s');
        $logData = [
            'timestamp' => $timestamp,
            'level' => strtoupper($level),
            'context' => $this->context,
            'message' => $message
        ];
        
        if ($this->config->includeContext() && !empty($context)) {
            $logData['data'] = $context;
        }
        
        if ($this->config->includeTrace()) {
            $trace = debug_backtrace(DEBUG_BACKTRACE_IGNORE_ARGS, 3);
            $logData['trace'] = array_map(function($item) {
                return [
                    'file' => $item['file'] ?? '',
                    'line' => $item['line'] ?? '',
                    'function' => $item['function'] ?? '',
                    'class' => $item['class'] ?? ''
                ];
            }, $trace);
        }
        
        if ($this->config->getFormat() === LogConfig::FORMAT_JSON) {
            return json_encode($logData, JSON_UNESCAPED_UNICODE) . PHP_EOL;
        } else {
            return $this->formatTextLog($logData);
        }
    }
    
    /**
     * 格式化文本日志
     */
    private function formatTextLog(array $logData): string
    {
        $text = "[{$logData['timestamp']}] [{$logData['level']}] [{$logData['context']}] {$logData['message']}";
        
        if (isset($logData['data']) && !empty($logData['data'])) {
            $text .= " | Data: " . json_encode($logData['data'], JSON_UNESCAPED_UNICODE);
        }
        
        if (isset($logData['trace']) && !empty($logData['trace'])) {
            $text .= " | Trace: " . json_encode($logData['trace'], JSON_UNESCAPED_UNICODE);
        }
        
        return $text . PHP_EOL;
    }
    
    /**
     * 写入日志文件
     */
    private function writeLog(string $logEntry): void
    {
        $logFile = $this->config->getFullLogPath();
        
        // 检查文件大小限制
        if (file_exists($logFile) && $this->shouldRotateLog($logFile)) {
            $this->rotateLog($logFile);
        }
        
        // 写入日志
        if (file_put_contents($logFile, $logEntry, FILE_APPEND | LOCK_EX) === false) {
            error_log("Failed to write to log file: {$logFile}");
        }
    }
    
    /**
     * 检查是否需要轮转日志
     */
    private function shouldRotateLog(string $logFile): bool
    {
        $maxSize = $this->parseSize($this->config->getMaxSize());
        $currentSize = filesize($logFile);
        
        return $currentSize > $maxSize;
    }
    
    /**
     * 轮转日志文件
     */
    private function rotateLog(string $logFile): void
    {
        $dir = dirname($logFile);
        $filename = basename($logFile);
        $extension = pathinfo($filename, PATHINFO_EXTENSION);
        $name = pathinfo($filename, PATHINFO_FILENAME);
        
        // 移除日期部分
        $name = preg_replace('/_\d{4}-\d{2}-\d{2}$/', '', $name);
        
        // 创建新的日志文件名
        $newLogFile = $dir . DIRECTORY_SEPARATOR . $name . '_' . date('Y-m-d_H-i-s') . '.' . $extension;
        
        // 重命名当前日志文件
        if (rename($logFile, $newLogFile)) {
            // 清理旧日志文件
            $this->cleanOldLogs($dir, $name, $extension);
        }
    }
    
    /**
     * 清理旧的日志文件
     */
    private function cleanOldLogs(string $dir, string $name, string $extension): void
    {
        $pattern = $dir . DIRECTORY_SEPARATOR . $name . '_*.' . $extension;
        $files = glob($pattern);
        
        if (count($files) > $this->config->getMaxFiles()) {
            // 按修改时间排序
            usort($files, function($a, $b) {
                return filemtime($a) - filemtime($b);
            });
            
            // 删除最旧的文件
            $filesToDelete = array_slice($files, 0, count($files) - $this->config->getMaxFiles());
            foreach ($filesToDelete as $file) {
                unlink($file);
            }
        }
    }
    
    /**
     * 解析文件大小字符串
     */
    private function parseSize(string $size): int
    {
        $units = [
            'B' => 1,
            'KB' => 1024,
            'MB' => 1024 * 1024,
            'GB' => 1024 * 1024 * 1024
        ];
        
        $size = strtoupper(trim($size));
        
        foreach ($units as $unit => $multiplier) {
            if (str_ends_with($size, $unit)) {
                $value = (int) str_replace($unit, '', $size);
                return $value * $multiplier;
            }
        }
        
        return (int) $size;
    }
    
    /**
     * 确保日志目录存在
     */
    private function ensureLogDirectory(): void
    {
        $logPath = $this->config->getLogPath();
        
        if (!is_dir($logPath)) {
            if (!mkdir($logPath, 0755, true)) {
                error_log("Failed to create log directory: {$logPath}");
            }
        }
    }
    
    /**
     * 设置上下文
     */
    public function setContext(string $context): self
    {
        $this->context = $context;
        return $this;
    }
    
    /**
     * 获取当前上下文
     */
    public function getContext(): string
    {
        return $this->context;
    }
}
