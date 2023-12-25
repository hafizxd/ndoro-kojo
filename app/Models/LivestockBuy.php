<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LivestockBuy extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function seller() {
        return $this->belongsTo(Farmer::class, 'seller_id');
    }

    public function buyer() {
        return $this->belongsTo(Farmer::class, 'buyer_id');
    }
}
