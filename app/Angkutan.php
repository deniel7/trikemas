<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Angkutan extends Model
{
    public function tujuan() {
        return $this->hasMany('App\AngkutanTujuan', 'angkutan_id', 'id');
    }
}
