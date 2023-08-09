<?php

namespace App\PWServer\Models;

class GRoleForbids {
	public $count = 0;	//byte
	public $forbids;	//array GRoleForbid
	
	public function __construct() {
		$this->forbids = array();
	}
}
