<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\LivestockType;
use App\Models\Livestock;
use App\Models\Farmer;
use App\Models\Sensor;

class Kandang extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'kandang';

    public function farmer() {
        return $this->belongsTo(Farmer::class, 'farmer_id');
    }

    public function sensor() {
        return $this->hasOne(Sensor::class, 'kandang_id');
    }

    public function livestocks() {
        return $this->hasMany(Livestock::class, 'kandang_id');
    }

    public function livestockType() {
        return $this->belongsTo(LivestockType::class, 'type_id');
    }
}
