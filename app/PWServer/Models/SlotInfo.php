<?php

namespace App\PWServer\Models;

class SlotInfo {
	public $SlotCount=0;		// Int16 Кол-во ячеек
	public $SlotFlag=0;		// Int16 Флаги ячеек
	public $SlotStone;		// Array of Int32 // ID камня в ячйке

	public function __construct() {
		$this->SlotStone = array();
	}

	function ReadSlotInfo( $p ) {		
		$this->SlotCount = $p->ReadInt16(false);
		$this->SlotFlag = $p->ReadInt16(false);
		for ($i=0; $i<$this->SlotCount; $i++){
			$this->SlotStone[$i]=$p->ReadInt32(false);
		}
	}

	function WriteSlotInfo( $p ) {
		$p->WriteInt16(count($this->SlotStone),false);
		$p->WriteInt16(count($this->SlotFlag),false);
		foreach ($this->SlotStone as $i => $val){
			$p->WriteInt32($val,false);
		}
	}
}
