<?php 

class Selfie extends Eloquent {

	public function User(){
		return $this->belongsTo('User');
	}

}