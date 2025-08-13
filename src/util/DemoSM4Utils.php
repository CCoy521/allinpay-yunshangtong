<?php
namespace Allinpay\YunshangTong\Util;

include '../config/DemoConfig.php';

require_once __DIR__ . '/../../vendor/autoload.php';

use Allinpay\YunshangTong\Config\DemoConfig;
use FG\ASN1\ASNObject;
use phpseclib3\Crypt\Random;
use Rtgm\sm\RtSm4;

class DemoSM4Utils
{

    public const ALGORITHM_NAME_ECB_PADDING = 'sm4-ecb';
    public const DEFAULT_KEY_SIZE = 128;


    /**
     * Automatically generate key
     *
     * @return string
     */
    public static function generateKey(): string
    {
        return self::generateKeyWithSize(self::DEFAULT_KEY_SIZE);
    }

    /**
     * Generate key with specified size
     *
     * @param int $keySize Key size
     * @return string
     */
    public static function generateKeyWithSize(int $keySize): string
    {
        return Random::string($keySize / 8);
    }

    /**
     * sm4加密
     *
     * @explain 加密模式：ECB 密文长度不固定，会随着被加密字符串长度的变化而变化
     * @param hexKey 16进制密钥（忽略大小写）
     * @param paramStr 待加密字符串
     * @return 返回16进制的加密字符串
     * @throws \Exception
     */
    public static function encryptEcb(string $hexKey, string $paramStr): string
    {
        $keyData = hex2bin($hexKey);
        $srcData = $paramStr;
        return self::encrypt_Ecb_Padding($keyData, $srcData);
    }

    /**
     * Encryption mode ECB
     *
     * @param string $key Key
     * @param string $data Data
     * @return string
     * @throws \Exception
     */
    public static function encrypt_Ecb_Padding(string $key, string $data): string
    {
        $cipher = new RtSm4($key);
        return $cipher->encrypt($data, self::ALGORITHM_NAME_ECB_PADDING);
    }

    /**
     * sm4解密
     *
     * @explain 解密模式：采用ECB
     * @param hexKey 16进制密钥
     * @param cipherText 16进制的加密字符串（忽略大小写）
     * @return 解密后的字符串
     * @throws \Exception
     */
    public static function decryptEcb(string $hexKey, string $cipherText): string
    {
        $keyData = hex2bin($hexKey);
        $cipherData = hex2bin($cipherText);
        return self::decrypt_Ecb_Padding($keyData, $cipherData);
    }

    /**
     * Decryption
     *
     * @param string $key Key
     * @param string $cipherText Cipher text
     * @return string
     * @throws \Exception
     */
    public static function decrypt_Ecb_Padding(string $key, string $cipherText): string
    {
        $cipher = new RtSm4($key);
        return $cipher->decrypt($cipherText, self::ALGORITHM_NAME_ECB_PADDING);
    }

    /**
     * Verify if the string before and after encryption is the same data
     *
     * @param string $hexKey 16-digit hexadecimal key (case-insensitive)
     * @param string $cipherText Encrypted hexadecimal string
     * @param string $paramStr String before encryption
     * @return bool Whether it is the same data
     * @throws \Exception
     */
    public static function verifyEcb(string $hexKey, string $cipherText, string $paramStr): bool
    {
        $keyData = hex2bin($hexKey);
        $cipherData = hex2bin($cipherText);
        $decryptData = self::decrypt_Ecb_Padding($keyData, $cipherData);
        $srcData = $paramStr;
        return $decryptData === $srcData;
    }

    public static function test(){
        // Example usage
        $conf = new DemoConfig();
        $key = $conf->getSecretKey();
        $keys = hex2bin($key);
        $sm4 = new RtSm4($keys);
        $hex = $sm4->encrypt('410725199907022818','sm4-ecb');
        echo $hex . PHP_EOL;
        $data2 = $sm4->decrypt($hex,'sm4-ecb');
        echo base64_decode("89fe8e44892a1e567a2196b801b73781");
        $data3 = $sm4->decrypt('89fe8e44892a1e567a2196b801b73781','sm4-ecb');
        echo $data2 . PHP_EOL;
    }
}

(new DemoSM4Utils())->test();
