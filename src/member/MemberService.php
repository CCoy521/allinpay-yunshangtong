<?php
namespace Allinpay\YunshangTong\Member;

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
 * 会员服务类
 * 
 * 负责处理所有会员相关的业务逻辑，包括：
 * - 个人用户开户流程
 * - 企业用户开户流程
 * - 会员信息管理
 * - 会员状态查询
 * 
 * @author CodeBuddy
 * @version 1.0
 * @date 2025/01/13
 */
class MemberService
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
        
        $this->logger = new Logger('MemberService');
        $this->logger->pushHandler(new StreamHandler('php://stderr', Logger::INFO));
        
        $this->logger->info('会员服务初始化成功');
    }

    // ==================== 个人用户开户流程 ====================

    /**
     * 个人会员注册（1010）
     * 个人用户起草开通通联记账户申请
     * 
     * @param array $personalData 个人用户数据
     * @return array 响应结果
     * @throws Exception
     */
    public function registerPersonalMember(array $personalData): array
    {
        $this->validatePersonalMemberData($personalData);
        
        $bizParameter = new BizParameter();
        $bizParameter->addParam("reqTraceNum", TxUtils::genReqTraceNum());
        $bizParameter->addParam("signNum", $personalData['signNum']);
        $bizParameter->addParam("memberRole", $personalData['memberRole'] ?? '分销方');
        $bizParameter->addParam("cerType", $personalData['cerType'] ?? '1'); // 1-身份证
        $bizParameter->addParam("cerNum", $this->encryptSensitiveData($personalData['cerNum']));
        $bizParameter->addParam("name", $personalData['name']);
        $bizParameter->addParam("acctNum", $this->encryptSensitiveData($personalData['acctNum']));
        $bizParameter->addParam("phone", $personalData['phone']);
        $bizParameter->addParam("bindType", $personalData['bindType'] ?? '8'); // 8-银行卡四要素
        $bizParameter->addParam("bizOrderCode", (string)TxUtils::getMillisecond());
        
        // 可选参数
        if (isset($personalData['validDate'])) {
            $bizParameter->addParam("validDate", $personalData['validDate']);
        }
        if (isset($personalData['cvv2'])) {
            $bizParameter->addParam("cvv2", $personalData['cvv2']);
        }
        if (isset($personalData['email'])) {
            $bizParameter->addParam("email", $personalData['email']);
        }
        
        $this->logger->info('发起个人会员注册', ['signNum' => $personalData['signNum']]);
        return $this->sendMemberRequest("1010", $bizParameter);
    }

    /**
     * 个人会员绑卡（1011）
     * 个人会员实名绑卡申请
     * 
     * @param array $bindData 绑卡数据
     * @return array 响应结果
     * @throws Exception
     */
    public function bindPersonalCard(array $bindData): array
    {
        $this->validateRequired($bindData, ['signNum', 'acctNum']);
        
        $bizParameter = new BizParameter();
        $bizParameter->addParam("reqTraceNum", TxUtils::genReqTraceNum());
        $bizParameter->addParam("signNum", $bindData['signNum']);
        $bizParameter->addParam("acctNum", $this->encryptSensitiveData($bindData['acctNum']));
        $bizParameter->addParam("bindType", $bindData['bindType'] ?? '8');
        $bizParameter->addParam("bizOrderCode", (string)TxUtils::getMillisecond());
        
        // 可选参数
        if (isset($bindData['validDate'])) {
            $bizParameter->addParam("validDate", $bindData['validDate']);
        }
        if (isset($bindData['cvv2'])) {
            $bizParameter->addParam("cvv2", $bindData['cvv2']);
        }
        
        $this->logger->info('发起个人会员绑卡', ['signNum' => $bindData['signNum']]);
        return $this->sendMemberRequest("1011", $bizParameter);
    }

    // ==================== 企业用户开户流程 ====================

    /**
     * 企业会员注册（1020）
     * 企业用户起草开通通联记账户申请
     * 
     * @param array $companyData 企业数据
     * @return array 响应结果
     * @throws Exception
     */
    public function registerCompanyMember(array $companyData): array
    {
        $this->validateCompanyMemberData($companyData);
        
        $bizParameter = new BizParameter();
        $bizParameter->addParam("reqTraceNum", TxUtils::genReqTraceNum());
        $bizParameter->addParam("signNum", $companyData['signNum']);
        $bizParameter->addParam("memberRole", $companyData['memberRole'] ?? '分销方');
        $bizParameter->addParam("companyName", $companyData['companyName']);
        $bizParameter->addParam("licenseNo", $companyData['licenseNo']);
        $bizParameter->addParam("legalName", $companyData['legalName']);
        $bizParameter->addParam("legalCerType", $companyData['legalCerType'] ?? '1');
        $bizParameter->addParam("legalCerNum", $this->encryptSensitiveData($companyData['legalCerNum']));
        $bizParameter->addParam("bizOrderCode", (string)TxUtils::getMillisecond());
        
        // 可选参数
        if (isset($companyData['companyType'])) {
            $bizParameter->addParam("companyType", $companyData['companyType']);
        }
        if (isset($companyData['businessScope'])) {
            $bizParameter->addParam("businessScope", $companyData['businessScope']);
        }
        if (isset($companyData['registeredCapital'])) {
            $bizParameter->addParam("registeredCapital", $companyData['registeredCapital']);
        }
        if (isset($companyData['establishDate'])) {
            $bizParameter->addParam("establishDate", $companyData['establishDate']);
        }
        
        $this->logger->info('发起企业会员注册', ['signNum' => $companyData['signNum'], 'companyName' => $companyData['companyName']]);
        return $this->sendMemberRequest("1020", $bizParameter);
    }

    /**
     * 获取会员开户结果（1022）
     * 企业会员开账户结果
     * 
     * @param string $signNum 会员编号
     * @param string $bizOrderCode 业务订单号
     * @return array 响应结果
     * @throws Exception
     */
    public function getCompanyAccountResult(string $signNum, string $bizOrderCode): array
    {
        $bizParameter = new BizParameter();
        $bizParameter->addParam("signNum", $signNum);
        $bizParameter->addParam("bizOrderCode", $bizOrderCode);
        
        $this->logger->info('查询企业会员开户结果', ['signNum' => $signNum, 'bizOrderCode' => $bizOrderCode]);
        return $this->sendMemberRequest("1022", $bizParameter);
    }

    /**
     * 企业会员开通支付账户（1023）
     * 企业会员开通支付账户申请
     * 
     * @param array $paymentAccountData 支付账户数据
     * @return array 响应结果
     * @throws Exception
     */
    public function openCompanyPaymentAccount(array $paymentAccountData): array
    {
        $this->validateRequired($paymentAccountData, ['signNum']);
        
        $bizParameter = new BizParameter();
        $bizParameter->addParam("reqTraceNum", TxUtils::genReqTraceNum());
        $bizParameter->addParam("signNum", $paymentAccountData['signNum']);
        $bizParameter->addParam("bizOrderCode", (string)TxUtils::getMillisecond());
        
        // 可选参数
        if (isset($paymentAccountData['acctType'])) {
            $bizParameter->addParam("acctType", $paymentAccountData['acctType']);
        }
        if (isset($paymentAccountData['summary'])) {
            $bizParameter->addParam("summary", $paymentAccountData['summary']);
        }
        
        $this->logger->info('发起企业支付账户开通', ['signNum' => $paymentAccountData['signNum']]);
        return $this->sendMemberRequest("1023", $bizParameter);
    }

    /**
     * 获取会员开户受理状态（1024）
     * 获取会员开账户受理状态
     * 
     * @param string $signNum 会员编号
     * @param string $bizOrderCode 业务订单号
     * @return array 响应结果
     * @throws Exception
     */
    public function getMemberAccountStatus(string $signNum, string $bizOrderCode): array
    {
        $bizParameter = new BizParameter();
        $bizParameter->addParam("signNum", $signNum);
        $bizParameter->addParam("bizOrderCode", $bizOrderCode);
        
        $this->logger->info('查询会员开户受理状态', ['signNum' => $signNum, 'bizOrderCode' => $bizOrderCode]);
        return $this->sendMemberRequest("1024", $bizParameter);
    }

    // ==================== 会员信息管理 ====================

    /**
     * 获取会员支付账户号（1025）
     * 获取会员支付账户号
     * 
     * @param string $signNum 会员编号
     * @return array 响应结果
     * @throws Exception
     */
    public function getMemberPaymentAccountNum(string $signNum): array
    {
        $bizParameter = new BizParameter();
        $bizParameter->addParam("signNum", $signNum);
        
        $this->logger->info('查询会员支付账户号', ['signNum' => $signNum]);
        return $this->sendMemberRequest("1025", $bizParameter);
    }

    /**
     * 支付账户开户审核详情（1026）
     * 支付账户开户审核详情
     * 
     * @param string $signNum 会员编号
     * @param string $bizOrderCode 业务订单号
     * @return array 响应结果
     * @throws Exception
     */
    public function getPaymentAccountAuditDetail(string $signNum, string $bizOrderCode): array
    {
        $bizParameter = new BizParameter();
        $bizParameter->addParam("signNum", $signNum);
        $bizParameter->addParam("bizOrderCode", $bizOrderCode);
        
        $this->logger->info('查询支付账户开户审核详情', ['signNum' => $signNum, 'bizOrderCode' => $bizOrderCode]);
        return $this->sendMemberRequest("1026", $bizParameter);
    }

    /**
     * 会员信息查询（1027）
     * 会员信息查询
     * 
     * @param string $signNum 会员编号
     * @return array 响应结果
     * @throws Exception
     */
    public function getMemberInfo(string $signNum): array
    {
        $bizParameter = new BizParameter();
        $bizParameter->addParam("signNum", $signNum);
        
        $this->logger->info('查询会员信息', ['signNum' => $signNum]);
        return $this->sendMemberRequest("1027", $bizParameter);
    }

    /**
     * 会员绑卡信息查询（1030）
     * 会员绑卡信息查询
     * 
     * @param string $signNum 会员编号
     * @return array 响应结果
     * @throws Exception
     */
    public function getMemberBindCardInfo(string $signNum): array
    {
        $bizParameter = new BizParameter();
        $bizParameter->addParam("signNum", $signNum);
        
        $this->logger->info('查询会员绑卡信息', ['signNum' => $signNum]);
        return $this->sendMemberRequest("1030", $bizParameter);
    }

    /**
     * 会员解绑银行卡（1031）
     * 会员解绑银行卡
     * 
     * @param array $unbindData 解绑数据
     * @return array 响应结果
     * @throws Exception
     */
    public function unbindMemberCard(array $unbindData): array
    {
        $this->validateRequired($unbindData, ['signNum', 'acctNum']);
        
        $bizParameter = new BizParameter();
        $bizParameter->addParam("reqTraceNum", TxUtils::genReqTraceNum());
        $bizParameter->addParam("signNum", $unbindData['signNum']);
        $bizParameter->addParam("acctNum", $this->encryptSensitiveData($unbindData['acctNum']));
        $bizParameter->addParam("bizOrderCode", (string)TxUtils::getMillisecond());
        
        $this->logger->info('发起会员解绑银行卡', ['signNum' => $unbindData['signNum']]);
        return $this->sendMemberRequest("1031", $bizParameter);
    }

    /**
     * 会员销户（1032）
     * 会员销户申请
     * 
     * @param string $signNum 会员编号
     * @param string $reason 销户原因
     * @return array 响应结果
     * @throws Exception
     */
    public function closeMemberAccount(string $signNum, string $reason = ''): array
    {
        $bizParameter = new BizParameter();
        $bizParameter->addParam("reqTraceNum", TxUtils::genReqTraceNum());
        $bizParameter->addParam("signNum", $signNum);
        $bizParameter->addParam("bizOrderCode", (string)TxUtils::getMillisecond());
        
        if (!empty($reason)) {
            $bizParameter->addParam("reason", $reason);
        }
        
        $this->logger->info('发起会员销户', ['signNum' => $signNum, 'reason' => $reason]);
        return $this->sendMemberRequest("1032", $bizParameter);
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
            $this->logger->warning("会员业务处理失败", $result);
        } else {
            $this->logger->info("会员业务处理成功", ['transCode' => $transCode, 'code' => $response->code]);
        }

        return $result;
    }

    /**
     * 验证个人会员数据
     * 
     * @param array $data 个人会员数据
     * @throws Exception
     */
    private function validatePersonalMemberData(array $data): void
    {
        $required = ['signNum', 'name', 'cerNum', 'acctNum', 'phone'];
        $this->validateRequired($data, $required);
        
        // 验证身份证号格式
        if (!preg_match('/^[1-9]\d{5}(18|19|20)\d{2}((0[1-9])|(1[0-2]))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$/', $data['cerNum'])) {
            throw new Exception('身份证号格式不正确');
        }
        
        // 验证手机号格式
        if (!preg_match('/^1[3-9]\d{9}$/', $data['phone'])) {
            throw new Exception('手机号格式不正确');
        }
    }

    /**
     * 验证企业会员数据
     * 
     * @param array $data 企业会员数据
     * @throws Exception
     */
    private function validateCompanyMemberData(array $data): void
    {
        $required = ['signNum', 'companyName', 'licenseNo', 'legalName', 'legalCerNum'];
        $this->validateRequired($data, $required);
        
        // 验证统一社会信用代码格式
        if (!preg_match('/^[0-9A-HJ-NPQRTUWXY]{2}\d{6}[0-9A-HJ-NPQRTUWXY]{10}$/', $data['licenseNo'])) {
            throw new Exception('统一社会信用代码格式不正确');
        }
        
        // 验证法人身份证号格式
        if (!preg_match('/^[1-9]\d{5}(18|19|20)\d{2}((0[1-9])|(1[0-2]))(([0-2][1-9])|10|20|30|31)\d{3}[0-9Xx]$/', $data['legalCerNum'])) {
            throw new Exception('法人身份证号格式不正确');
        }
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