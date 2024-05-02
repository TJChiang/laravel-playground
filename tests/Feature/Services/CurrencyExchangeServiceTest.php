<?php

namespace Tests\Feature\Services;

use App\Models\CurrencyRate;
use App\Repositories\CurrencyRepository;
use App\Services\CurrencyExchangeService;
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
    #[TestDox('測試貨幣代碼不存在，拋出例外')]
    #[DataProvider('provideInvalidCurrencyCode')]
    public function shouldThrowExceptionIfSourceOrTargetNotFound(string $sourceCode, string $targetCode): void
    {
        // Arrange
        $amount = 123456;

        // Assert
        $this->expectException(InvalidArgumentException::class);

        $mockRepository = $this->mock(CurrencyRepository::class);
        $mockRepository->shouldReceive('getCodeList')->andReturn(['TWD']);
        $mockRepository->shouldReceive('getRatesByCode')->never();

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

    #[Test]
    #[TestDox('測試貨幣數字不存在，拋出例外')]
    #[DataProvider('provideInvalidCurrencyAmount')]
    public function shouldThrowExceptionIfAmountAreInvalid($amount): void
    {
        // Arrange
        $sourceCode = 'TWD';
        $targetCode = 'JPY';

        // Assert
        $this->expectException(InvalidArgumentException::class);

        $this->mock(CurrencyRepository::class)
            ->shouldReceive('getCodeList')
            ->twice()
            ->andReturn(['TWD', 'JPY']);

        // Act
        /** @var CurrencyExchangeService $target */
        $target = $this->app->make(CurrencyExchangeService::class);
        $target->exchangeRate($sourceCode, $targetCode, $amount);
    }

    public static function provideInvalidCurrencyAmount(): iterable
    {
        yield 'amount 是字串' => ['whatever'];
        yield 'amount 小數點太長' => ['12312.123'];
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

        $mockRepository = $this->mock(CurrencyRepository::class);
        $mockRepository->shouldReceive('getCodeList')
            ->twice()
            ->andReturn(['TWD', 'JPY']);
        $mockRepository->shouldReceive('getRatesByCode')
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
