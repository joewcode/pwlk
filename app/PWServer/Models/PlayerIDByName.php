<?php

namespace App\PWServer\Models;

use App\PWServer\PacketStream;

class PlayerIDByName {
	private $type;		// cuInt32
	private $answlen;	// cuInt32	
	public $localsid;		// Int32
	public $rolename;		// String
	public $reason;		// Byte
	public $retcode;		// Int32
	public $roleid;		// Int32	
	public $error=0;		// Ошибка при разборке пакета

	function GetRoleId( $rolename, $fp, $reason = 0 ) {
		$this->rolename = $rolename;
		$this->reason = $reason;
		$p = new PacketStream();
		$p->WriteInt32(2147483648);
		$p->WriteString($rolename);
		if ( PROTOCOL_VER >= 27 ) $p->WriteByte($reason);
		//$p->WriteByte(0);
		$packet = cuint(3033).$p->wcount.$p->buffer;
		if ( $fp ) {
			fputs($fp, $packet);
			$data = fread($fp, 8096);
			$a = new PacketStream($data);
			$this->type = $a->ReadCUInt32();
			$this->answlen = $a->ReadCUInt32();			
			$this->localsid = $a->ReadInt32();
			$this->retcode = $a->ReadInt32();
			$this->roleid = $a->ReadInt32();			
			$this->error = 0;
			if ( $a->done != true ) $this->error = 1;
			if ( $a->overflow == true ) $this->error = 2;
			return $data;
		}
	}
}
