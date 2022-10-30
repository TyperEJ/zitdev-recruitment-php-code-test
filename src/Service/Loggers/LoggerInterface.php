<?php

namespace App\Service\Loggers;

interface LoggerInterface
{
    public function info($message);

    public function debug($message);

    public function error($message);
}