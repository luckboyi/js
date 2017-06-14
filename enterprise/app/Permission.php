<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

class Permission extends Model
{
    //

    // Permissions.php
	public function roles()
	{
	    return $this->belongsToMany(Role::class);
	}
 
}
