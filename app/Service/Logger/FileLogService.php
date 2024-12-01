<?php

namespace App\Service\Logger;

use Carbon\Carbon;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Storage;
use Psr\Log\LoggerInterface;

class FileLogService implements ILogger
{
    /** @var LoggerInterface */
    private $_logger;

    public function __construct()
    {
        $this->_logger = Log::channel('system');
    }

    public function createLog(string $message, string $action, int $status = self::APP_STATUS_SUCCESS, int $levelLog = ILogger::APP_LEVEL_LOG_DEBUG): void
    {
        $this->_writeLogToFile($message, $action, $status, $levelLog);
    }

    public function deleteLogs(): void
    {
        if (config('logging.channels.system.path')) {
            unlink(config('logging.channels.system.path'));
        }
    }

    public function getLogs(): array
    {
        $logPath = config('logging.channels.system.path');

        if (!file_exists($logPath)) {
            return [];
        }

        $logContent = file_get_contents($logPath);

        if ($logContent === false || $logContent === '') {
            return [];
        }

        $logs = explode("\n", trim($logContent));

        return array_map(function (string $log): array {
            $log      = json_decode($log, true);
            $message  = $log['message'] ?? 'without message';
            $context  = $log['context'] ?? "{}";
            $dateTime = Carbon::create($log['datetime'] ?? now())->format('Y-m-d H:i:s');

            $data = ['message' => $message, 'date' => $dateTime];
            return array_merge($data, $context);
        }, $logs);
    }

    private function _writeLogToFile(string $message, string $action, int $status, int $levelLog): void
    {
        $context = ['action' => $action, 'status' => $status];
        switch ($levelLog) {
            case ILogger::APP_LEVEL_LOG_DEBUG:
                $this->_logger->debug($message, $context);
                break;
            case ILogger::APP_LEVEL_LOG_INFO:
                $this->_logger->info($message, $context);
                break;
            case ILogger::APP_LEVEL_LOG_ERROR:
                $this->_logger->error($message, $context);
                break;
        }
    }
}
