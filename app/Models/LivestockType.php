<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Kandang;
use App\Models\Livestock;

class LivestockType extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function livestockChildren() {
        return $this->hasMany(LivestockType::class, 'parent_type_id');
    }

    public function kandang() {
        return $this->hasMany(Kandang::class, 'type_id');
    }

    public function livestocks() {
        return $this->hasMany(Livestock::class, 'type_id');
    }

    public function livestocksThroughKandang() {
        return $this->hasManyThrough(
            Livestock::class, 
            Kandang::class,
            'type_id',
            'kandang_id',
            'id',
            'id'
        );
    }
}
