<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Kandang;
use App\Models\Pakan;
use App\Models\Limbah;
use App\Models\LivestockBuy;
use App\Models\LivestockType;

class Livestock extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function livestockType() {
        return $this->belongsTo(LivestockType::class, 'type_id');
    }

    public function kandang() {
        return $this->belongsTo(Kandang::class, 'kandang_id');
    }

    public function pakan() {
        return $this->belongsTo(Pakan::class);
    }

    public function limbah() {
        return $this->belongsTo(Limbah::class);
    }

    public function livestockBuy() {
        return $this->hasOne(LivestockBuy::class);
    }
}
