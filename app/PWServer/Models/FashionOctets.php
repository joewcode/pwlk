<?php

namespace App\PWServer\Models;

use App\PWServer\PacketStream;

class FashionOctets {
	public $require_level=0;	// int32
	public $color=0;		// int16
	public $gender=0;		// int16
	public $name_type=0;		// byte
	public $name='';		// string
	public $color_mask;		// word
	public $retcode;		// Result reading

	function ReadOctets( $data ) {
		$this->retcode=0;
		$p = new PacketStream($data);
		$this->require_level = $p->ReadInt32(false);
		$this->color = $p->ReadInt16(false);
		$this->gender = $p->ReadInt16(false);
		$this->name_type = $p->ReadByte();
		$this->name = $p->ReadString();
		$this->color_mask = $p->ReadInt16(false);
		if ($p->done==false) {
			$this->retcode=2;	// Пакет не разобран до конца
			return false;
		}
		if ($p->overflow==true) {
			$this->retcode=3;	// Длинна пакета меньше ожидаемой
			return false;
		}
	}
}