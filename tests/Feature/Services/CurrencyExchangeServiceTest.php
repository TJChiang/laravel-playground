<?php

namespace Tests\Feature\Services;

use App\Models\CurrencyRate;
use App\Repositories\CurrencyRepository;
use App\Services\CurrencyExchangeService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use InvalidArgumentException;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

#[CoversClass(CurrencyExchangeService::class)]
class CurrencyExchangeServiceTest extends TestCase
{
    #[Test]
    #[TestDox('測試來源貨幣代碼不存在，拋出例外')]
    public function shouldThrowExceptionIfSourceCodeNotFound(): void
    {
        // Arrange
        $amount = 123456;
        $sourceCode = 'TWD';
        $targetCode = 'JPY';

        // Assert
        $this->expectException(ModelNotFoundException::class);

        $this->mock(CurrencyRepository::class)
            ->shouldReceive('getRatesByCode')
            ->with($sourceCode)
            ->once()
            ->andThrow(ModelNotFoundException::class);

        // Act
        /** @var CurrencyExchangeService $target */
        $target = $this->app->make(CurrencyExchangeService::class);
        $target->exchangeRate($sourceCode, $targetCode, $amount);
    }

    #[Test]
    #[TestDox('測試目標貨幣匯率不存在，拋出例外')]
    public function shouldThrowExceptionIfTargetRateNotFound(): void
    {
        // Arrange
        $amount = 123456;
        $sourceCode = 'TWD';
        $targetCode = 'RMB';

        // Assert
        $this->expectException(InvalidArgumentException::class);

        $this->mock(CurrencyRepository::class)
            ->shouldReceive('getRatesByCode')
            ->with($sourceCode)
            ->once()
            ->andReturn(CurrencyRate::factory()->make([
                'currency_code' => $sourceCode,
            ]));

        // Act
        /** @var CurrencyExchangeService $target */
        $target = $this->app->make(CurrencyExchangeService::class);
        $target->exchangeRate($sourceCode, $targetCode, $amount);
    }

    #[Test]
    #[TestDox('測試換匯成功，回傳結果')]
    #[DataProvider('provideValidCurrencyAmount')]
    public function shouldReturnResultIfExchangeSucceed($amount, string $expected): void
    {
        // Arrange
        $sourceCode = 'TWD';
        $targetCode = 'JPY';
        $rate = 3.21234;

        $this->mock(CurrencyRepository::class)
            ->shouldReceive('getRatesByCode')
            ->with($sourceCode)
            ->once()
            ->andReturn(CurrencyRate::factory()->make([
                'currency_code' => $sourceCode,
                "{$targetCode}_rate" => $rate,
            ]));

        // Act
        /** @var CurrencyExchangeService $target */
        $target = $this->app->make(CurrencyExchangeService::class);
        $actual = $target->exchangeRate($sourceCode, $targetCode, $amount);

        // Assert
        self::assertSame($expected, $actual);
    }

    public static function provideValidCurrencyAmount(): iterable
    {
        yield [2.54, '8.16'];
        yield [30, '96.37'];
        yield [5.1, '16.38'];
        yield [24, '77.10'];
        yield [0.3, '0.96'];
        yield [0.12, '0.39'];
        yield [0.02, '0.06'];
        yield ['6.04', '19.40'];
        yield ['7.46', '23.96'];
        yield ['8.7', '27.95'];
        yield ['7', '22.49'];
        yield ['0.8', '2.57'];
        yield ['0.08', '0.26'];
        yield ['0.18', '0.58'];
    }
}
