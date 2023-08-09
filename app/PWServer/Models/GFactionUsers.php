<?php

namespace App\PWServer\Models;

class GFactionUsers {
	public $count = 0;	//CuInt32
	public $members;	//array of GFactionUser	

	public function __construct() {
		$this->members = array();
	}
}

