<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysUserAttribute extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sys_user_attribute';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id_user', 'attribute_name', 'attribute_value'];
    
    // disable timestamps
    public $timestamps = false;
    
    public function header() {
        return $this->belongsTo('App\SysUser', 'id', 'id_user');
    }
}
