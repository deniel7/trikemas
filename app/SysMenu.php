<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysMenu extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sys_menu';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['id_menu', 'title'];
    
    public function role() {
        return $this->hasMany('App\SysRoleDtl', 'id_menu', 'id_menu');
    }
}
