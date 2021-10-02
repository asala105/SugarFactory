<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserType extends Model{
	protected $table = "user_types";
	

public function user(){
	return $this->belongsToMany(User::class);
}

}


?>