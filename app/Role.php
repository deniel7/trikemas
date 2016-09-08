<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    /**
     * Get the users record associated with the user.
     */
    public function users()
    {
        return $this->hasMany('App\User');
    }
}
