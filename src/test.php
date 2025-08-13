<?php
namespace src;

require_once __DIR__ . '/../vendor/autoload.php';

use config\DemoConfig;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\GuzzleException;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Rtgm\sm\RtSm2;
use TxClient;
use util\DemoSM2Utils;
use util\DemoSM4Utils;
use util\TxUtils;
use vo\BizParameter;
use vo\TxRequest;
use vo\TxResponse;

/**
 * 通联支付云商通SDK统一入口类
 * 
 * 提供完整的云商通业务功能，包括：
 * - 会员管理（注册、实名认证、绑卡等）
 * - 交易处理（转账、充值、提现等）
 * - 查询服务（交易查询、余额查询等）
 * - 文件服务（上传、下载）
 * - 安全加密（SM2签名、SM4加解密）
 * 
 * @author CodeBuddy
 * @version 2.0
 * @date 2025/01/13
 */
class AllinpayYST
{
    /** @var DemoConfig 配置对象 */
    private DemoConfig $config;
    
    /** @var TxClient 交易客户端 */
    private TxClient $txClient;
    
    /** @var Logger 日志记录器 */
    private Logger $logger;
    
    /** @var RtSm2 SM2加密实例 */
    private RtSm2 $sm2;
    
    /** @var string 私钥 */
    private string $privateKey;
    
    /** @var string 通联公钥 */
    private string $publicKey;
    
    /** @var array 错误码映射 */
    private const ERROR_CODES = [
        '0000' => '成功',
        '1001' => '参数错误',
        '1002' => '签名验证失败',
        '1003' => '系统异常',
        '2001' => '会员不存在',
        '2002' => '账户余额不足',
        '3001' => '交易失败',
        '3002' => '交易超时',
    ];

    /**
     * 构造函数
     * 
     * @param DemoConfig|null $config 配置对象，为空时使用默认配置
     * @throws Exception
     */
    public function __construct(?DemoConfig $config = null)
    {
        try {
            // 初始化配置
            $this->config = $config ?? new DemoConfig();
            
            // 初始化日志
            $this->logger = new Logger('AllinpayYST');
            $this->logger->pushHandler(new StreamHandler('php://stderr', Logger::INFO));
            
            // 初始化SM2加密
            $this->sm2 = new RtSm2('base64');
            
            // 初始化密钥
            $this->privateKey = DemoSM2Utils::privKeySM2FromBase64Str($this->config->getPrivateKeyStr());
            $this->publicKey = DemoSM2Utils::pubKeySM2FromBase64Str($this->config->getAllinpayPublicKeyStr());
            
            // 初始化交易客户端
            $this->txClient = new TxClient($this->config);
            
            $this->logger->info('AllinpayYST SDK 初始化成功');
            
        } catch (Exception $e) {
            $this->logger->error('AllinpayYST SDK 初始化失败: ' . $e->getMessage());
            throw new Exception('SDK初始化失败: ' . $e->getMessage(), 0, $e);
        }
    }

    // ==================== 会员管理模块 ====================

    /**
     * 个人会员实名认证及绑卡
     * 
     * @param array $memberData 会员数据
     * @return array 响应结果
     * @throws Exception
     */
    public function memberPersonalAuth(array $memberData): array
    {
        $this->validateRequired($memberData, ['signNum', 'name', 'cerNum', 'acctNum', 'phone']);
        
        $bizParameter = new BizParameter();
        $bizParameter->addParam("reqTraceNum", $this->generateTraceNum());
        $bizParameter->addParam("signNum", $memberData['signNum']);
        $bizParameter->addParam("memberRole", $memberData['memberRole'] ?? '分销方');
        $bizParameter->addParam("cerType", $memberData['cerType'] ?? '1');
        $bizParameter->addParam("cerNum", $this->encryptSensitiveData($memberData['cerNum']));
        $bizParameter->addParam("name", $memberData['name']);
        $bizParameter->addParam("acctNum", $this->encryptSensitiveData($memberData['acctNum']));
        $bizParameter->addParam("phone", $memberData['phone']);
        $bizParameter->addParam("bindType", $memberData['bindType'] ?? '8');
        $bizParameter->addParam("bizOrderCode", (string)TxUtils::getMillisecond());
        
        // 可选参数
        if (isset($memberData['validDate'])) {
            $bizParameter->addParam("validDate", $memberData['validDate']);
        }
        if (isset($memberData['cvv2'])) {
            $bizParameter->addParam("cvv2", $memberData['cvv2']);
        }
        
        return $this->sendMemberRequest("1010", $bizParameter);
    }

    /**
     * 企业会员注册
     * 
     * @param array $companyData 企业数据
     * @return array 响应结果
     * @throws Exception
     */
    public function memberCompanyRegister(array $companyData): array
    {
        $this->validateRequired($companyData, ['signNum', 'companyName', 'licenseNo', 'legalName', 'legalCerNum']);
        
        $bizParameter = new BizParameter();
        $bizParameter->addParam("reqTraceNum", $this->generateTraceNum());
        $bizParameter->addParam("signNum", $companyData['signNum']);
        $bizParameter->addParam("memberRole", $companyData['memberRole'] ?? '分销方');
        $bizParameter->addParam("companyName", $companyData['companyName']);
        $bizParameter->addParam("licenseNo", $companyData['licenseNo']);
        $bizParameter->addParam("legalName", $companyData['legalName']);
        $bizParameter->addParam("legalCerType", $companyData['legalCerType'] ?? '1');
        $bizParameter->addParam("legalCerNum", $this->encryptSensitiveData($companyData['legalCerNum']));
        $bizParameter->addParam("bizOrderCode", (string)TxUtils::getMillisecond());
        
        return $this->sendMemberRequest("1020", $bizParameter);
    }

    /**
     * 会员绑卡
     * 
     * @param array $bindData 绑卡数据
     * @return array 响应结果
     * @throws Exception
     */
    public function memberBindCard(array $bindData): array
    {
        $this->validateRequired($bindData, ['signNum', 'acctNum']);
        
        $bizParameter = new BizParameter();
        $bizParameter->addParam("reqTraceNum", $this->generateTraceNum());
        $bizParameter->addParam("signNum", $bindData['signNum']);
        $bizParameter->addParam("acctNum", $this->encryptSensitiveData($bindData['acctNum']));
        $bizParameter->addParam("bindType", $bindData['bindType'] ?? '8');
        $bizParameter->addParam("bizOrderCode", (string)TxUtils::getMillisecond());
        
        return $this->sendMemberRequest("1011", $bizParameter);
    }

    // ==================== 交易处理模块 ====================

    /**
     * 转账申请
     * 
     * @param array $transferData 转账数据
     * @return array 响应结果
     * @throws Exception
     */
    public function transfer(array $transferData): array
    {
        $this->validateRequired($transferData, ['signNum', 'inSignNum', 'orderAmount']);
        
        $bizParameter = new BizParameter();
        $bizParameter->addParam("reqTraceNum", $this->generateTraceNum() . "transfer");
        $bizParameter->addParam("signNum", $transferData['signNum']);
        $bizParameter->addParam("inSignNum", $transferData['inSignNum']);
        $bizParameter->addParam("orderAmount", $transferData['orderAmount']);
        $bizParameter->addParam("respUrl", $transferData['respUrl'] ?? $this->config->getNotifyUrl());
        
        // 可选参数
        if (isset($transferData['acctType'])) {
            $bizParameter->addParam("acctType", $transferData['acctType']);
        }
        if (isset($transferData['summary'])) {
            $bizParameter->addParam("summary", $transferData['summary']);
        }
        if (isset($transferData['extendParams'])) {
            $bizParameter->addParam("extendParams", $transferData['extendParams']);
        }
        
        return $this->sendTransactionRequest("2084", $bizParameter);
    }

    /**
     * 充值申请
     * 
     * @param array $rechargeData 充值数据
     * @return array 响应结果
     * @throws Exception
     */
    public function recharge(array $rechargeData): array
    {
        $this->validateRequired($rechargeData, ['signNum', 'orderAmount']);
        
        $bizParameter = new BizParameter();
        $bizParameter->addParam("reqTraceNum", $this->generateTraceNum() . "recharge");
        $bizParameter->addParam("signNum", $rechargeData['signNum']);
        $bizParameter->addParam("orderAmount", $rechargeData['orderAmount']);
        $bizParameter->addParam("respUrl", $rechargeData['respUrl'] ?? $this->config->getNotifyUrl());
        
        return $this->sendTransactionRequest("2085", $bizParameter);
    }

    /**
     * 提现申请
     * 
     * @param array $withdrawData 提现数据
     * @return array 响应结果
     * @throws Exception
     */
    public function withdraw(array $withdrawData): array
    {
        $this->validateRequired($withdrawData, ['signNum', 'orderAmount']);
        
        $bizParameter = new BizParameter();
        $bizParameter->addParam("reqTraceNum", $this->generateTraceNum() . "withdraw");
        $bizParameter->addParam("signNum", $withdrawData['signNum']);
        $bizParameter->addParam("orderAmount", $withdrawData['orderAmount']);
        $bizParameter->addParam("respUrl", $withdrawData['respUrl'] ?? $this->config->getNotifyUrl());
        
        return $this->sendTransactionRequest("2089", $bizParameter);
    }

    // ==================== 查询服务模块 ====================

    /**
     * 交易查询
     * 
     * @param string $reqTraceNum 商户订单号
     * @param string|null $signNum 会员编号
     * @return array 响应结果
     * @throws Exception
     */
    public function queryTransaction(string $reqTraceNum, ?string $signNum = null): array
    {
        $bizParameter = new BizParameter();
        $bizParameter->addParam("reqTraceNum", $reqTraceNum);
        
        if ($signNum) {
            $bizParameter->addParam("signNum", $signNum);
        }
        
        return $this->sendQueryRequest("3001", $bizParameter);
    }

    /**
     * 余额查询
     * 
     * @param string $signNum 会员编号
     * @param int $acctType 账户类型 1-簿记账户 2-支付账户
     * @return array 响应结果
     * @throws Exception
     */
    public function queryBalance(string $signNum, int $acctType = 1): array
    {
        $bizParameter = new BizParameter();
        $bizParameter->addParam("signNum", $signNum);
        $bizParameter->addParam("acctType", $acctType);
        
        return $this->sendQueryRequest("3002", $bizParameter);
    }

    // ==================== 文件服务模块 ====================

    /**
     * 文件上传
     * 
     * @param string $filePath 文件路径
     * @param string $fileType 文件类型
     * @return array 响应结果
     * @throws Exception
     */
    public function uploadFile(string $filePath, string $fileType = '0'): array
    {
        if (!file_exists($filePath)) {
            throw new Exception("文件不存在: {$filePath}");
        }
        
        try {
            $timestamp = date('Y-m-d H:i:s');
            $md5 = $this->getFileMD5($filePath);
            
            // 构建签名源串
            $srcMsg = $this->config->getSpAppId() . $this->config->getAppId() . $fileType . $md5 . $timestamp;
            $sign = $this->sm2->doSign($srcMsg, $this->privateKey);
            
            $client = new Client();
            $multipart = [
                ['name' => 'file', 'contents' => fopen($filePath, 'r'), 'filename' => basename($filePath)],
                ['name' => 'appId', 'contents' => $this->config->getAppId()],
                ['name' => 'fileType', 'contents' => $fileType],
                ['name' => 'timestamp', 'contents' => $timestamp],
                ['name' => 'md5', 'contents' => $md5],
                ['name' => 'sign', 'contents' => $sign]
            ];
            
            $response = $client->post($this->config->getFileUploadUrl(), ['multipart' => $multipart]);
            $result = json_decode($response->getBody()->getContents(), true);
            
            $this->logger->info('文件上传成功', ['filePath' => $filePath, 'result' => $result]);
            return $result;
            
        } catch (Exception $e) {
            $this->logger->error('文件上传失败', ['filePath' => $filePath, 'error' => $e->getMessage()]);
            throw new Exception('文件上传失败: ' . $e->getMessage(), 0, $e);
        }
    }

    /**
     * 文件下载
     * 
     * @param string $fileId 文件ID
     * @param string $savePath 保存路径
     * @return bool 是否成功
     * @throws Exception
     */
    public function downloadFile(string $fileId, string $savePath): bool
    {
        try {
            $client = new Client();
            $response = $client->get($this->config->getFileDownloadUrl(), [
                'query' => ['fileId' => $fileId]
            ]);
            
            $content = $response->getBody()->getContents();
            $result = file_put_contents($savePath, $content);
            
            if ($result === false) {
                throw new Exception('文件保存失败');
            }
            
            $this->logger->info('文件下载成功', ['fileId' => $fileId, 'savePath' => $savePath]);
            return true;
            
        } catch (Exception $e) {
            $this->logger->error('文件下载失败', ['fileId' => $fileId, 'error' => $e->getMessage()]);
            throw new Exception('文件下载失败: ' . $e->getMessage(), 0, $e);
        }
    }

    // ==================== 工具方法 ====================

    /**
     * 生成交易流水号
     * 
     * @return string 流水号
     */
    public function generateTraceNum(): string
    {
        return TxUtils::genReqTraceNum();
    }

    /**
     * 加密敏感数据
     * 
     * @param string $data 原始数据
     * @return string 加密后数据
     */
    public function encryptSensitiveData(string $data): string
    {
        return DemoSM4Utils::encryptEcb($this->config->getSecretKey(), $data);
    }

    /**
     * 解密敏感数据
     * 
     * @param string $encryptedData 加密数据
     * @return string 解密后数据
     */
    public function decryptSensitiveData(string $encryptedData): string
    {
        return DemoSM4Utils::decryptEcb($this->config->getSecretKey(), $encryptedData);
    }

    /**
     * 设置通知地址
     * 
     * @param string $notifyUrl 通知地址
     * @return self
     */
    public function setNotifyUrl(string $notifyUrl): self
    {
        $this->config->setNotifyUrl($notifyUrl);
        return $this;
    }

    /**
     * 获取配置对象
     * 
     * @return DemoConfig
     */
    public function getConfig(): DemoConfig
    {
        return $this->config;
    }

    // ==================== 私有方法 ====================

    /**
     * 发送会员请求
     * 
     * @param string $transCode 交易码
     * @param BizParameter $bizParameter 业务参数
     * @return array 响应结果
     * @throws Exception
     */
    private function sendMemberRequest(string $transCode, BizParameter $bizParameter): array
    {
        try {
            $response = $this->txClient->sendRequest($transCode, $bizParameter, $this->config->getMemberUrl());
            return $this->processResponse($response, $transCode);
        } catch (Exception $e) {
            $this->logger->error("会员请求失败", ['transCode' => $transCode, 'error' => $e->getMessage()]);
            throw new Exception("会员请求失败: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * 发送交易请求
     * 
     * @param string $transCode 交易码
     * @param BizParameter $bizParameter 业务参数
     * @return array 响应结果
     * @throws Exception
     */
    private function sendTransactionRequest(string $transCode, BizParameter $bizParameter): array
    {
        try {
            $response = $this->txClient->sendRequest($transCode, $bizParameter, $this->config->getUrl());
            return $this->processResponse($response, $transCode);
        } catch (Exception $e) {
            $this->logger->error("交易请求失败", ['transCode' => $transCode, 'error' => $e->getMessage()]);
            throw new Exception("交易请求失败: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * 发送查询请求
     * 
     * @param string $transCode 交易码
     * @param BizParameter $bizParameter 业务参数
     * @return array 响应结果
     * @throws Exception
     */
    private function sendQueryRequest(string $transCode, BizParameter $bizParameter): array
    {
        try {
            $response = $this->txClient->sendRequest($transCode, $bizParameter, $this->config->getQueryUrl());
            return $this->processResponse($response, $transCode);
        } catch (Exception $e) {
            $this->logger->error("查询请求失败", ['transCode' => $transCode, 'error' => $e->getMessage()]);
            throw new Exception("查询请求失败: " . $e->getMessage(), 0, $e);
        }
    }

    /**
     * 处理响应结果
     * 
     * @param object $response 原始响应
     * @param string $transCode 交易码
     * @return array 处理后的响应
     * @throws Exception
     */
    private function processResponse(object $response, string $transCode): array
    {
        $result = [
            'success' => $response->code === '0000',
            'code' => $response->code,
            'message' => $this->getErrorMessage($response->code),
            'transCode' => $transCode,
            'bizData' => $response->bizData ?? null,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        if (!$result['success']) {
            $this->logger->warning("业务处理失败", $result);
        } else {
            $this->logger->info("业务处理成功", ['transCode' => $transCode, 'code' => $response->code]);
        }

        return $result;
    }

    /**
     * 验证必需参数
     * 
     * @param array $data 数据
     * @param array $required 必需字段
     * @throws Exception
     */
    private function validateRequired(array $data, array $required): void
    {
        foreach ($required as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                throw new Exception("缺少必需参数: {$field}");
            }
        }
    }

    /**
     * 获取文件MD5值
     * 
     * @param string $filePath 文件路径
     * @return string Base64编码的MD5值
     */
    private function getFileMD5(string $filePath): string
    {
        $md5 = md5_file($filePath);
        return base64_encode(hex2bin($md5));
    }

    /**
     * 获取错误信息
     * 
     * @param string $code 错误码
     * @return string 错误信息
     */
    private function getErrorMessage(string $code): string
    {
        return self::ERROR_CODES[$code] ?? "未知错误码: {$code}";
    }
}
