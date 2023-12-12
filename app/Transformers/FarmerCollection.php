<?php

namespace App\Transformers;

use Illuminate\Http\Resources\Json\JsonResource;

class FarmerCollection extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @param  \Illuminate\Http\Request
     * @return array
     */
    public function toArray($request)
    {
        $resData = $this->resource->toArray();

        $isProfileComplete = false;
        if (isset($this->code) && 
            isset($this->fullname) &&
            isset($this->username) &&
            isset($this->email) &&
            isset($this->password) &&
            isset($this->phone) &&
            isset($this->address) &&
            isset($this->occupation) &&
            isset($this->gender) &&
            isset($this->age) &&
            isset($this->kelompok_ternak) &&
            isset($this->province_id) &&
            isset($this->regency_id) &&
            isset($this->district_id) &&
            isset($this->village_id)
        ) {
            $isProfileComplete = true;
        }
            
        $resData['is_profile_complete'] = $isProfileComplete;

        return $resData;
    }
}
