<?php

namespace App\Repositories;

use App\Models\CurrencyRate;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Redis\Connections\Connection;

class CurrencyRepository
{
    private string $cacheKey = 'currency_code_list';

    public function __construct(protected readonly Connection $redis, protected readonly int $ttl = 604800)
    {
    }

    /**
     * @throws ModelNotFoundException
     */
    public function getCodeList(): array
    {
        $cacheList = $this->redis->smembers($this->cacheKey);
        if (!empty($cacheList)) {
            return $cacheList;
        }

        $list = CurrencyRate::orderBy('id', 'asc')
            ->get(['id', 'currency_code'])
            ->map(fn ($entity) => $entity['currency_code'])
            ->toArray();

        if (empty($list)) {
            throw new ModelNotFoundException();
        }

        $this->redis->sadd($this->cacheKey, ...$list);
        $this->redis->expire($this->cacheKey, $this->ttl);
        return $list;
    }

    /**
     * @throws ModelNotFoundException
     */
    public function getRatesByCode(string $code): CurrencyRate
    {
        return CurrencyRate::where('currency_code', $code)->firstOrFail();
    }
}
