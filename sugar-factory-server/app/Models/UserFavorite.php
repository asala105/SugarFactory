<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserFavorite extends Model{
	protected $table = "user_favorites";
	
	protected $fillable = ['from_user_id','to_user_id'];

	public function user(){
		return $this->belongsToMany(User::class);
	}



}

	



?>