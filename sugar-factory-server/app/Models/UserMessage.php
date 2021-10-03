<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserMessage extends Model{
	protected $table = "user_messages";
	
	protected $fillable = [
		'sender_id',
		'reciever_id',
		'body',
		'is_approved',
		'is_read'
	];

	public function user(){
		return $this->belongsTo(User::class);
	}
}
?>