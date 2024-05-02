<?php

namespace Tests\Feature\Services;

use App\Services\CurrencyExchangeService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

#[CoversClass(CurrencyExchangeService::class)]
class CurrencyExchangeServiceTest extends TestCase
{
    #[Test]
    #[TestDox('測試貨幣代碼不存在，拋出例外')]
    #[DataProvider('provideInvalidCurrencyCode')]
    public function shouldThrowExceptionIfSourceOrTargetNotFound(string $sourceCode, string $targetCode): void
    {
        // Arrange
        $amount = 123456;

        // Act
        $this->expectException(ModelNotFoundException::class);

        // Act
        /** @var CurrencyExchangeService $target */
        $target = $this->app->make(CurrencyExchangeService::class);
        $target->exchangeRate($sourceCode, $targetCode, $amount);
    }

    public static function provideInvalidCurrencyCode(): iterable
    {
        yield '來源貨幣代碼不存在' => ['INVALID', 'TWD'];
        yield '目標貨幣代碼不存在' => ['TWD', 'INVALID'];
    }
}
