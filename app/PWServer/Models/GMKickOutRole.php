<?php

namespace App\PWServer\Models;

use App\PWServer\PacketStream;

class GMKickOutRole {
	private $type;		    // cuInt32
	private $answlen;	    // cuInt32
	public $retcode;		// Int32
	public $gmroleid;		// Int32
	public $localsid;		// Int32
	public $kickroleid; 	// Int32
	public $error = 0;		// Ошибка при разборке пакета

	public function KickOutRole( $gmroleid, $localsid, $kickroleid, $forbid_time, $reason, $fp ) {
		$p = new PacketStream();
		$p->WriteInt32($gmroleid);
		$p->WriteInt32($localsid);
		$p->WriteInt32($kickroleid);
		$p->WriteInt32($forbid_time);
		$p->WriteString($reason);
		$packet = cuint(360).$p->wcount.$p->buffer;
		if ($fp) {
			fputs($fp, $packet);
			$data = fread($fp, 8096);
			$a = new PacketStream($data);
			$this->type = $a->ReadCUInt32();
			$this->answlen = $a->ReadCUInt32();
			$this->retcode = $a->ReadInt32();
			$this->gmroleid = $a->ReadInt32();	
			$this->localsid = $a->ReadInt32();	
			$this->kickroleid = $a->ReadInt32();	
			$this->error = 0;
			if ($a->done!=true) $this->error = 1;		// Пакет разобран не до конца
			if ($a->overflow==true) $this->error = 2;	// Длинна пакета меньше ожидаемой
			return $data;
		}
	}
}
