<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Farmer;
use App\Models\Kandang;
use App\Models\Livestock;
use App\Models\LivestockBuyItem;

class LivestockBuy extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function seller()
    {
        return $this->belongsTo(Farmer::class, 'seller_id');
    }

    public function buyer()
    {
        return $this->belongsTo(Farmer::class, 'buyer_id');
    }

    public function kandang()
    {
        return $this->belongsTo(Kandang::class, 'kandang_id');
    }

    public function items()
    {
        return $this->hasMany(LivestockBuyItem::class, 'livestock_buy_id');
    }
}
