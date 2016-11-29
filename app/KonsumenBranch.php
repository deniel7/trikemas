<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class KonsumenBranch extends Model
{
    public function konsumen() {
        return $this->belongsTo('App\Konsumen', 'id', 'konsumen_id');
    }
}
