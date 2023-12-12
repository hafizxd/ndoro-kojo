<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class LivestockType extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function livestockChildren() {
        return $this->hasMany(LivestockType::class, 'parent_type_id');
    }
}
