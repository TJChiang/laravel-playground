<?php

namespace Tests\Feature;

use App\Http\Controllers\CurrencyExchangeGet;
use App\Models\CurrencyRate;
use App\Services\CurrencyExchangeService;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Testing\DatabaseTransactions;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\Attributes\TestDox;
use Tests\TestCase;

#[CoversClass(CurrencyExchangeGet::class)]
class CurrencyExchangeGetTest extends TestCase
{
    use DatabaseTransactions;

    #[Test]
    #[TestDox('測試貨幣代碼參數不合法，回傳錯誤訊息')]
    #[DataProvider('provideInvalidCurrencyCode')]
    public function shouldReturnErrorWhenCurrencyCodeAreInvalid(mixed $targetCode, mixed $sourceCode): void
    {
        // Arrange
        CurrencyRate::factory(2)->sequence(
            ['currency_code' => 'TWD'],
            ['currency_code' => 'JPY']
        )->create();
        $payload = [
            'source' => $sourceCode,
            'target' => $targetCode,
            'amount' => '12,456.02',
        ];

        // Act & Assert
        $this->getJson(route('api.currency_exchange.get', $payload))
            ->assertStatus(422)
            ->assertJsonFragment([
                'msg' => 'error',
            ]);
    }

    public static function provideInvalidCurrencyCode(): iterable
    {
        yield 'source 是數字' => [
            1234.123,
            'TWD',
        ];
        yield 'source 是陣列' => [
            [],
            'TWD',
        ];
        yield 'source 無法辨識' => [
            'What',
            'TWD',
        ];
        yield 'target 是數字' => [
            'TWD',
            123123,
        ];
        yield 'target 是陣列' => [
            'TWD',
            123123,
        ];
        yield 'target 無法辨識' => [
            'TWD',
            'What',
        ];
        yield 'source 與 target 相同' => [
            'TWD',
            'TWD',
        ];
    }

    #[Test]
    #[TestDox('測試金額參數不合法，回傳錯誤訊息')]
    #[DataProvider('provideInvalidAmount')]
    public function shouldReturnErrorWhenAmountIsInvalid(mixed $amount): void
    {
        // Arrange
        CurrencyRate::factory(2)->sequence(
            ['currency_code' => 'TWD'],
            ['currency_code' => 'JPY']
        )->create();
        $payload = [
            'source' => 'TWD',
            'target' => 'JPY',
            'amount' => $amount,
        ];

        // Act & Assert
        $this->getJson(route('api.currency_exchange.get', $payload))
            ->assertStatus(422)
            ->assertJsonFragment([
                'msg' => 'error',
            ]);
    }

    public static function provideInvalidAmount(): iterable
    {
        yield 'amount 是 null' => [null];
        yield 'amount 是空值' => [''];
        yield 'amount 是陣列' => [[]];
        yield 'amount 是負數' => [-123.12];
        yield 'amount 是字串負數' => ['-123.12'];
        yield 'amount 小數點太多位' => [3.14159];
        yield 'amount 字串小數點太多位' => ['3.14159'];
        // yield 'amount 0 開頭' => [03.14];
        yield 'amount 字串 0 開頭' => ['03.14'];
        // yield 'amount 字串 0 開頭 + 千分位' => ['0,244.01'];
        yield 'amount 千分號錯誤' => ['2,44.01'];
        yield 'amount 千分號開頭' => [',244.01'];
        yield 'amount 千分號在小數位' => ['244.0,1'];
        yield 'amount 千分號接連小數點' => ['244,.01'];
        yield 'amount 連兩個千分號' => ['12,,244.01'];
        yield 'amount 千分號在小數最後面' => ['244.01,'];
        yield 'amount 千分號在整數最後面' => ['244,'];
    }

    #[Test]
    #[TestDox('測試貨幣代碼不存在，回傳錯誤訊息')]
    public function shouldThrowExceptionWhenCurrencyCodeNotFound(): void
    {
        // Arrange
        $source = 'JPY';
        $target = 'TWD';
        $amount = '12,332.23';
        $payload = [
            'source' => $source,
            'target' => $target,
            'amount' => $amount,
        ];
        CurrencyRate::factory(2)->sequence(
            ['currency_code' => 'TWD'],
            ['currency_code' => 'JPY']
        )->create();

        $mockService = $this->mock(CurrencyExchangeService::class);
        $mockService->shouldReceive('exchangeRate')
            ->with($source, $target, '12332.23')
            ->once()
            ->andThrow(ModelNotFoundException::class);

        // Act & Assert
        $this->getJson(route('api.currency_exchange.get', $payload))
            ->assertNotFound()
            ->assertJsonFragment([
                'msg' => 'error',
            ]);
    }

    #[Test]
    #[TestDox('測試貨幣換匯成功，回傳結果')]
    public function shouldReturnResultWhenExchangeSucceed(): void
    {
        // Arrange
        $source = 'JPY';
        $target = 'TWD';
        $amount = '12,332.23';
        $expected = '123,456.12';
        $payload = [
            'source' => $source,
            'target' => $target,
            'amount' => $amount,
        ];
        CurrencyRate::factory(2)->sequence(
            ['currency_code' => 'TWD'],
            ['currency_code' => 'JPY']
        )->create();

        $this->mock(CurrencyExchangeService::class)
            ->shouldReceive('exchangeRate')
            ->with($source, $target, '12332.23')
            ->once()
            ->andReturn($expected);

        // Act & Assert
        $this->getJson(route('api.currency_exchange.get', $payload))
            ->assertOk()
            ->assertJson([
                'msg' => 'success',
                'amount' => $expected,
            ]);
    }
}
