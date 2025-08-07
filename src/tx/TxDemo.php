<?php

namespace tx;

use config\DemoConfig;
use Monolog\Logger;

class TxDemo
{
    protected static Logger $logger;

    protected static DemoConfig $demoConfig;

    public static function init()
    {
        self::$demoConfig = new DemoConfig();
        self::$logger = new Logger('TxDemo');
    }

    public function __construct()
    {
        self::init();
    }
}