<?php

namespace App\PWServer\Models;

use App\PWServer\PacketStream;

class TaskComplete {
	public $tasks;		// array of int16
	public $count;		// int32

	function ReadTasks($data){
		$this->tasks = array();
		$this->count = 0;
		if ($data == '') return true;
		$p = new PacketStream($data);
		$this->count = $p->ReadInt32(false);
		if ($this->count > 0) {
			for ($i=0; $i<$this->count; $i++){
				$this->tasks[$i] = $p->ReadInt16(false);
			}
		}
		if ($p->done && !$p->overflow) return true; else return false;
	}

	function WriteTasks(){
		$p = new PacketStream();
		$this->count = count($this->tasks);
		$p->WriteInt32($this->count,false);
		foreach ($this->tasks as $i => $val){
			$p->WriteInt16($val,false);
		}
		return $p->buffer;
	}
}
