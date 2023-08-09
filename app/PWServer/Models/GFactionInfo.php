<?php

namespace App\PWServer\Models;

class GFactionInfo {
	var	$fid = 0;		// Int32
	var	$name = '';		// Octets
	var	$level = 0;		// Byte
	var	$masterid = 0;		// Int32
	var	$masterrole = 0;	// Byte
	var	$members;		// array of GMember
	var	$announce = '';		// Octets
	var	$sysinfo = '';		// Octets
}
