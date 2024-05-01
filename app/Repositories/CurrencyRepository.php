<?php

namespace App\Repositories;

use App\Models\Currency;
use Illuminate\Redis\Connections\Connection;

class CurrencyRepository
{
    private string $cacheKey = 'currency_code_list';

    public function __construct(protected readonly Connection $redis, protected readonly int $ttl = 604800)
    {
    }

    public function getCodeList(): array
    {
        $cacheList = $this->redis->smembers($this->cacheKey);
        if (!empty($cacheList)) {
            return $cacheList;
        }

        $list = Currency::orderBy('id', 'asc')->get(['id', 'code'])->map(fn ($entity) => $entity['code'])->toArray();
        if (empty($list)) {
            return $list;
        }

        $this->redis->sadd($this->cacheKey, ...$list);
        $this->redis->expire($this->cacheKey, $this->ttl);
        return $list;
    }
}
