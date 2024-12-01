<?php

namespace App\Service\Logger;

interface ILogger
{
    public const APP_LEVEL_LOG_DEBUG   = 0;
    public const APP_LEVEL_LOG_INFO    = 2;
    public const APP_LEVEL_LOG_ERROR   = 1;

    public const APP_STATUS_SUCCESS = 0;
    public const APP_STATUS_ERROR   = 1;

    public function createLog(string $message, string $action, int $status = self::APP_STATUS_SUCCESS, int $levelLog = self::APP_LEVEL_LOG_DEBUG): void;
    public function deleteLogs(): void;
    public function getLogs(): array;
}
