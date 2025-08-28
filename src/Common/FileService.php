<?php

namespace AllinpayYunshangtong\Common;

use AllinpayYunshangtong\Config\AppConfig;
use AllinpayYunshangtong\Utils\HttpClient;
use AllinpayYunshangtong\Utils\SignatureUtils;
use AllinpayYunshangtong\VO\BaseRequest;
use AllinpayYunshangtong\VO\BaseResponse;

/**
 * 文件处理服务类
 * 处理文件上传和下载业务逻辑
 */
class FileService
{
    private AppConfig $config;
    private HttpClient $httpClient;
    private SignatureUtils $signatureUtils;
    
    public function __construct(AppConfig $config)
    {
        $this->config = $config;
        $this->httpClient = new HttpClient($config);
        $this->signatureUtils = new SignatureUtils($config);
    }
    
    /**
     * 文件上传
     * 接口代码：FileUpload
     */
    public function uploadFile(string $filePath, string $fileType, string $bizUserId = ''): BaseResponse
    {
        // 检查文件是否存在
        if (!file_exists($filePath)) {
            throw new \InvalidArgumentException('文件不存在: ' . $filePath);
        }
        
        // 读取文件内容
        $fileContent = file_get_contents($filePath);
        if ($fileContent === false) {
            throw new \RuntimeException('文件读取失败: ' . $filePath);
        }
        
        // 构建上传参数
        $uploadData = [
            'fileType' => $fileType,
            'fileContent' => base64_encode($fileContent),
            'fileName' => basename($filePath)
        ];
        
        if (!empty($bizUserId)) {
            $uploadData['bizUserId'] = $bizUserId;
        }
        
        $request = new BaseRequest();
        $request->setAppId($this->config->get('appId'))
                ->setTransCode('FileUpload')
                ->setBizData(json_encode($uploadData, JSON_UNESCAPED_UNICODE));
        
        // 生成签名
        $sign = $this->signatureUtils->generateSign($request->toArray());
        $request->setSign($sign);
        
        // 发送请求
        $responseData = $this->httpClient->sendFileUpload($request->toArray());
        
        return new BaseResponse($responseData);
    }
    
    /**
     * 文件下载
     * 接口代码：FileDownload
     */
    public function downloadFile(string $fileId, string $savePath = ''): BaseResponse
    {
        $request = new BaseRequest();
        $request->setAppId($this->config->get('appId'))
                ->setTransCode('FileDownload')
                ->setBizData(json_encode(['fileId' => $fileId], JSON_UNESCAPED_UNICODE));
        
        // 生成签名
        $sign = $this->signatureUtils->generateSign($request->toArray());
        $request->setSign($sign);
        
        // 发送请求
        $responseData = $this->httpClient->sendFileDownload($request->toArray());
        
        // 如果下载成功且有文件内容，保存文件
        if ($responseData['code'] === '000000' && isset($responseData['data']['fileContent'])) {
            $this->saveDownloadedFile($responseData['data']['fileContent'], $savePath);
        }
        
        return new BaseResponse($responseData);
    }
    
    /**
     * 保存下载的文件
     */
    private function saveDownloadedFile(string $fileContent, string $savePath = ''): void
    {
        $decodedContent = base64_decode($fileContent);
        
        if ($decodedContent === false) {
            throw new \RuntimeException('文件内容解码失败');
        }
        
        // 如果没有指定保存路径，使用临时目录
        if (empty($savePath)) {
            $savePath = sys_get_temp_dir() . DIRECTORY_SEPARATOR . 'allinpay_' . uniqid() . '.tmp';
        }
        
        // 确保目录存在
        $dir = dirname($savePath);
        if (!is_dir($dir)) {
            if (!mkdir($dir, 0755, true)) {
                throw new \RuntimeException('无法创建目录: ' . $dir);
            }
        }
        
        // 保存文件
        if (file_put_contents($savePath, $decodedContent) === false) {
            throw new \RuntimeException('文件保存失败: ' . $savePath);
        }
    }
    
    /**
     * 上传身份证照片
     */
    public function uploadIdCard(string $filePath, string $bizUserId, string $idCardType = '01'): BaseResponse
    {
        return $this->uploadFile($filePath, 'ID_CARD', $bizUserId);
    }
    
    /**
     * 上传营业执照
     */
    public function uploadBusinessLicense(string $filePath, string $bizUserId): BaseResponse
    {
        return $this->uploadFile($filePath, 'BUSINESS_LICENSE', $bizUserId);
    }
    
    /**
     * 上传协议文件
     */
    public function uploadAgreement(string $filePath, string $bizUserId): BaseResponse
    {
        return $this->uploadFile($filePath, 'AGREEMENT', $bizUserId);
    }
    
    /**
     * 上传其他证明文件
     */
    public function uploadOtherDocument(string $filePath, string $fileType, string $bizUserId = ''): BaseResponse
    {
        return $this->uploadFile($filePath, $fileType, $bizUserId);
    }
    
    /**
     * 批量文件上传
     */
    public function batchUploadFiles(array $files): array
    {
        $results = [];
        
        foreach ($files as $file) {
            try {
                $result = $this->uploadFile(
                    $file['path'],
                    $file['type'],
                    $file['bizUserId'] ?? ''
                );
                $results[] = [
                    'file' => $file,
                    'success' => true,
                    'response' => $result
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'file' => $file,
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }
        
        return $results;
    }
}
