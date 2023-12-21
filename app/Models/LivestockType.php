<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Kandang;

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
}
