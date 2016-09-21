<?php

namespace app;

use Illuminate\Database\Eloquent\Model;

class KaryawanHarian extends Model
{
    public function statusKaryawan()
    {
        return $this->belongsTo('App\StatusKaryawan');
    }
}
