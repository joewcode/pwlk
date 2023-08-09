<?php

namespace App\PWServer\Models;

class RoleStatus {
	var 	$version=1;		// Byte
	var 	$level=0;		// Int32
	var 	$level2=0;		// Int32
	var 	$exp=0;			// Int32
	var 	$sp=0;			// Int32
	var 	$pp=0;			// Int32
	var 	$hp=0;			// Int32
	var 	$mp=0;			// Int32
	var 	$posx=0;		// Float
	var 	$posy=0;		// Float
	var 	$posz=0;		// Float
	var 	$worldtag=0;		// Int32
	var 	$invader_state=0;	// Int32
	var 	$invader_time=0;	// Int32
	var 	$pariah_time=0;		// Int32
	var 	$reputation=0;		// Int32
	var 	$custom_status='';	// Octets
	var 	$filter_data='';	// Octets
	var 	$charactermode='';	// Octets
	var 	$instancekeylist='';	// Octets
	var 	$dbltime_expire=0;	// Int32
	var 	$dbltime_mode=0;	// Int32
	var 	$dbltime_begin=0;	// Int32
	var 	$dbltime_used=0;	// Int32
	var 	$dbltime_max=0;		// Int32
	var 	$time_used=0;		// Int32
	var 	$dbltime_data='';	// Octets
	var 	$storesize=0;		// Int16
	var 	$petcorral='';		// Octets
	var 	$property='';		// Octets
	var 	$var_data='';		// Octets
	var 	$skills='';		// Octets
	var 	$storehousepasswd='';	// Octets	md5 from password
	var 	$waypointlist='';	// Octets
	var 	$coolingtime='';	// Octets
	var 	$npc_relation='';	// Octets	// 1.4.4+
	var 	$multi_exp_ctrl='';	// Octets	// 1.4.4+
	var 	$storage_task='';	// Octets	// 1.4.4+
	var 	$faction_contrib='';	// Octets	// 1.4.4+
	var 	$force_data='';		// Octets	// 1.4.5+
	var 	$online_award='';	// Octets	// 1.4.6(69)+
	var 	$profit_time_data='';	// Octets	// 1.4.6(69)+
	var 	$country_data='';	// Octets	// 70+
	var	$reserved1=0;		// Int32	// В 1.4.4+ нет
	var	$reserved2=0;		// Int32	// В 1.4.4+ нет
	var	$reserved31=0;		// Byte		// 1.4.5+ (в 70 нет)
	var	$reserved32=0;		// Int16	// 1.4.5+(в 69 нет)
	var	$reserved3=0;		// Int32	// В 1.4.5+ нет
	var	$reserved4=0;		// Int32
	var	$reserved5=0;		// Int32	// 1.4.4+
	// 85+ предыдущих reserved там нет except reserved5
	var 	$king_data='';		// Octets
	var 	$meridian_data='';	// Octets
	var 	$extraprop='';		// Octets
	var	$title_data='';		// Octets	// 88+
	var	$reserved43=0;		// Byte	
	// 101 +
	var	$reincarnation_data='';	// Octets
	var	$realm_data='';		// Octets
	// char reserved2, reserved3;
}

