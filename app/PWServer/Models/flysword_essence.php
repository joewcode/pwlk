<?php

namespace App\PWServer\Models;

use App\PWServer\PacketStream;

class flysword_essence {
	public $cur_time;		// int32
	public $max_time;		// uint32
	public $require_level;		// int16
	public $element_level; 	// int16
	public $require_class;		// int32
	public $time_per_element;	// uint32
	public $speed_increase1;	// float
	public $speed_increase2;	// float
	public $flag;			// byte
	public $creator;		// String
	public $unk;			// int16
	public $retcode;		// Ошибки

	function ReadFlyswordEssence( $data ) {
		$this->retcode=0;
		$p = new PacketStream($data);
		$this->cur_time = $p->ReadInt32(false);
		$this->max_time = $p->ReadInt32(false);
		$this->require_level = $p->ReadInt16(false);
		$this->element_level = $p->ReadInt16(false);
		$this->require_class = $p->ReadInt32(false);
		$this->time_per_element = $p->ReadInt32(false);
		$this->speed_increase1 = $p->ReadSingle(false);
		$this->speed_increase2 = $p->ReadSingle(false);
		$this->flag = $p->ReadByte();
		$this->creator = $p->ReadString();
		$this->creator = iconv("UTF-16", "UTF-8", $this->creator);
		$this->unk = $p->ReadInt16(false);
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
	
	function WriteFlyswordEssence(){
		$p = new PacketStream();
		$p->WriteInt32($this->cur_time,false);
		$p->WriteInt32($this->max_time,false);
		$p->WriteInt16($this->require_level,false);
		$p->WriteInt16($this->element_level,false);
		$p->WriteInt32($this->require_class,false);
		$p->WriteInt32($this->time_per_element,false);
		$p->WriteSingle($this->speed_increase1,false);
		$p->WriteSingle($this->speed_increase2,false);
		$p->WriteByte($this->flag);
		$a = iconv("UTF-8", "UTF-16", $this->creator);
		$p->WriteString($a);
		$p->WriteInt16($this->unk, false);
		return $p->buffer;
	}
}