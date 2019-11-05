<?php

namespace CaliforniaMountainSnake\LongmanTelegrambotUtils\Logger;

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;

trait TelegrambotUtilsLogger
{
    /**
     * @var LoggerInterface
     */
    protected $telegrambotUtilsLogger;

    /**
     * @param LoggerInterface $_logger
     */
    public function setTelegrambotUtilsLogger(LoggerInterface $_logger): void
    {
        $this->telegrambotUtilsLogger = $_logger;
    }

    /**
     * @return LoggerInterface
     */
    public function getTelegrambotUtilsLogger(): LoggerInterface
    {
        if ($this->telegrambotUtilsLogger === null) {
            $this->telegrambotUtilsLogger = new NullLogger();
        }
        return $this->telegrambotUtilsLogger;
    }
}
