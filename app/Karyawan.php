<?php

namespace app;

use Illuminate\Database\Eloquent\Model;

class Karyawan extends Model
{
    public function statusKaryawan()
    {
        return $this->belongsTo('App\StatusKaryawan');
    }
}
