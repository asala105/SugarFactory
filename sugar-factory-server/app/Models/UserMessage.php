<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMessage extends Model{
	protected $table = "user_messages";
	

	public function user(){
		return $this->belongsTo(User::class);
	}




}


?>