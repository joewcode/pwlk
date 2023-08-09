<?php

namespace App\PWServer\Models;

class GTerritory {
	public $id;			// Int16
	public $level;			// Int16
	public $owner;			// Int32
	public $occupy_time;		// Int32
	public $challenger;		// Int32
	public $deposit;		// Int32
	public $cutoff_time;		// Int32
	public $battle_time;		// Int32
	public $bonus_time;		// Int32
	public $color;			// Int32
	public $status;		// Int32
	public $timeout;		// Int32
	public $maxbonus;		// Int32
	public $challenge_time;	// Int32
	public $challengerdetails;	// Octets
	public $reserved1;		// byte
	public $reserved2;		// byte
	public $reserved3;		// byte

}
