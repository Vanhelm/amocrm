<?php

namespace App\Http\Controllers\Core;

use App\Http\Controllers\Controller;
use App\Service\Logger\ILogger;

class LoggerController extends Controller
{
    public function logListAction(ILogger $logger)
    {
        try {
            return response($logger->getLogs());
        } catch (\Exception $e) {
            return [[
                'status'  => 1,
                'action'  => 'Запрошены логи',
                'date'    => now()->format('Y-m-d H:i:s'),
            ]];
        }
    }
}
