<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserBlocked extends Model{
	protected $table = "user_blocked";
	
	protected $fillable = ['from_user_id','to_user_id'];


	public function user(){
		return $this->belongsToMany(User::class);
	}



}


?>