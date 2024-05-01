<?php

namespace App\Models;

use Carbon\Carbon;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property string currency_code 貨幣代碼
 * @property float TWD_rate 台幣匯率
 * @property float JPY_rate 日幣匯率
 * @property float USD_rate 美金匯率
 * @property Carbon updated_at
 */
class CurrencyRate extends Model
{
    use HasFactory;

    protected $table = 'currency_rates';
    protected $primaryKey = 'id';
    protected $fillable = [
        'currency_code',
        'TWD_rate',
        'JPY_rate',
        'USD_rate',
    ];
}
