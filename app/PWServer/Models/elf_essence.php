<?php

namespace App\PWServer\Models;

use App\PWServer\PacketStream;

class elf_essence {
	public $exp;			// uint32
	public $level;			// int16
	public $total_attribute;	// int16
	public $strength;		// int16
	public $agility;		// int16
	public $vitality;		// int16
	public $energy;		// int16
	public $total_genius;		// int16
	public $genius;		// array[0..4] of int16
	public $refine_level;		// int16
	public $stamina;		// int32
	public $status_value;		// int32
	public $equip;			// array of int32
	public $skills;		// array of elf_skill
	public $retcode;		// внутр. ошибки

	function WriteElfEssence() {
		$p = new PacketStream();
		$p->WriteInt32($this->exp,false);
		$p->WriteInt16($this->level,false);
		$p->WriteInt16($this->total_attribute,false);
		$p->WriteInt16($this->strength,false);
		$p->WriteInt16($this->agility,false);
		$p->WriteInt16($this->vitality,false);
		$p->WriteInt16($this->energy,false);
		$p->WriteInt16($this->total_genius,false);
		for ($a=0; $a<5; $a++){
			$p->WriteInt16($this->genius[$a],false);
		}
		$p->WriteInt16($this->refine_level,false);
		$p->WriteInt32($this->stamina,false);
		$p->WriteInt32($this->status_value,false);
		$p->WriteInt32(count($this->equip),false);
		if (count($this->equip)>0) {
			foreach ($this->equip as $i => $val) {
				$p->WriteInt32($val,false);
			}
		}
		$p->WriteInt32(count($this->skills),false);
		if (count($this->skills)>0) {
			foreach ($this->skills as $i => $val) {
				$p->WriteInt16($val->id,false);
				$p->WriteInt16($val->lvl,false);
			}
		}
		return $p->buffer;
	}

	function ReadElfEssence( $data ) {
		$this->retcode=0;
		$p = new PacketStream($data);
		$this->exp		= $p->ReadInt32(false);
		$this->level		= $p->ReadInt16(false);
		$this->total_attribute	= $p->ReadInt16(false);
		$this->strength		= $p->ReadInt16(false);
		$this->agility		= $p->ReadInt16(false);
		$this->vitality		= $p->ReadInt16(false);
		$this->energy		= $p->ReadInt16(false);
		$this->total_genius	= $p->ReadInt16(false);
		$this->genius = array();
		for ($a=0; $a<5; $a++){
			$this->genius[$a] = $p->ReadInt16(false);
		}
		$this->refine_level	= $p->ReadInt16(false);
		$this->stamina		= $p->ReadInt32(false);
		$this->status_value	= $p->ReadInt32(false);
		$equip_count		= $p->ReadInt32(false);
		$this->equip = array ();
		if ($equip_count>10) {
			$this->retcode=11;	// Слишком много эквипа
			return false;
		}
		if ($equip_count>0) {
			for ($a=0; $a<$equip_count; $a++) {
				$this->equip[$a] = $p->ReadInt32(false);
			}
		}
		$skill_count = $p->ReadInt32(false);
		$this->skills = array();
		if ($skill_count>10) {
			$this->retcode=10;	// Слишком много скилов
			return false;
		}
		if ($skill_count>0) {
			for ($a=0; $a<$skill_count; $a++) {
				$this->skills[$a] = new elf_skill();
				$this->skills[$a]->id = $p->ReadInt16(false);
				$this->skills[$a]->lvl = $p->ReadInt16(false);
			}
		}
		if ($p->done==false) {
			$this->retcode=2;	// Пакет не разобран до конца
			return false;
		}
		if ($p->overflow==true) {
			$this->retcode=3;	// Длинна пакета меньше ожидаемой
			return false;
		}
		return true;
	}
}

