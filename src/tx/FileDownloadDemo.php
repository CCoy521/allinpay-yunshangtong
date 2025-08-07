<?php
namespace tx;

include 'TxDemo.php';
include '../config/DemoConfig.php';
include '../util/DemoSM2Utils.php';

require_once __DIR__ . '/../../vendor/autoload.php';

use config\DemoConfig;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Request;
use GuzzleHttp\Exception\RequestException;
use Monolog\Logger;
use tx\TxDemo;
use Rtgm\sm\RtSm2;
use util\DemoSM2Utils;


class FileDownloadDemo extends TxDemo
{
    private const FILEPATH = 'E:\\Downloads\\'; // 文件下载路径

    protected static Logger $logger;

    protected static DemoConfig $demoConfig ;

    protected static RtSm2 $sm2;

    public function __construct()
    {
        self::$demoConfig = new DemoConfig();
        self::$sm2 = new RtSm2('base64');
        self::$logger = new Logger('FileDownloadDemo');
    }

    public static function main()
    {
        new FileDownloadDemo();
        echo self::download("1", "20240626");
    }

    private static function download($fileType, $fileDate)
    {
        $result = "";
        try {
            $client = new Client();
            $formParams = [
                'spAppId' => self::$demoConfig->getSpAppId(),
                'appId' => self::$demoConfig->getAppId(),
                'fileType' => $fileType,
                'fileDate' => $fileDate
            ];

            //SM2加签
            $srcMsg = self::$demoConfig->getSpAppId() . self::$demoConfig->getAppId() . $fileType . $fileDate;
            $privateKey = DemoSM2Utils::privKeySM2FromBase64Str(self::$demoConfig->getPrivateKeyStr());
            $sign = self::$sm2->doSign($srcMsg, $privateKey);
            $sign = trim($sign);
            $formParams['sign'] = $sign;

            $request = new Request('POST', self::$demoConfig->getFileDownloadUrl());
            echo PHP_EOL;
            echo $request->getUri();
            //echo $request->getBody();
            $response = $client->send($request, ['form_params' => $formParams]);

            if ($response->getStatusCode() == 200) {
                $contentType = $response->getHeaderLine('Content-Type');
                self::$logger->info("contentType=" . $contentType);

                if ($contentType === "text/html;charset=UTF-8") {
                    $result = "Download failed, response: " . $response->getBody();
                } else {
                    $fileName = self::getFileName($response);
                    $filePath = self::FILEPATH . $fileName;

                    if (!file_exists(dirname($filePath))) {
                        mkdir(dirname($filePath), 0777, true);
                    }

                    file_put_contents($filePath, $response->getBody());
                    $result = "File downloaded successfully, path: " . $filePath;
                }
            }
        } catch (RequestException $e) {
            echo $e->getMessage();
        }

        return $result;
    }

    public static function getFileName($response): ?string
    {
        $contentDisposition = $response->getHeaderLine('Content-Disposition');
        $filename = null;

        if (!empty($contentDisposition)) {
            if (preg_match('/filename="?(.+)"?/', $contentDisposition, $matches)) {
                $filename = $matches[1];
            }
        }

        return $filename;
    }


}

FileDownloadDemo::main();

