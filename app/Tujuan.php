<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Tujuan extends Model
{
    public function angkutan() {
        return $this->hasMany('App\AngkutanTujuan', 'tujuan_id', 'id');
    }
}
