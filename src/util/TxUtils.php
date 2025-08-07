<?php

namespace util;


use DateTime;

class TxUtils
{
    private const YYYY_MM_DD_HH_MM_SS = 'YmdHis';

    public static function genReqTraceNum()
    {
        date_default_timezone_set("Asia/Shanghai");
        $date = new DateTime();
        $formattedDate = $date->format(self::YYYY_MM_DD_HH_MM_SS);
        $randomNumber = rand(1000, 9999);
        return $formattedDate . $randomNumber;
    }

    public static function getMillisecond() {
        list($s1, $s2) = explode(' ', microtime());
        return (float)sprintf('%.0f', (floatval($s1) + floatval($s2)) * 1000);
    }


    public static function main()
    {
       // date_default_timezone_set("Asia/Shanghai");
        echo self::genReqTraceNum().PHP_EOL;
    }
}

TxUtils::main();
