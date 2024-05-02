<?php

namespace Tests\Feature\Repositories;

use App\Models\CurrencyRate;
use App\Repositories\CurrencyRepository;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use Illuminate\Redis\Connections\Connection;
use Illuminate\Support\Facades\Redis;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

#[CoversClass(CurrencyRepository::class)]
class CurrencyRepositoryTest extends TestCase
{
    use DatabaseTransactions;

    protected Connection $redis;

    protected function setUp(): void
    {
        parent::setUp();
        $this->redis = Redis::connection('currency_list');
        $this->redis->flushdb();
    }

    protected function tearDown(): void
    {
        $this->redis->flushdb();
        parent::tearDown(); // TODO: Change the autogenerated stub
    }

    #[Test]
    #[TestDox('測試 cache 有資料，直接從 cache 取得')]
    public function shouldReturnCodeListWhenDataFoundInCache(): void
    {
        // Arrange
        $expected = [
            'USD',
            'JPY',
            'TWD',
        ];

        $this->redis->sadd('currency_code_list', ...$expected);

        // Act
        /** @var CurrencyRepository $target */
        $target = $this->app->make(CurrencyRepository::class);
        $actual = $target->getCodeList();

        // Assert
        self::assertSame($expected, $actual);
    }

    #[Test]
    #[TestDox('測試 cache 和 db 都沒有資料，拋出例外')]
    public function shouldThrowNotFoundExceptionWhenDataNotFoundInCacheAndDB(): void
    {
        // Arrange
        $expected = [];

        $this->expectException(ModelNotFoundException::class);

        // Act
        /** @var CurrencyRepository $target */
        $target = $this->app->make(CurrencyRepository::class);
        $target->getCodeList();

        // Assert
        self::assertSame($expected, $this->redis->smembers('currency_code_list'));
    }

    #[Test]
    #[TestDox('測試只有 db 有資料，回傳資料並暫存到 Cache')]
    public function shouldReturnDBListAndCacheWhenFoundDataOnlyInDB(): void
    {
        // Arrange
        $expected = [
            'USD',
            'TWD',
        ];

        CurrencyRate::factory(2)->sequence(
            ['currency_code' => 'USD'],
            ['currency_code' => 'TWD']
        )->create();

        // Act
        /** @var CurrencyRepository $target */
        $target = $this->app->make(CurrencyRepository::class);
        $actual = $target->getCodeList();

        // Assert
        self::assertSame($expected, $actual);
        self::assertSame($expected, $this->redis->smembers('currency_code_list'));
        self::assertDatabaseHas(CurrencyRate::class, [
            'currency_code' => 'USD',
        ], config('database.default'));
        self::assertDatabaseHas(CurrencyRate::class, [
            'currency_code' => 'TWD',
        ], config('database.default'));
    }
}
