<?php

namespace App\Logging;

use Illuminate\Support\Facades\Log;
use Monolog\Formatter\LineFormatter;
use Monolog\Handler\SlackWebhookHandler;

class SlackLogFormatter
{
    public function __invoke($logger)
    {
        foreach ($logger->getHandlers() as $handler) {
            if ($handler instanceof SlackWebhookHandler) {
                $format=""; // Look on the Monolog's Line formatter documentation
                $formatter= new LineFormatter($format,"Y-m-d H:i:s");


                $handler->pushProcessor(function ($record) use ($handler, $logger) {
                   $record['extra']['Project'] = 'POSLA';
                   return $record;
                });

                $handler->setFormatter($formatter);
            }
        }
    }
}
