<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Konsumen extends Model
{
    public function barang() {
        return $this->hasMany('App\KonsumenBarang', 'konsumen_id', 'id');
    }
    
    public function branch() {
        return $this->hasMany('App\KonsumenBranch', 'konsumen_id', 'id');
    }
}
