<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

/**
 * @property int id
 * @property string code 貨幣代碼
 * @property string name 貨幣單位名稱
 * @property string number 貨幣數字代碼
 */
class Currency extends Model
{
    use HasFactory;

    protected $table = 'currencies';
    protected $primaryKey = 'id';

    public $timestamps = false;

    protected $fillable = [
        'code',
        'name',
        'number',
    ];
}
