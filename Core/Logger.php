<?php

declare(strict_types=1);

namespace Core;

class Logger
{
    private static $logs = array();

    public static function emergency(string $message, array $context = []): void
    {
        self::log('emergency', $message, $context);
    }

    public static function alert(string $message, array $context = []): void
    {
        self::log('alert', $message, $context);
    }

    public static function critical(string $message, array $context = []): void
    {
        self::log('critical', $message, $context);
    }

    public static function error(string $message, array $context = []): void
    {
        self::log('error', $message, $context);
    }

    public static function warning(string $message, array $context = []): void
    {
        self::log('warning', $message, $context);
    }

    public static function notice(string $message, array $context = []): void
    {
        self::log('notice', $message, $context);
    }

    public static function info(string $message, array $context = []): void
    {
        self::log('info', $message, $context);
    }

    public static function debug(string $message, array $context = []): void
    {
        self::log('debug', $message, $context);
    }

    public static function log($level, string $message, array $context = []): void
    {
        self::$logs[] = array(
            'level' => $level,
            'message' => $message,
            'context' => $context
        );

        $logFile = __DIR__ . '/../../storage/nazdy.log';

        $date = date('Y-m-d H:i:s');
        $message = "[$date] [$level] $message\n";

        file_put_contents($logFile, $message, FILE_APPEND);
    }

    public static function getLogs(): array
    {
        return self::$logs;
    }
}