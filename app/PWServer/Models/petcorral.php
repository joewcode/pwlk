<?php

namespace App\PWServer\Models;

use App\PWServer\PacketStream;
use App\PWServer\Models\pet;

class petcorral {
	public $capacity;			// int32
	public $count;			// byte
	public $list;			// array of pet
	
	function ReadPetCorral($data) {
		$p = new PacketStream($data);
		$this->capacity	= $p->ReadInt32();
		$this->count	= $p->ReadByte();
		$this->list	= array();
		for ($a=0; $a<$this->count; $a++){
			$this->list[$a] = new pet();
			$this->list[$a]->ReadPet($p);
		}
		$res=0;
		if ($p->done!=true) 	$res = 1;
		if ($p->overflow==true) $res = 2;
		return $res;
	}
	
	function WritePetCorral(){
		$p = new PacketStream();
		$p->WriteInt32($this->capacity);
		$p->WriteByte(count($this->list));
		foreach ($this->list as $i => $val){
			$this->list[$i]->WritePet($p);
		}
		return $p->buffer;
	}
}
