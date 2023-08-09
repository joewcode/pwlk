<?php

namespace App\PWServer\Models;

class GRoleDetail {
	public $version=0;		// Byte
	public $id=0;			// Unsigned Int32
	public $userid=0;		// Unsigned Int32	// 1.4.4+
	public $status;		// RoleStatus
	public $name;			// String
	public $race=0;		// Int32
	public $cls=0;			// Int32
	public $spouse=0;		// Unsigned Int32
	public $gender=0;		// Byte
	public $create_time=0;		// Int32;
	public $lastlogin_time=0;	// Int32;		// 1.4.4+
	public $cash_add=0;		// Int32;		// 1.4.4+
	public $cash_total=0;		// Int32;
	public $cash_used=0;		// Int32;
	public $cash_serial=0;		// Int32;
	public $factionid=0;		// Unsigned Int32;
	public $factionrole=0;		// Int32
	public $custom_data='';	// Octets
	public $custom_stamp=0;	// Int32
	public $inventory;		// RolePocket
	public $equipment;		// RoleInventoryVector
	public $storehouse;		// RoleStorehouse
	public $task;			// RoleTask
	public $addiction='';		// Octets
	public $logs;			// ShopLogs
	// 27+
	public $bonus_add=0;		// int
	public $bonus_reward=0;	// int
	public $bonus_used=0;		// int
	public $referrer=0;		// int
	public $userstorehouse;	// RoleStorehouse
	public $taskcounter='';	// Octets
	// 60+
	public $factionalliance;	// GNET::GFactionDetail::GFactionAllianceVector
	public $factionhostile;	// GNET::GFactionDetail::GFactionHostileVector
	// 1.4.6(69)+
	public $mall_consumption=0;	// int
	// 1/.4.7(85)+
	public $src_zoneid=0;		// int
	// 145+
	public $unifid=0;		// int64
	// 156+
	public $vip_level=0;		// int
	public $score_add=0;		// int
	public $score_cost=0;		// int
	public $score_consume=0;	// int
	public $day_clear_stamp=0;	// int
	public $week_clear_stamp=0;	// int
	public $month_clear_stamp=0;	// int
	public $year_clear_stamp=0;	// int
	public $purchase_limit_data='';// Octets
	public $home_level=0;		// int
}
