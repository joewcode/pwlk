<?php

namespace App\PWServer\Models;

use App\PWServer\PacketStream;
use App\PWServer\Models\SkillInfo;

class Skills {
	public $skills;	// array of SkillInfo
	public $count;		// количество скилов

	function ReadSkills($data){
		$this->skills = array();
		$p = new PacketStream($data);
		$this->count = $p->ReadInt32(false);
		for ($i=0; $i<$this->count; $i++){
			$this->skills[$i] = new SkillInfo();
			$this->skills[$i]->id = $p->ReadInt32(false);
			$this->skills[$i]->kraft = $p->ReadInt32(false);
			$this->skills[$i]->lvl = $p->ReadInt32(false);
		}
		if ($p->done && !$p->overflow) return true; 
        else return false;
	}

	function WriteSkills(){
		$p = new PacketStream();
		$this->count = count($this->skills);
		$p->WriteInt32($this->count,false);
		foreach ($this->skills as $i => $val){
			$p->WriteInt32($val->id,false);
			$p->WriteInt32($val->kraft,false);
			$p->WriteInt32($val->lvl,false);
		}
		return $p->buffer;
	}
}
