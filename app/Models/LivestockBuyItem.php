<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use App\Models\Farmer;
use App\Models\Livestock;

class LivestockBuyItem extends Model
{
    use HasFactory;

    protected $guarded = [];

    public function livestock()
    {
        return $this->belongsTo(Livestock::class);
    }
}
