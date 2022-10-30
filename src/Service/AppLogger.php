<?php

namespace App\Service;

use App\Service\Loggers\LoggerFactory;
use App\Service\Loggers\LoggerTypeErrorException;

class AppLogger
{
    private $logger;

    /**
     * @throws LoggerTypeErrorException
     */
    public function __construct($type)
    {
        $this->logger = LoggerFactory::make($type);
    }

    public function info($message = '')
    {
        $this->logger->info($message);
    }

    public function debug($message = '')
    {
        $this->logger->debug($message);
    }

    public function error($message = '')
    {
        $this->logger->error($message);
    }
}