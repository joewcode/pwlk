<?php

namespace App\PWServer\Models;

use App\PWServer\Models\Bonuses;

class BonusInfo {
	public $count=0;		// Int32 количество бонусов
	public $bonus;			// Array of Bonuses

	public function __construct() {
		$this->bonus = array();
	}

	function ReadBonusInfo($p){
		$this->count = $p->ReadInt32(false);
		for ($i=0; $i<$this->count; $i++){
			$this->bonus[$i] = new Bonuses();
			$this->bonus[$i]->id = $p->ReadInt32(false);
			$this->bonus[$i]->type = 0;
			//echo '<br>';
			//printf("BonusId: %d, BonusCount: %d<br><br>", $this->bonus[$i]->id, $this->count);
			if ($this->bonus[$i]->id & b_func1) {
				$this->bonus[$i]->type ^= b_func1;
				$this->bonus[$i]->id ^= b_func1;
			}
			if ($this->bonus[$i]->id & b_func2) {
				$this->bonus[$i]->type ^= b_func2;
				$this->bonus[$i]->id ^= b_func2;
			}
			if ($this->bonus[$i]->id & b_func3) {
				$this->bonus[$i]->type ^= b_func3;
				$this->bonus[$i]->id ^= b_func3;
			}
			if ($this->bonus[$i]->id & b_func4) {
				//echo ($this->bonus[$i]->type ^ b_func4).' - 2<br><br>';
				$this->bonus[$i]->type ^= b_func4;
				$this->bonus[$i]->id ^= b_func4;
			}
			if ($this->bonus[$i]->id & b_func5) {
				//echo b_func5.' - 3<br><br>';
				$this->bonus[$i]->type ^= b_func5;
				$this->bonus[$i]->id ^= b_func5;
			}
			if ($this->bonus[$i]->id != 410 && $this->bonus[$i]->id != 336 && $this->bonus[$i]->id != 472) $this->bonus[$i]->stat = $p->ReadInt32(false);
			if ($this->bonus[$i]->type & b_func4) $this->bonus[$i]->dopstat = $p->ReadInt32(false);
			if ($this->bonus[$i]->type & b_func4 && $this->bonus[$i]->type & b_func5) $this->bonus[$i]->dopstat1 = $p->ReadInt32(false);
		}
	}

	function WriteBonusInfo($p){		
		$p->WriteInt32(count($this->bonus),false);
		foreach ($this->bonus as $i => $val){
			$p->WriteInt32($val->id+$val->type,false);
			if ($val->id != 410 && $val->id != 336 && $val->id != 472) $p->WriteInt32($val->stat, false);
			if ($val->type & b_func4) $p->WriteInt32($val->dopstat,false);
			if ($val->type & b_func4 && $val->type & b_func5) $p->WriteInt32($val->dopstat1,false);
		}
	}
}
