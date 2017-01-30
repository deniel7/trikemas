<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysRoleDtl extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sys_role_dtl';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id_hdr', 'id_menu'];
    
    // disable timestamps
    public $timestamps = false;
    
    public function header() {
        return $this->belongsTo('App\SysRole', 'id', 'id_hdr');
    }
    
    public function menu() {
        return $this->belongsTo('App\SysMenu', 'id_menu', 'id_menu');
    }
}
