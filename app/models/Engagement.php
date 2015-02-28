<?php

class Engagement extends Eloquent {

	public function User(){
		return $this->belongsToMany('User');
	}

}