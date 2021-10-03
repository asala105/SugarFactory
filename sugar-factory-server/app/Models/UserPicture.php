<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Model;

class UserPicture extends Model{
	protected $table = "user_pictures";


	public function user(){
		return $this->belongsTo(User::class);
	}

	protected $fillable = [
        'user_id',
        'picture_url',
        'is_profile_picture',
        'is_approved'
    ];



}


?>