<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Kandang;
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
}
