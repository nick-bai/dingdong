<?php

namespace app\command\service;

class GlobalData
{
    public static function start()
    {
        $worker = new \GlobalData\Server('127.0.0.1', 2207);
    }
}