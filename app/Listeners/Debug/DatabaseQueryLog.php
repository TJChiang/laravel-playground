<?php

namespace App\Listeners\Debug;

use Illuminate\Database\Events\QueryExecuted;
use Illuminate\Support\Facades\Log;

readonly class DatabaseQueryLog
{
    public function handle(QueryExecuted $event): void
    {
        Log::debug('Query executed', [
            'connection_name' => $event->connectionName,
            'connection' => $event->connection->getNameWithReadWriteType(),
            'sql' => $event->sql,
            'bindings' => $event->bindings,
            'time' => $event->time,
        ]);
    }
}
