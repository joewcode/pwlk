<?php

namespace App\PWServer\Models;

use App\PWServer\PacketStream;

class RealmDataOctets {
	var	$level=0;	// int32
	var	$exp=0;		// int32
	var	$reserved1=0;	// int32
	var	$reserved2=0;	// int32
	var	$retcode;		// Result reading

	function ReadOctets($data){
		$this->retcode=0;
		$p = new PacketStream($data);
		$this->level = $p->ReadInt32();
		$this->exp = $p->ReadInt32();
		$this->reserved1 = $p->ReadInt32();
		$this->reserved2 = $p->ReadInt32();
		if ($p->done==false) {
			$this->retcode=2;	// Пакет не разобран до конца
			return false;
		}
		if ($p->overflow==true) {
			$this->retcode=3;	// Длинна пакета меньше ожидаемой
			return false;
		}
	}

	function WriteOctets(){
		$p = new PacketStream();
		$p->WriteInt32($this->level);
		$p->WriteInt32($this->exp);
		$p->WriteInt32($this->reserved1);
		$p->WriteInt32($this->reserved2);
		return $p->buffer;
	}
}
