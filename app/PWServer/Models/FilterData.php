<?php

namespace App\PWServer\Models;

use App\PWServer\PacketStream;
use App\PWServer\Models\Filter;

class FilterData {
	public $count=0;	// int32
	public $filters;	// array of Filter;

	function ReadFilters($data){
		$this->filters = array();
		$p = new PacketStream($data);
		$this->count = $p->ReadInt32(false);
		for ($i=0; $i<$this->count; $i++){
			$this->filters[$i] = new Filter();
			$this->filters[$i]->id = $p->ReadInt32(false);
			$this->filters[$i]->param1 = $p->ReadByte();
			$this->filters[$i]->param2 = $p->ReadByte();
			$this->filters[$i]->param3 = $p->ReadByte();
			$this->filters[$i]->param4 = $p->ReadByte();
			$this->filters[$i]->showid = $p->ReadInt32(false);
			$this->filters[$i]->param5 = $p->ReadByte();
			$this->filters[$i]->param6 = $p->ReadByte();
			$this->filters[$i]->time = $p->ReadInt32(false);
			if ($this->filters[$i]->id==215) 
				$this->filters[$i]->lvl = $p->ReadInt16(false); else
			$this->filters[$i]->lvl = $p->ReadInt32(false);
		}
		$res = 0;
		if (!$p->done) $res = 1;
		if ($p->overflow) $res = 2;
		return $res;
	}

	function WriteFilters(){
		$p = new PacketStream();
		$this->count = count($this->filters);
		$p->WriteInt32($this->count,false);
		foreach ($this->filters as $i => $val){
			$p->WriteInt32($val->id,false);
			$p->WriteByte($val->param1);
			$p->WriteByte($val->param2);
			$p->WriteByte($val->param3);
			$p->WriteByte($val->param4);
			$p->WriteInt32($val->showid,false);
			$p->WriteByte($val->param5);
			$p->WriteByte($val->param6);
			$p->WriteInt32($val->time,false);
			if ($val->id==215)
				$p->WriteInt16($val->lvl,false); else
			$p->WriteInt32($val->lvl,false);
		}
		return $p->buffer;
	}

	function AddFilter($id,$time,$lvl){		
		$f=false;
		foreach ($this->filters as $i => $val){		// Проверка айди на наличие в списке текущих фильтров
			if ($val->id==$id) {
				$c=$i;
				$f=true;
			}
		}
		if ($f==false){
			$this->count++;
			$c = $this->count;
			$this->filters[$c] = new Filter();
			$this->filters[$c]->id		=	$id;
			$this->filters[$c]->param1	=	4;
			$this->filters[$c]->param2	=	0;
			$this->filters[$c]->param3	=	34;
			$this->filters[$c]->param4	=	0;
			$this->filters[$c]->showid	=	$id;
			$this->filters[$c]->param5	=	0;
			$this->filters[$c]->param6	=	0;
			$this->filters[$c]->time	=	$time;
			$this->filters[$c]->lvl		=	$lvl;
		} else {
			$this->filters[$c]->time	+=	$time;
		}
	}
}

