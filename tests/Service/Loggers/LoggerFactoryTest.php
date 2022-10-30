<?php

namespace Test\Service\Loggers;

use App\Service\Loggers\Log4PHPLogger;
use App\Service\Loggers\LoggerFactory;
use App\Service\Loggers\LoggerTypeErrorException;
use App\Service\Loggers\ThinkLogLogger;
use PHPUnit\Framework\TestCase;

class LoggerFactoryTest extends TestCase
{
    public function testMakeLog4PHPLogger()
    {
        $logger = LoggerFactory::make(LoggerFactory::TYPE_LOG4PHP);

        $this->assertInstanceOf(Log4PHPLogger::class, $logger);
    }

    public function testMakeThinkLogLogger()
    {
        $logger = LoggerFactory::make(LoggerFactory::TYPE_THINK_LOG);

        $this->assertInstanceOf(ThinkLogLogger::class, $logger);
    }

    public function testMakeErrorTypeLogger()
    {
        $this->expectException(LoggerTypeErrorException::class);

        LoggerFactory::make('');
    }
}
