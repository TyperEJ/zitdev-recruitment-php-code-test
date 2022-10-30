<?php

namespace App\Service\Loggers;

use think\facade\Log;

class ThinkLogLogger implements LoggerInterface
{
    private $defaultConfig = [
        'default'	=>	'file',
        'channels'	=>	[
            'file'	=>	[
                'type'	=>	'file',
                'path'	=>	'./logs/',
            ],
        ],
    ];

    public function __construct($config = null)
    {
        Log::init($config ?? $this->defaultConfig);
    }

    private function toUpperString($string): string
    {
        return strtoupper($string);
    }

    public function info($message)
    {
        Log::info(
            $this->toUpperString($message)
        );
    }

    public function debug($message)
    {
        Log::debug(
            $this->toUpperString($message)
        );
    }

    public function error($message)
    {
        Log::error(
            $this->toUpperString($message)
        );
    }
}