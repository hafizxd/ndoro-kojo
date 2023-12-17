<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Farmer;

class Kandang extends Model
{
    use HasFactory;

    protected $guarded = [];

    protected $table = 'kandang';

    public function farmer() {
        return $this->belongsTo(Farmer::class, 'farmer_id');
    }
}
