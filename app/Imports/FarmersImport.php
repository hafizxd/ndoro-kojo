<?php

namespace App\Imports;

use App\Models\Farmer;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Hash;
use Maatwebsite\Excel\Concerns\ToCollection;
use Maatwebsite\Excel\Concerns\WithHeadingRow;
use Illuminate\Support\Facades\DB;

class FarmersImport implements ToCollection, WithHeadingRow
{
    public function collection(Collection $rows)
    {
        set_time_limit(0);
        try {
            DB::beginTransaction();
            
            $iFarmerCode = '';
            $iKandangName = '';
            $kandangArr = [];
            $now = date('Y-m-d H:i:s');

            foreach ($rows as $row) 
            {
                if ($row['farmer_id'] != $iFarmerCode) {
                    $iFarmerCode = $row['farmer_id'];

                    $createArr = [
                        'code' => $row['farmer_id'],
                        'fullname' => $row['farmer_id'],
                        'username' => $row['farmer_id'],
                        'email' => $row['farmer_id'] . '@mail.com',
                        'password' => Hash::make('123456'),
                        'gender' => $row['farmer_gender'],
                        'age' => $row['farmer_age'],
                        'kelompok_ternak' => $row['kelompok_ternak_name'],
                        'created_at' => $now,
                        'updated_at' => $now
                    ];

                    if (isset($row['farmer_occupation'])) {
                        $createArr = array_merge($createArr, ['occupation' => explode(', ', $row['farmer_occupation'])[0]]);
                    }

                    $village = DB::table('villages')
                        ->select('villages.id as village_id', 'villages.district_id', 'districts.regency_id', 'regencies.province_id')
                        ->join('districts', 'villages.district_id', '=', 'districts.id')
                        ->join('regencies', 'districts.regency_id', '=', 'regencies.id')
                        ->where('villages.name', $row['village_name'])
                        ->where('districts.name', $row['district_name'])
                        ->first();

                    if (isset($village)) {
                        $createArr = array_merge($createArr, [
                            'province_id' => $village->province_id,
                            'regency_id' => $village->regency_id,
                            'district_id' => $village->district_id,
                            'village_id' => $village->village_id,
                        ]);
                    }
                    
                    $farmerId = DB::table('farmers')->insertGetId($createArr);                    
                }
                
                if ($iKandangName != $row['nama_kandang']) {
                    $iKandangName = $row['nama_kandang'];

                    $kandangInsert = [
                        'farmer_id' => $farmerId,
                        'name' => $row['nama_kandang'],
                        'panjang' => (float) $row['panjang_kandang'],
                        'lebar' => (float) $row['lebar_kandang'],
                        'luas' => (float) $row['luas_kandang'],
                        'jenis' => $row['jenis_kandang'],
                        'address' => $row['kandang_address'],
                        'rt_rw' => $row['kandang_rt_rw'],
                        'longitude' => $row['longitude'],
                        'latitude' => $row['latitude'],
                        'sensor_status' => $row['livestock_sensor_status']
                    ];
    
                    $village = DB::table('villages')
                        ->select('villages.id as village_id', 'villages.district_id', 'districts.regency_id', 'regencies.province_id')
                        ->join('districts', 'villages.district_id', '=', 'districts.id')
                        ->join('regencies', 'districts.regency_id', '=', 'regencies.id')
                        ->where('villages.name', $row['kandang_village_name'])
                        ->where('districts.name', $row['kandang_district_name'])
                        ->first();
    
                    if (isset($village)) {
                        $kandangInsert = array_merge($kandangInsert, [
                            'province_id' => $village->province_id,
                            'regency_id' => $village->regency_id,
                            'district_id' => $village->district_id,
                            'village_id' => $village->village_id,
                        ]);
                    }
    
                    $livestockTypeQ = DB::table('livestock_types')->where('livestock_type', $row['livestock_race'])->first();
                    if (isset($livestockTypeQ)) {
                        $livestockType = ($livestockTypeQ->level == 1) ? $livestockTypeQ->id :  $livestockTypeQ->parent_type_id;
                        $kandangInsert = array_merge($kandangInsert, [
                            'type_id' => $livestockType
                        ]);
                    }
    
                    $kandangId = DB::table('kandang')->insertGetId($kandangInsert);
                }

                // Livestock
                $livestockInsert = [
                    'kandang_id' => $kandangId,
                    'age' => strtoupper($row['livestock_age']),
                    'gender' => $row['livestock_gender'],
                    "acquired_status" =>  $row['livestock_acquired_status'],
                    "acquired_year" => $row['livestock_acquired_year'],
                    "acquired_month" => ($row['livestock_acquired_month'] < 10) ? "0".$row['livestock_acquired_month'] : $row['livestock_acquired_month'],
                    "acquired_month_name" => $row['livestock_acquired_month_name'],
                    "dead_type" => $row['livestock_dead_type'],
                    "dead_reason" => $row['livestock_dead_reason'],
                    "dead_year" => $row['livestock_dead_year'],
                    "dead_month" => ($row['livestock_dead_month'] < 10) ? "0".$row['livestock_dead_month'] : $row['livestock_dead_month'],
                    "dead_month_name" => $row['livestock_dead_month_name'],
                    "sold_year" => $row['livestock_sold_year'],
                    "sold_month" => ($row['livestock_sold_month'] < 10) ? "0".$row['livestock_sold_month'] : $row['livestock_sold_month'],
                    "sold_month_name" => $row['livestock_sold_month_name'],
                    "availability" =>  $row['livestock_availability']
                ];

                if (isset($row['jenis_pakan'])) {
                    $jenisPakan = explode(', ', $row['jenis_pakan'])[0];
                    $pakan = DB::table('pakan')->select('id')->where('jenis_pakan', $jenisPakan)->first();
                    if (isset($pakan)) {
                        $livestockInsert = array_merge($livestockInsert, ['pakan_id' => $pakan->id]);
                    }
                }

                if (isset($row['pengolahan_limbah'])) {
                    $pengolahanLimbah = explode(', ', $row['pengolahan_limbah'])[0];
                    $limbah = DB::table('limbah')->select('id')->where('pengolahan_limbah', $pengolahanLimbah)->first();
                    if (isset($limbah)) {
                        $livestockInsert = array_merge($livestockInsert, ['limbah_id' => $limbah->id]);
                    }
                }

                if (isset($row['livestock_race'])) {
                    $type = DB::table('livestock_types')->select('id')->where('livestock_type', $row['livestock_race'])->first();
                    if (isset($type)) {
                        $livestockInsert = array_merge($livestockInsert, ['type_id' => $type->id]);
                    }
                }

                DB::table('livestocks')->insert($livestockInsert);
            }

            DB::commit();
        } catch (\PDOException $e) {
            // Woopsy
            DB::rollBack();
            dd($e);
        }
    }
}
