<?php

namespace App\PWServer\Models;

class GFactionUser {
	var $roleid=0;		//int32
	var $level=0;		//byte
	var $occupation=0;	//byte	
	var $froleid=0;		//byte
	var $loginday=0;	//short
	var $online_status=0;	//byte
	var $name='';		//String;
	var $nickname='';	//String;
	var $contrib=0;		//int32		 1.4.4+
	// 85+
	var $delayexpel=0;	//byte
	var $expeltime=0;	//int32
	// 145+
	var $reputation=0;	//int
	var $reincarn_times=0;	//byte
	var $gender=0;		//byte
	var $rank='';		//string
}
