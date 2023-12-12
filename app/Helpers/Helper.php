<?php

namespace App\Helpers;

use Illuminate\Support\Facades\DB;

function generateRandomCode($prefix, $table, $column) {
    $rand = $prefix . "_" . mt_rand(1000000000, 9999999999);

    $data = DB::table($table)->select('id')->where($column, $rand)->first();
    if (isset($data)) 
        return generateRandomCode($prefix, $table, $column);

    return $rand;
}