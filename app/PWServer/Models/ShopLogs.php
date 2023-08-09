<?php

namespace App\PWServer\Models;

use App\PWServer\Models\ShopLog;


class ShopLogs {
	public $count;		// CuInt32
	public $logs;		// array of ShopLog
	
	function ReadLogs( $a ) {
		$this->count = $a->ReadCUInt32();
		$this->logs = array();
		for ($i=0; $i < $this->count; $i++) {
			$this->logs[$i] = new ShopLog();
			$this->logs[$i]->roleid = $a->ReadInt32();	
			$this->logs[$i]->order_id = $a->ReadInt32();
			$this->logs[$i]->item_id = $a->ReadInt32();
			$this->logs[$i]->expire = $a->ReadInt32();
			$this->logs[$i]->item_count = $a->ReadInt32();
			$this->logs[$i]->order_count = $a->ReadInt32();
			$this->logs[$i]->cash_need = $a->ReadInt32();
			$this->logs[$i]->time = $a->ReadInt32();
			$this->logs[$i]->guid1 = $a->ReadInt32();
			$this->logs[$i]->guid2 = $a->ReadInt32();		
		}
	}

	function WriteLogs( $a ) {
		$a->WriteCUint32(count($this->logs));
		foreach ($this->logs as $i => $val){
			$a->WriteInt32($val->roleid);	
			$a->WriteInt32($val->order_id);
			$a->WriteInt32($val->item_id);
			$a->WriteInt32($val->expire);
			$a->WriteInt32($val->item_count);
			$a->WriteInt32($val->order_count);
			$a->WriteInt32($val->cash_need);
			$a->WriteInt32($val->time);
			$a->WriteInt32($val->guid1);
			$a->WriteInt32($val->guid2);		
		}
	}
}

