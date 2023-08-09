<?php

namespace App\PWServer\Models;

class RolePocket {
    public $capacity = 0;		// Int32
	public $timestamp = 0;		// Int32
	public $money = 0;		    // Int32
	public $items;			    // GRoleItems
	public $reserved1 = 0;		// int32
	public $reserved2 = 0;		// int32
}
