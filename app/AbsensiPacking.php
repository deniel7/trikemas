<?php

namespace app;

use Illuminate\Database\Eloquent\Model;

class AbsensiPacking extends Model
{
    protected $fillable = ['id', 'tanggal', 'bagian', 'jenis', 'jumlah', 'karyawan_id'];

    public function karyawan()
    {
        return $this->belongsTo('App\Karyawan');
    }
}
