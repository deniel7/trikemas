<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class AngkutanTujuan extends Model
{
    public function angkutan() {
        return $this->belongsTo('App\Angkutan', 'id', 'angkutan_id');
    }
    
    public function tujuan() {
        return $this->belongsTo('App\Tujuan', 'id', 'tujuan_id');
    }
}
