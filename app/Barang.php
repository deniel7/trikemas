<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Barang extends Model
{
    public function konsumen() {
        return $this->hasMany('App\KonsumenBarang', 'barang_id', 'id');
    }
}
