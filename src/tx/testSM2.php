<?php

include "../../vendor/autoload.php";

use Rtgm\sm\RtSm2;

$publicfile = "../PEMFile/SM2Pub.pem";
$privatefile = "../PEMFile/SM2.pem";
$userId = '1234567812345678';
//$document = "appId=21762000921804636162&bizData={\"signNum\":\"YTF202407030001\",\"cerNum\":\"4d21bffbbafbf8857f7341e43a29df1b1b0ff1bc63cbee1691c0921dd9ea5992\",\"phone\":\"18248569965\",\"acctNum\":\"ca3b9e77dd72caa94dd93ef25c4998c39fb11ce622839ed0ee3efa76fb433aa6\",\"reqTraceNum\":\"202410101429595233\",\"name\":\"张三\",\"bindType\":\"8\",\"cerType\":\"1\",\"memberRole\":\"分账方\"}&charset=UTF-8&format=json&transCode=1010&transDate=20241010&transTime=143002&version=1.0";
  $document = "appId=21879470716783222786&bizData={\"signNum\":\"04bcj03d7695da57ktk_01\",\"openAcctStatus\":\"1\",\"payAcctNoStatus\":\"0\",\"respTraceNum\":\"20250123115747102500481214\",\"cusId\":\"660883017710154\",\"reqTraceNum\":\"20250123115752583339267\",\"payAcctAuditJson\":{\"legaIdCardVerifyResult\":\"2\",\"bankAcctVerifyResult\":\"0\",\"legalCerPhotoResult\":\"4\",\"tlPayAcctNoAgreeResult\":\"0\",\"unifiedCreditPhotoResult\":\"4\",\"busInnerPhotoResult\":\"1\",\"acctManWithIdPhotoResult\":\"1\",\"busOutdoorPhotoResult\":\"1\",\"settleAcctPhotoResult\":\"1\",\"busCoopConfirmResult\":\"0\",\"enterpriseVerifyResult\":\"2\",\"acctManOutdoorPhotoResult\":\"1\",\"nonNatBenfitInfoResult\":\"0\"},\"payAcctNo\":\"123115811198104\"}&charset=utf-8&notifyId=b6ec42d5-5255-4b79-b97e-8ea4ed879682&notifyTime=2025-01-23 13:39:32&transCode=1025&version=1.0";
//$document = "21762000921804636162120240626";


$privateKeyStr2 = "MHcCAQEEIAae+zUxhH7XNm929hu/Gdf9YvVqUHltgnpRq5bB/nCVoAoGCCqBHM9VAYItoUQDQgAEYtNE4m8DEp/Srn3XH7/iiX2plZjvj03GmOvHPhKhycDm5ZLg1iAkiJtzie0xjF7/Iss+IpFjSYoPE2anR1iOmg==";
$allinpayPublicKeyStr2 = "MFkwEwYHKoZIzj0CAQYIKoEcz1UBgi0DQgAEYtNE4m8DEp/Srn3XH7/iiX2plZjvj03GmOvHPhKhycDm5ZLg1iAkiJtzie0xjF7/Iss+IpFjSYoPE2anR1iOmg==";

//public 通联公钥
$temp1 = "BLvSzZCZci7YycbUFiBpXGf4ch7eXmeyxB9Z3zoitR/R7BNXDD7uSiuY2VU7QMnO5TzF4sVDIq3+gN7TG7jG4Ko=";
//public? 应用公钥
$temp2 = "BOc4GuaH7jHlEFbaElH48j/dRVPEMd+fWsj0Jm7ADQxt1hUhCL/WM4sjGBilaZDVOMgxcME42KjO1AMft8aVtC4=";
//private 应用私钥
$temp3 = "xL/uTSkBSGPtJWV7O+KTBPds8wZ9iTxondaH7NneHw4=";
//$temp3 = "iaZmB+feACtziE8SYjVZsaQwLNLRiyO8ebSupeoWIF0=";

$privateKey = bin2hex(base64_decode($temp3));
$publicKey = bin2hex(base64_decode($temp2));


$signStr = "MEUCIQCJdjmEeDcqOn7FDMILK1w859uT81zIKOcMRSA8TFUB7QIgczU0M2Ch9QAurYggWvjJNVn0\/lt7TJKKQbXdBaTW82c=";
$publicKeyApp = "BPASjTOropoH33Y9OhZ4kfnYuZ9LSwWi+cHz8sxqggA5OUUm0DdiCNZcqU7cpP4ao4J8ILjETrSKwKvby2rLuEw=";


define('GK', 1);
define('SIGN', 1);
define('SIGNPEM', 1);
//返回的签名16进制还是base64, 目前可选hex,与base64两种
$sm2 = new RtSm2('base64');

$privateKeyHex = bin2hex(base64_decode($privateKeyStr2));
$allinpayPublicKeyHex = bin2hex(base64_decode($allinpayPublicKeyStr2));

//if(GK){
//    echo "\n----------生成明文密钥对--------------------------\n";
//    print_r($sm2->generatekey()); //生成明文密钥
//    echo "\n----------生成pem密钥对--------------------------\n";
//    print_r($sm2->generatePemkey()); //生成pem密钥，请放到相应的文件中
//}

if (SIGN) {
    echo "\n---------明文密钥签名---------------------------\n";
    $sign = $sm2->doSign($document, $privateKey, $userId);
    print_r($sign);
    echo "\n---------明文密钥验签---------------------------\n";
    var_dump($sm2->verifySign($document, $sign, $publicKey, $userId));
    echo "\n---------明文密钥验签---------------------------\n";
    var_dump($sm2->verifySign($document, $signStr, $publicKey, $userId));
}

//if(SIGNPEM){
//    echo "\n---------PEM密钥签名---------------------------\n";
//    $sign = $sm2->doSignOutKey( $document, $privatefile, $userId);
//    print_r($sign);
//    echo "\n---------PEM密钥验签---------------------------\n";
//    var_dump($sm2->verifySignOutKey( $document, $sign, $publicfile, $userId ));
//}
