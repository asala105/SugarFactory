<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserInterest extends Model{
	protected $table = "user_interests";
	


	public function user(){
		return $this->belongsTo(User::class);
	}


}


?>