<?php

namespace App\PWServer\Models;

class RoleBase {
	public $version=1;		// Byte
	public $id=0;			// Int32
	public $name='';		// String
	public $race=0;			// int32
	public $cls=0;			// int32
	public $gender=0;		// byte
	public $custom_data='';	// octets
	public $config_data='';	// octets
	public $custom_stamp=0;	// int32
	public $status=0;		// byte
	public $delete_time=0;		// int32
	public $create_time=0;		// int32
	public $lastlogin_time=0;	// int32
	public $forbid;			// GRoleForbids
	public $help_states='';	// Octets
	public $spouse=0;		// int32
	public $userid=0;		// int32
	public $reserved2=0;		// int32	// 80+ no
	public $cross_data='';		// Octets	// 80+	
	public $reserved2_=0;		// byte		// 80+
	public $reserved3=0;		// byte		// 80+
	public $reserved4=0;		// byte		// 80+
}

