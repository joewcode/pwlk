<?php

namespace App\PWServer\Models;

class RoleStorehouse {
	public $capacity=0;		// Int32
	public $money=0;		// Int32
	public $items;			// GRoleItems
	public $reserved1=0;		// Int32	в 27+ нету
	public $reserved2=0;		// Int32	в 27+ нету
	// 27+
	public $size1=0;		// char		
	public $size2=0;		// char
	public $dress;			// GRoleItems
	public $material;		// GRoleItems
	public $reserved=0;		// Int32 27+  в 101+ в конце и short
	// 101+
	public $size3=0;		// char
	public $generalcard;		// GRoleItems
}
