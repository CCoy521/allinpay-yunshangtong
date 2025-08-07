<?php
namespace tx;
include 'TxDemo.php';
include '../config/DemoConfig.php';
include '../util/DemoSM2Utils.php';

require_once __DIR__ . '/../../vendor/autoload.php';

use config\DemoConfig;
use Exception;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Utils;
use Rtgm\sm\RtSm2;
use SplFileObject;
use tx\TxDemo;
use util\DemoSM2Utils;

class FileUploadDemo extends TxDemo
{
    private const FILEPATH = 'E:\\1.txt';//替换为本地文件路径

    protected static DemoConfig $demoConfig;

    private static RtSm2 $sm2;

    public static function main()
    {
        self::$demoConfig = new DemoConfig();
        self::$sm2 = new RtSm2('base64');
        echo self::upload(self::FILEPATH, '0');
    }

    private static function upload($filePath, $fileType)
    {
        $result = '';
        try {
            $timestamp = date('Y-m-d H:i:s');
            $file = new SplFileObject($filePath);
            $fileStream = Utils::streamFor($file->openFile());

            $client = new Client();

            $multipart = [
                [
                    'name' => 'file',
                    'contents' => $fileStream,
                    'filename' => $file->getFilename()
                ],
                [
                    'name' => 'appId',
                    'contents' => self::$demoConfig->getAppId()
                ],
                [
                    'name' => 'fileType',
                    'contents' => $fileType
                ],
                [
                    'name' => 'timestamp',
                    'contents' => $timestamp
                ]
            ];

            // 计算 MD5
            $md5 = self::getFileMD5($filePath);
            $multipart[] = [
                'name' => 'md5',
                'contents' => $md5
            ];

            // 签名
            $srcMsg = self::$demoConfig->getSpAppId() . self::$demoConfig->getAppId() . $fileType . $md5 . $timestamp;
            $privateKey = DemoSM2Utils::privKeySM2FromBase64Str(self::$demoConfig->getPrivateKeyStr());
            $sign = self::$sm2->doSign( $srcMsg,$privateKey);
            $multipart[] = [
                'name' => 'sign',
                'contents' => $sign
            ];

            $response = $client->post(self::$demoConfig->getFileUploadUrl(), [
                'multipart' => $multipart
            ]);

            $result = $response->getBody()->getContents();
        } catch (Exception $ex) {
            echo $ex->getMessage();
        }

        return $result;
    }

    public static function getFileMD5($filePath)
    {
        if (!file_exists($filePath)) {
            return null;
        }

        $md5 = md5_file($filePath);
        return base64_encode(hex2bin($md5));
    }
}

FileUploadDemo::main();

