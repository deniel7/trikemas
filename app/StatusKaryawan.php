<?php

namespace app;

use Illuminate\Database\Eloquent\Model;

class StatusKaryawan extends Model
{
    public function karyawans()
    {
        return $this->hasMany('App\Karyawan');
    }

    public static function findByStatusId($status)
    {
        $statuses = DB::table('status_karyawans')->where('id', strtoupper($status))->get();

        if (count($statuses) == 1) {
            return $statuses[1]->keterangan;
        } else {
            return 'NOT FOUND';
        }
    }
}
