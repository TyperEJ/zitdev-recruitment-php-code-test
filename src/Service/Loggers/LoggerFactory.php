<?php

namespace App\Service\Loggers;

class LoggerFactory
{
    const TYPE_LOG4PHP = 'log4php';
    const TYPE_THINK_LOG = 'think-log';

    /**
     * @throws LoggerTypeErrorException
     */
    public static function make($type): LoggerInterface
    {
        switch ($type)
        {
            case LoggerFactory::TYPE_LOG4PHP:
                return new Log4PHPLogger();
            case LoggerFactory::TYPE_THINK_LOG:
                return new ThinkLogLogger();
            default:
                throw new LoggerTypeErrorException();
        }
    }
}