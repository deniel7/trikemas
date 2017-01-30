<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class SysRole extends Model
{
    /**
     * The table associated with the model.
     *
     * @var string
     */
    protected $table = 'sys_role';
    
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = ['rolename', 'description'];
    
    public function detail() {
        return $this->hasMany('App\SysRoleDtl', 'id_hdr', 'id');
    }
    
    public function user() {
        return $this->belongsTo('App\SysUser', 'rolename', 'rolename');
    }
    
    public function detailMenu() {
        $detail = $this->detail;
        $menu = array();
        foreach($detail as $item) {
            $menu[] = $item->id_menu;
        }
        return $menu;
    }
    
}
