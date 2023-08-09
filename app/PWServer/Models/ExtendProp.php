<?php

namespace App\PWServer\Models;

use App\PWServer\PacketStream;

class ExtendProp {
	public $vitality;		// Int	выносливость
	public $energy;		// Int  интеллект
	public $strength;		// Int  сила
	public $agility;		// Int  ловкость
	public $max_hp;		// Int
	public $max_mp;		// Int
	public $hp_gen;		// Int
	public $mp_gen;		// Int
	public $walk_speed;		// Float
	public $run_speed;		// Float
	public $swim_speed;		// Float
	public $flight_speed;		// Float
	public $attack;		// Int
	public $damage_low;		// Int
	public $damage_high;		// Int
	public $attack_speed;		// Int
	public $attack_range;		// Float
	public $addon_damage_low;	// array 1-5 of Int
	public $addon_damage_high;	// array 1-5 of Int
	public $damage_magic_low;	// Int
	public $damage_magic_high;	// Int
	public $resistance;		// array 1-5 of Int
	public $defense;		// Int
	public $armor;			// Int
	public $max_ap;		// Int

	function ReadProperty($data){
		$p = new PacketStream($data);
		$this->vitality = $p->ReadInt32(false);
		$this->energy = $p->ReadInt32(false);
		$this->strength = $p->ReadInt32(false);
		$this->agility = $p->ReadInt32(false);
		$this->max_hp = $p->ReadInt32(false);
		$this->max_mp = $p->ReadInt32(false);
		$this->hp_gen = $p->ReadInt32(false);
		$this->mp_gen = $p->ReadInt32(false);
		$this->walk_speed = $p->ReadSingle(false);
		$this->run_speed = $p->ReadSingle(false);
		$this->swim_speed = $p->ReadSingle(false);
		$this->flight_speed = $p->ReadSingle(false);
		$this->attack = $p->ReadInt32(false);
		$this->damage_low = $p->ReadInt32(false);
		$this->damage_high = $p->ReadInt32(false);
		$this->attack_speed = $p->ReadInt32(false);
		$this->attack_range = $p->ReadSingle(false);
		$this->addon_damage_low = array($p->ReadInt32(false),$p->ReadInt32(false),$p->ReadInt32(false),$p->ReadInt32(false),$p->ReadInt32(false));
		$this->addon_damage_high = array($p->ReadInt32(false),$p->ReadInt32(false),$p->ReadInt32(false),$p->ReadInt32(false),$p->ReadInt32(false));
		$this->damage_magic_low = $p->ReadInt32(false);
		$this->damage_magic_high = $p->ReadInt32(false);
		$this->resistance = array($p->ReadInt32(false),$p->ReadInt32(false),$p->ReadInt32(false),$p->ReadInt32(false),$p->ReadInt32(false));
		$this->defense = $p->ReadInt32(false);
		$this->armor = $p->ReadInt32(false);
		$this->max_ap = $p->ReadInt32(false);
		$error = 0;
		if ($p->done!=true) $error = 1;		// Пакет разобран не до конца
		if ($p->overflow==true) $error = 2;	// Длинна пакета меньше ожидаемой
		return $error;	
	}

	function WriteProperty() {
		$p = new PacketStream();
		$p->WriteInt32($this->vitality,false);
		$p->WriteInt32($this->energy,false);
		$p->WriteInt32($this->strength,false);
		$p->WriteInt32($this->agility,false);
		$p->WriteInt32($this->max_hp,false);
		$p->WriteInt32($this->max_mp,false);
		$p->WriteInt32($this->hp_gen,false);
		$p->WriteInt32($this->mp_gen,false);
		$p->WriteSingle($this->walk_speed,false);
		$p->WriteSingle($this->run_speed,false);
		$p->WriteSingle($this->swim_speed,false);
		$p->WriteSingle($this->flight_speed,false);
		$p->WriteInt32($this->attack,false);
		$p->WriteInt32($this->damage_low,false);
		$p->WriteInt32($this->damage_high,false);
		$p->WriteInt32($this->attack_speed,false);
		$p->WriteSingle($this->attack_range,false);
		foreach ($this->addon_damage_low as $i => $val){
			$p->WriteInt32($val, false);
		}
		foreach ($this->addon_damage_high as $i => $val){
			$p->WriteInt32($val, false);
		}
		$p->WriteInt32($this->damage_magic_low,false);
		$p->WriteInt32($this->damage_magic_high,false);
		foreach ($this->resistance as $i => $val){
			$p->WriteInt32($val, false);
		}
		$p->WriteInt32($this->defense,false);
		$p->WriteInt32($this->armor,false);
		$p->WriteInt32($this->max_ap,false);
		return $p->buffer;
	}
}
