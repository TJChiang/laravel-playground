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
    #[TestDox('測試輸入參數不合法，回傳錯誤訊息')]
    #[DataProvider('provideInvalidArguments')]
    public function shouldReturnErrorWhenArgumentsAreInvalid(array $payload): void
    {
        // Arrange
        CurrencyRate::factory(2)->sequence(
            ['currency_code' => 'TWD'],
            ['currency_code' => 'JPY']
        )->create();

        // Act & Assert
        $this->getJson(route('api.currency_exchange.get', $payload))
            ->assertStatus(422)
            ->assertJsonFragment([
                'msg' => 'error',
            ]);
    }

    public static function provideInvalidArguments(): iterable
    {
        yield 'source 是數字' => [[
            'source' => 1234.123,
            'target' => 'TWD',
            'amount' => 123.12,
        ]];
        yield 'source 是陣列' => [[
            'source' => [],
            'target' => 'TWD',
            'amount' => 123.12,
        ]];
        yield 'source 無法辨識' => [[
            'source' => 'What',
            'target' => 'TWD',
            'amount' => 123.12,
        ]];
        yield 'target 是數字' => [[
            'source' => 'TWD',
            'target' => 123123,
            'amount' => 123.12,
        ]];
        yield 'target 是陣列' => [[
            'source' => 'TWD',
            'target' => 123123,
            'amount' => 123.12,
        ]];
        yield 'target 無法辨識' => [[
            'source' => 'TWD',
            'target' => 'What',
            'amount' => 123.12,
        ]];
        yield 'source 與 target 相同' => [[
            'source' => 'TWD',
            'target' => 'TWD',
            'amount' => 123.12,
        ]];
        yield 'amount 小數點太多位' => [[
            'source' => 'JPY',
            'target' => 'TWD',
            'amount' => 123.121,
        ]];
        yield 'amount 不是 numeric' => [[
            'source' => 'JPY',
            'target' => 'TWD',
            'amount' => 'whatever',
        ]];
        yield 'amount 小於 0' => [[
            'source' => 'JPY',
            'target' => 'TWD',
            'amount' => '-1',
        ]];
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
