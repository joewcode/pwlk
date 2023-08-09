<?php

namespace App\PWServer\Models;

use App\PWServer\PacketStream;

class TaskData {
	public $tasks;
	public $count;		// Byte
	public $count1;	// Byte
	public $unk1;		// Int16
	public $count2;	// Int32
	public $data1;		// array[0..6] of array Int32
	public $data2;		// array of Int16

	function ReadTasks($data){
		$this->data1 = array();
		$this->data2 = array();
		$this->tasks = array();
		$this->count = 0;
		if ($data == '') return true;
		$p = new PacketStream($data);
		$this->count = $p->ReadByte();
		$this->count1 = $p->ReadByte();
		$this->unk1 = $p->ReadInt16(false);
		$this->count2 = $p->ReadInt32(false);
		for ($i=0; $i<$this->count; $i++){
			$this->tasks[$i] = $p->ReadInt16(false);
			$this->data1[$i] = array();
			for ($i1=0; $i1<7; $i1++){
				$this->data1[$i][$i1] = $p->ReadInt32();
			}			
			$this->data2[$i] = $p->ReadInt16();
		}
		if ($p->done && !$p->overflow) return true; else return false;
	}

	function WriteTasks(){
		$p = new PacketStream();
		$this->count = count($this->tasks);
		$p->WriteByte($this->count);
		$p->WriteByte($this->count1);
		$p->WriteInt16($this->unk1,false);
		$p->WriteInt32($this->count2,false);
		for ($i=0; $i<$this->count; $i++){
			$p->WriteInt16($this->tasks[$i],false);
			for ($i1=0; $i1<7; $i1++){
				$p->WriteInt32($this->data1[$i][$i1]);
			}
			$p->WriteInt16($this->data2[$i]);
		}
		return $p->buffer;
	}
}
