<?php

namespace App\PWServer\Models;

class pet_data_skills {
	var $skill;		//int
	var $level;		//int
	
	function ReadPetDataSkills($p){
		$this->skill	= $p->ReadInt32(false);
		$this->level	= $p->ReadInt32(false);
	}
	
	function WritePetDataSkills($p){
		$p->WriteInt32($this->skill,false);
		$p->WriteInt32($this->level,false);
	}
}
