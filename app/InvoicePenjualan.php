<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class InvoicePenjualan extends Model
{
    protected $dates = ['tanggal', 'tgl_jatuh_tempo', 'tanggal_bayar', 'tanggal_bayar_angkutan'];
    
    public function detail() {
        return $this->hasMany('App\DetailPenjualan', 'invoice_penjualan_id', 'id');
    }
}
