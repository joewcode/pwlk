<?php

namespace App\PWServer\Models;

use App\PWServer\PacketStream;

class GRoleStatus {
	private $type;			// cuInt32
	private $answlen;		// cuInt32
	public $localsid;		// Int32
	public $retcode = -1;		// Int32
	public $status = 1;		// GRoleStatus
	public $error = 0;		// Ошибка при разборке пакета

	function PutRoleStatus( $id, $fp ) {
		$p = new PacketStream();
		$p->WriteInt32(2147483649);
		$p->WriteInt32($id);
		$p->WriteRoleStatus($this->status);
		$packet = cuint(3014).$p->wcount.$p->buffer;
		//$fp = fsockopen($ip, 29400);
		if ( $fp ) {
			fputs($fp, $packet);
			$data = fread($fp,8096);
			//fclose($fp);
			$a = new PacketStream($data);
			$this->type = $a->ReadCUInt32();
			$this->answlen = $a->ReadCUInt32();
			$this->localsid = $a->ReadInt32();
			$this->retcode = $a->ReadInt32();
			$this->error = 0;
			if ( $a->done != true ) $this->error = 1;		// Пакет разобран не до конца
			if ( $a->overflow == true ) $this->error = 2;	// Длинна пакета меньше ожидаемой
			return $data;
		}
	}

	function GetRoleStatus( $id, $fp ) {
		$p = new PacketStream();
		$p->WriteInt32(2147483648);
		$p->WriteInt32($id);				
		$packet = cuint(3015).$p->wcount.$p->buffer;		
		//$fp = fsockopen($ip, 29400);
		if ( $fp ) {	
			fputs($fp, $packet);
			$data = ReadPWPacket($fp);
			//fclose($fp);
			$a = new PacketStream($data);
			$this->type = $a->ReadCUInt32();
			$this->answlen = $a->ReadCUInt32();
			$this->localsid = $a->ReadInt32();
			$this->retcode = $a->ReadInt32();
			$this->status = $a->ReadRoleStatus();			
			$this->error = 0;
			if ( $a->done != true ) $this->error = 1;		// Пакет разобран не до конца
			if ( $a->overflow == true ) $this->error = 2;	// Длинна пакета меньше ожидаемой
			return $data;			
		}
	}
}
