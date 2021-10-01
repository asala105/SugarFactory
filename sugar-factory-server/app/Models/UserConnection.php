<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserConnection extends Model{
	protected $table = "user_connections";
	
	protected $fillable = ['user1_id','user2_id'];
}


?>