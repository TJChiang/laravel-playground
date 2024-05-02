<?php

namespace Tests\Feature;

use App\Http\Controllers\CurrencyExchangeGet;
use PHPUnit\Framework\Attributes\CoversClass;
use Tests\TestCase;

#[CoversClass(CurrencyExchangeGet::class)]
class CurrencyExchangeGetTest extends TestCase
{
    /**
     * A basic feature test example.
     */
    public function test_example(): void
    {
        $response = $this->get('/');

        $response->assertStatus(200);
    }
}
