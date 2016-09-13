<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Konsumen extends Model
{
    public function barang() {
        return $this->hasMany('App\KonsumenBarang', 'konsumen_id', 'id');
    }
}
