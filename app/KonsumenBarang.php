<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KonsumenBarang extends Model
{
    public function barang() {
        return $this->belongsTo('App\Barang', 'id', 'barang_id');
    }
    
    public function konsumen() {
        return $this->belongsTo('App\Konsumen', 'id', 'konsumen_id');
    }
}
