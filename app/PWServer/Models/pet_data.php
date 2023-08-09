<?php

namespace App\PWServer\Models;

use App\PWServer\PacketStream;
use App\PWServer\Models\pet_data_skills;

class pet_data {
	public $size;		//cuint

	public $honor_point;	//int
	public $hunger_gauge;	//int
	public $feed_period;	//int
	public $pet_tid;		//int
	public $pet_vis_tid;	//int
	public $pet_egg_tid;	//int
	public $pet_class;		//int
	public $hp_factor;		//float
	public $level;		//short
	public $color;		//unsigned short
	public $exp;		//int
	public $skill_point;	//int	
	public $is_bind;		//byte		
	public $unused;		//byte	
	public $name_len;		//unsigned short
	public $name;		//char[16]
	public $skills;		//array[0..15] of pet_data_skills
	
	function ReadPetData($p){
		$this->size		= $p->ReadCUInt32();
		$this->honor_point	= $p->ReadInt32(false);
		$this->hunger_gauge	= $p->ReadInt32(false);
		$this->feed_period	= $p->ReadInt32(false);
		$this->pet_tid		= $p->ReadInt32(false);
		$this->pet_vis_tid	= $p->ReadInt32(false);
		$this->pet_egg_tid	= $p->ReadInt32(false);
		$this->pet_class	= $p->ReadInt32(false);
		$this->hp_factor	= $p->ReadSingle(false);
		$this->level		= $p->ReadInt16(false);		
		$this->color		= $p->ReadInt16(false);
		$this->exp		= $p->ReadInt32(false);
		$this->skill_point	= $p->ReadInt32(false);		
		$this->is_bind		= $p->ReadByte();
		$this->unused		= $p->ReadByte();
		$this->name_len		= $p->ReadInt16(false);
		$this->name='';
		for ($a=0; $a<16; $a++) $this->name.= chr($p->ReadByte());
		$this->name = substr($this->name, 0, $this->name_len);
		$this->name = iconv("UTF-16", "UTF-8", $this->name);
		$this->skills = array();
		for ($a=0; $a<16; $a++) {
			$this->skills[$a] = new pet_data_skills();
			$this->skills[$a]->ReadPetDataSkills($p);
		}		
	}
	
	function WritePetData($p){
		$p1 = new PacketStream();
		$p1->WriteInt32($this->honor_point,false);
		$p1->WriteInt32($this->hunger_gauge,false);
		$p1->WriteInt32($this->feed_period,false);
		$p1->WriteInt32($this->pet_tid,false);
		$p1->WriteInt32($this->pet_vis_tid,false);
		$p1->WriteInt32($this->pet_egg_tid,false);
		$p1->WriteInt32($this->pet_class,false);
		$p1->WriteSingle($this->hp_factor,false);
		$p1->WriteInt16($this->level,false);
		$p1->WriteInt16($this->color,false);
		$p1->WriteInt32($this->exp,false);
		$p1->WriteInt32($this->skill_point,false);		
		$p1->WriteByte($this->is_bind);
		$p1->WriteByte($this->unused);
		$a=RTRIM($this->name,chr(0).chr(0));
		$a = iconv("UTF-8", "UTF-16LE", $a);		
		if (strlen($a)>16) $a=substr($a,0,16);
		$p1->WriteInt16(strlen($a),false);
		if (strlen($a)<16) {
			for ($q=strlen($a); $q<16; $q++) {
				$a.=chr(0);
			}			
		}		
		for ($i=0; $i<16; $i++) $p1->WriteByte(ord($a[$i]));
		for ($a=0; $a<16; $a++) {
			$this->skills[$a]->WritePetDataSkills($p1);
		}
		$p->buffer.=$p1->wcount.$p1->buffer;
	}
}
