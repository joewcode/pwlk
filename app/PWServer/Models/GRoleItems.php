<?php

namespace App\PWServer\Models;

class GRoleItems {
	public $count=0;		// Byte
	public $items;			// array GRoleInventory

	function GRoleItems() {
		$this->items = array();
	}
}
