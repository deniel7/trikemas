<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class DetailPenjualan extends Model
{
    public function header() {
        return $this->belongsTo('App\InvoicePenjualan', 'id', 'invoice_penjualan_id');
    }
}
