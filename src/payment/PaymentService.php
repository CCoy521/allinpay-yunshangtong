<?php
namespace Allinpay\YunshangTong\Payment;

require_once __DIR__ . '/../../vendor/autoload.php';

use Allinpay\YunshangTong\Config\DemoConfig;
use Exception;
use Monolog\Logger;
use Monolog\Handler\StreamHandler;
use Allinpay\YunshangTong\Tx\TxClient;
use Allinpay\YunshangTong\Util\DemoSM4Utils;
use Allinpay\YunshangTong\Util\TxUtils;
use Allinpay\YunshangTong\Vo\BizParameter;

/**
 * 支付服务类
 * 
 * 负责处理所有支付相关的业务逻辑，包括：
 * - 转账交易处理
 * - 充值提现业务
 * - 支付账户管理
 * - 交易查询服务
 * - 协议支付管理
 * 
 * @author CodeBuddy
 * @version 1.0
 * @date 2025/01/13
 */
class PaymentService
{
    /** @var DemoConfig 配置对象 */
    private DemoConfig $config;
    
    /** @var TxClient 交易客户端 */
    private TxClient $txClient;
    
    /** @var Logger 日志记录器 */
    private Logger $logger;

    /**
     * 构造函数
     * 
     * @param DemoConfig $config 配置对象
     * @throws Exception
     */
    public function __construct(DemoConfig $config)
    {
        $this->config = $config;
        $this->txClient = new TxClient($config);
        
        $this->logger = new Logger('PaymentService');
        $this->logger->pushHandler(new StreamHandler('php://stderr', Logger::INFO));
        
        $this->logger->info('支付服务初始化成功');
    }

    // ==================== 转账交易处理 ====================

    /**
     * 转账申请（2084）
     * 对公账户转入打款支付账户号
     * 
     * @param array $transferData 转账数据
     * @return array 响应结果
     * @throws Exception
     */
    public function transfer(array $transferData): array
    {
        $this->validateRequired($transferData, ['signNum', 'inSignNum', 'orderAmount']);
        
        $bizParameter = new BizParameter();
        $bizParameter->addParam("reqTraceNum", TxUtils::genReqTraceNum() . "transfer");
        $bizParameter->addParam("signNum", $transferData['signNum']); // 转出方会员编号
        $bizParameter->addParam("inSignNum", $transferData['inSignNum']); // 转入方会员编号
        $bizParameter->addParam("orderAmount", $transferData['orderAmount']); // 转账金额（分）
        $bizParameter->addParam("respUrl", $transferData['respUrl'] ?? $this->config->getNotifyUrl());
        
        // 可选参数
        if (isset($transferData['acctType'])) {
            $bizParameter->addParam("acctType", $transferData['acctType']); // 1-簿记账户 2-支付账户
        }
        if (isset($transferData['inAcctType'])) {
            $bizParameter->addParam("inAcctType", $transferData['inAcctType']);
        }
        if (isset($transferData['acctNum'])) {
            $bizParameter->addParam("acctNum", $transferData['acctNum']);
        }
        if (isset($transferData['inAcctNum'])) {
            $bizParameter->addParam("inAcctNum", $transferData['inAcctNum']);
        }
        if (isset($transferData['summary'])) {
            $bizParameter->addParam("summary", $transferData['summary']);
        }
        if (isset($transferData['extendParams'])) {
            $bizParameter->addParam("extendParams", $transferData['extendParams']);
        }
        
        $this->logger->info('发起转账申请', [
            'from' => $transferData['signNum'], 
            'to' => $transferData['inSignNum'], 
            'amount' => $transferData['orderAmount']
        ]);
        
        return $this->sendTransactionRequest("2084", $bizParameter);
    }

    /**
     * 充值申请（2085）
     * 充值到会员账户
     * 
     * @param array $rechargeData 充值数据
     * @return array 响应结果
     * @throws Exception
     */
    public function recharge(array $rechargeData): array
    {
        $this->validateRequired($rechargeData, ['signNum', 'orderAmount']);
        
        $bizParameter = new BizParameter();
        $bizParameter->addParam("reqTraceNum", TxUtils::genReqTraceNum() . "recharge");
        $bizParameter->addParam("signNum", $rechargeData['signNum']);
        $bizParameter->addParam("orderAmount", $rechargeData['orderAmount']);
        $bizParameter->addParam("respUrl", $rechargeData['respUrl'] ?? $this->config->getNotifyUrl());
        
        // 可选参数
        if (isset($rechargeData['acctType'])) {
            $bizParameter->addParam("acctType", $rechargeData['acctType']);
        }
        if (isset($rechargeData['payType'])) {
            $bizParameter->addParam("payType", $rechargeData['payType']);
        }
        if (isset($rechargeData['summary'])) {
            $bizParameter->addParam("summary", $rechargeData['summary']);
        }
        
        $this->logger->info('发起充值申请', [
            'signNum' => $rechargeData['signNum'], 
            'amount' => $rechargeData['orderAmount']
        ]);
        
        return $this->sendTransactionRequest("2085", $bizParameter);
    }

    /**
     * 提现申请（2089）
     * 从会员账户提现到银行卡
     * 
     * @param array $withdrawData 提现数据
     * @return array 响应结果
     * @throws Exception
     */
    public function withdraw(array $withdrawData): array
    {
        $this->validateRequired($withdrawData, ['signNum', 'orderAmount']);
        
        $bizParameter = new BizParameter();
        $bizParameter->addParam("reqTraceNum", TxUtils::genReqTraceNum() . "withdraw");
        $bizParameter->addParam("signNum", $withdrawData['signNum']);
        $bizParameter->addParam("orderAmount", $withdrawData['orderAmount']);
        $bizParameter->addParam("respUrl", $withdrawData['respUrl'] ?? $this->config->getNotifyUrl());
        
        // 可选参数
        if (isset($withdrawData['acctType'])) {
            $bizParameter->addParam("acctType", $withdrawData['acctType']);
        }
        if (isset($withdrawData['acctNum'])) {
            $bizParameter->addParam("acctNum", $this->encryptSensitiveData($withdrawData['acctNum']));
        }
        if (isset($withdrawData['summary'])) {
            $bizParameter->addParam("summary", $withdrawData['summary']);
        }
        if (isset($withdrawData['fee'])) {
            $bizParameter->addParam("fee", $withdrawData['fee']);
        }
        
        $this->logger->info('发起提现申请', [
            'signNum' => $withdrawData['signNum'], 
            'amount' => $withdrawData['orderAmount']
        ]);
        
        return $this->sendTransactionRequest("2089", $bizParameter);
    }

    /**
     * 批量转账（2090）
     * 批量转账申请
     * 
     * @param array $batchTransferData 批量转账数据
     * @return array 响应结果
     * @throws Exception
     */
    public function batchTransfer(array $batchTransferData): array
    {
        $this->validateRequired($batchTransferData, ['signNum', 'transferList']);
        
        $bizParameter = new BizParameter();
        $bizParameter->addParam("reqTraceNum", TxUtils::genReqTraceNum() . "batch");
        $bizParameter->addParam("signNum", $batchTransferData['signNum']);
        $bizParameter->addListParam("transferList", $batchTransferData['transferList']);
        $bizParameter->addParam("respUrl", $batchTransferData['respUrl'] ?? $this->config->getNotifyUrl());
        
        // 可选参数
        if (isset($batchTransferData['totalAmount'])) {
            $bizParameter->addParam("totalAmount", $batchTransferData['totalAmount']);
        }
        if (isset($batchTransferData['totalCount'])) {
            $bizParameter->addParam("totalCount", $batchTransferData['totalCount']);
        }
        
        $this->logger->info('发起批量转账', [
            'signNum' => $batchTransferData['signNum'], 
            'count' => count($batchTransferData['transferList'])
        ]);
        
        return $this->sendTransactionRequest("2090", $bizParameter);
    }

    /**
     * 批量代付（2091）
     * 批量代付申请
     * 
     * @param array $batchPayData 批量代付数据
     * @return array 响应结果
     * @throws Exception
     */
    public function batchPay(array $batchPayData): array
    {
        $this->validateRequired($batchPayData, ['signNum', 'payList']);
        
        $bizParameter = new BizParameter();
        $bizParameter->addParam("reqTraceNum", TxUtils::genReqTraceNum() . "batchpay");
        $bizParameter->addParam("signNum", $batchPayData['signNum']);
        $bizParameter->addListParam("payList", $batchPayData['payList']);
        $bizParameter->addParam("respUrl", $batchPayData['respUrl'] ?? $this->config->getNotifyUrl());
        
        $this->logger->info('发起批量代付', [
            'signNum' => $batchPayData['signNum'], 
            'count' => count($batchPayData['payList'])
        ]);
        
        return $this->sendTransactionRequest("2091", $bizParameter);
    }

    // ==================== 交易查询服务 ====================

    /**
     * 交易查询（3001）
     * 查询交易结果
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
        
        $this->logger->info('查询交易结果', ['reqTraceNum' => $reqTraceNum, 'signNum' => $signNum]);
        return $this->sendQueryRequest("3001", $bizParameter);
    }

    /**
     * 余额查询（3002）
     * 查询会员账户余额
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
        
        $this->logger->info('查询账户余额', ['signNum' => $signNum, 'acctType' => $acctType]);
        return $this->sendQueryRequest("3002", $bizParameter);
    }

    /**
     * 交易明细查询（4003）
     * 查询交易明细
     * 
     * @param array $queryData 查询条件
     * @return array 响应结果
     * @throws Exception
     */
    public function queryTransactionDetail(array $queryData): array
    {
        $this->validateRequired($queryData, ['signNum']);
        
        $bizParameter = new BizParameter();
        $bizParameter->addParam("signNum", $queryData['signNum']);
        
        // 可选参数
        if (isset($queryData['startDate'])) {
            $bizParameter->addParam("startDate", $queryData['startDate']);
        }
        if (isset($queryData['endDate'])) {
            $bizParameter->addParam("endDate", $queryData['endDate']);
        }
        if (isset($queryData['pageNum'])) {
            $bizParameter->addParam("pageNum", $queryData['pageNum']);
        }
        if (isset($queryData['pageSize'])) {
            $bizParameter->addParam("pageSize", $queryData['pageSize']);
        }
        if (isset($queryData['transType'])) {
            $bizParameter->addParam("transType", $queryData['transType']);
        }
        
        $this->logger->info('查询交易明细', ['signNum' => $queryData['signNum']]);
        return $this->sendQueryRequest("4003", $bizParameter);
    }

    // ==================== 协议支付管理 ====================

    /**
     * 协议支付签约（1050）
     * 协议支付签约申请
     * 
     * @param array $signData 签约数据
     * @return array 响应结果
     * @throws Exception
     */
    public function agreementSign(array $signData): array
    {
        $this->validateRequired($signData, ['signNum', 'acctNum']);
        
        $bizParameter = new BizParameter();
        $bizParameter->addParam("reqTraceNum", TxUtils::genReqTraceNum());
        $bizParameter->addParam("signNum", $signData['signNum']);
        $bizParameter->addParam("acctNum", $this->encryptSensitiveData($signData['acctNum']));
        $bizParameter->addParam("bizOrderCode", (string)TxUtils::getMillisecond());
        
        // 可选参数
        if (isset($signData['agreementType'])) {
            $bizParameter->addParam("agreementType", $signData['agreementType']);
        }
        if (isset($signData['validDate'])) {
            $bizParameter->addParam("validDate", $signData['validDate']);
        }
        if (isset($signData['cvv2'])) {
            $bizParameter->addParam("cvv2", $signData['cvv2']);
        }
        if (isset($signData['phone'])) {
            $bizParameter->addParam("phone", $signData['phone']);
        }
        
        $this->logger->info('发起协议支付签约', ['signNum' => $signData['signNum']]);
        return $this->sendMemberRequest("1050", $bizParameter);
    }

    /**
     * 协议支付解约（1051）
     * 协议支付解约申请
     * 
     * @param array $unsignData 解约数据
     * @return array 响应结果
     * @throws Exception
     */
    public function agreementUnsign(array $unsignData): array
    {
        $this->validateRequired($unsignData, ['signNum', 'agreementNo']);
        
        $bizParameter = new BizParameter();
        $bizParameter->addParam("reqTraceNum", TxUtils::genReqTraceNum());
        $bizParameter->addParam("signNum", $unsignData['signNum']);
        $bizParameter->addParam("agreementNo", $unsignData['agreementNo']);
        $bizParameter->addParam("bizOrderCode", (string)TxUtils::getMillisecond());
        
        $this->logger->info('发起协议支付解约', ['signNum' => $unsignData['signNum'], 'agreementNo' => $unsignData['agreementNo']]);
        return $this->sendMemberRequest("1051", $bizParameter);
    }

    // ==================== 收银台支付 ====================

    /**
     * 收银台支付（3010）
     * 收银台支付申请
     * 
     * @param array $payData 支付数据
     * @return array 响应结果
     * @throws Exception
     */
    public function cashierPay(array $payData): array
    {
        $this->validateRequired($payData, ['signNum', 'orderAmount']);
        
        $bizParameter = new BizParameter();
        $bizParameter->addParam("reqTraceNum", TxUtils::genReqTraceNum() . "pay");
        $bizParameter->addParam("signNum", $payData['signNum']);
        $bizParameter->addParam("orderAmount", $payData['orderAmount']);
        $bizParameter->addParam("respUrl", $payData['respUrl'] ?? $this->config->getNotifyUrl());
        
        // 可选参数
        if (isset($payData['payType'])) {
            $bizParameter->addParam("payType", $payData['payType']);
        }
        if (isset($payData['frontUrl'])) {
            $bizParameter->addParam("frontUrl", $payData['frontUrl']);
        }
        if (isset($payData['orderInfo'])) {
            $bizParameter->addParam("orderInfo", $payData['orderInfo']);
        }
        if (isset($payData['validTime'])) {
            $bizParameter->addParam("validTime", $payData['validTime']);
        }
        if (isset($payData['extendParams'])) {
            $bizParameter->addParam("extendParams", $payData['extendParams']);
        }
        
        $this->logger->info('发起收银台支付', [
            'signNum' => $payData['signNum'], 
            'amount' => $payData['orderAmount']
        ]);
        
        return $this->sendTransactionRequest("3010", $bizParameter);
    }

    // ==================== 混合云支付 ====================

    /**
     * 混合云支付统一下单（1070）
     * 混合云支付统一下单
     * 
     * @param array $orderData 订单数据
     * @return array 响应结果
     * @throws Exception
     */
    public function hybridCloudUnifiedOrder(array $orderData): array
    {
        $this->validateRequired($orderData, ['signNum', 'orderAmount', 'payType']);
        
        $bizParameter = new BizParameter();
        $bizParameter->addParam("reqTraceNum", TxUtils::genReqTraceNum() . "hybrid");
        $bizParameter->addParam("signNum", $orderData['signNum']);
        $bizParameter->addParam("orderAmount", $orderData['orderAmount']);
        $bizParameter->addParam("payType", $orderData['payType']);
        $bizParameter->addParam("respUrl", $orderData['respUrl'] ?? $this->config->getNotifyUrl());
        
        // 可选参数
        if (isset($orderData['frontUrl'])) {
            $bizParameter->addParam("frontUrl", $orderData['frontUrl']);
        }
        if (isset($orderData['orderInfo'])) {
            $bizParameter->addParam("orderInfo", $orderData['orderInfo']);
        }
        if (isset($orderData['validTime'])) {
            $bizParameter->addParam("validTime", $orderData['validTime']);
        }
        if (isset($orderData['limitPay'])) {
            $bizParameter->addParam("limitPay", $orderData['limitPay']);
        }
        
        $this->logger->info('发起混合云支付统一下单', [
            'signNum' => $orderData['signNum'], 
            'amount' => $orderData['orderAmount'],
            'payType' => $orderData['payType']
        ]);
        
        return $this->sendTransactionRequest("1070", $bizParameter);
    }

    /**
     * 混合云支付查询（1072）
     * 混合云支付订单查询
     * 
     * @param string $reqTraceNum 商户订单号
     * @param string|null $signNum 会员编号
     * @return array 响应结果
     * @throws Exception
     */
    public function queryHybridCloudPay(string $reqTraceNum, ?string $signNum = null): array
    {
        $bizParameter = new BizParameter();
        $bizParameter->addParam("reqTraceNum", $reqTraceNum);
        
        if ($signNum) {
            $bizParameter->addParam("signNum", $signNum);
        }
        
        $this->logger->info('查询混合云支付订单', ['reqTraceNum' => $reqTraceNum, 'signNum' => $signNum]);
        return $this->sendQueryRequest("1072", $bizParameter);
    }

    // ==================== 私有方法 ====================

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
     * 处理响应结果
     * 
     * @param object $response 原始响应
     * @param string $transCode 交易码
     * @return array 处理后的响应
     */
    private function processResponse(object $response, string $transCode): array
    {
        $result = [
            'success' => $response->code === '0000',
            'code' => $response->code,
            'message' => $response->msg ?? '未知错误',
            'transCode' => $transCode,
            'bizData' => $response->bizData ?? null,
            'timestamp' => date('Y-m-d H:i:s')
        ];

        if (!$result['success']) {
            $this->logger->warning("支付业务处理失败", $result);
        } else {
            $this->logger->info("支付业务处理成功", ['transCode' => $transCode, 'code' => $response->code]);
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
     * 加密敏感数据
     * 
     * @param string $data 原始数据
     * @return string 加密后数据
     */
    private function encryptSensitiveData(string $data): string
    {
        return DemoSM4Utils::encryptEcb($this->config->getSecretKey(), $data);
    }
}