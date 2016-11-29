<?php

namespace app;

use Illuminate\Database\Eloquent\Model;

class AbsensiHarian extends Model
{
    public function karyawan()
    {
        return $this->belongsTo('App\Karyawan');
    }
}
