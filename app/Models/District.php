<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Regency;

class District extends Model
{
    use HasFactory;

    public function regencies() {
        return $this->belongsTo(Regency::class, 'regency_id');
    }
}
