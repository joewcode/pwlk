<?php

namespace App\PWServer\Models;

use App\PWServer\PacketStream;

class GRoleStorehouse {
	private $type;			// cuInt32
	private $answlen;		// cuInt32
	public $localsid;		// Int32
	public $retcode = -1;	// Int32
	public $storehouse;		// RoleStorehouse
	public $error = 0;		// Ошибка при разборке пакета

	function PutRoleStorehouse( $id, $fp, $autosortitems = false ) {
		$p = new PacketStream();
		$p->WriteInt32(2147483649);
		$p->WriteInt32($id);
		$p->WriteRoleStorehouse($this->storehouse);		
		$packet = cuint(3026).$p->wcount.$p->buffer;
		if ( $fp ) {
			fputs($fp, $packet);
			$data = fread($fp, 8196);
			$a = new PacketStream($data);
			$this->type = $a->ReadCUInt32();
			$this->answlen = $a->ReadCUInt32();
			while ((strlen($a->buffer)-$a->pos)<$this->answlen) { 
				$data.= fread($fp, 8196);				
				$a->PacketStream($data, false);
			}			
			$this->localsid = $a->ReadInt32();
			$this->retcode = $a->ReadInt32();
			$this->error = 0;
			if ($a->done!=true) $this->error = 1;		// Пакет разобран не до конца
			if ($a->overflow==true) $this->error = 2;	// Длинна пакета меньше ожидаемой			
			return $data;
		}
	}

	function GetRoleStorehouse( $id, $fp ) {
		$p = new PacketStream();
		$p->WriteInt32(2147483648);
		$p->WriteInt32($id);				
		$packet = cuint(3027).$p->wcount.$p->buffer;		
		if ( $fp ) {	
			fputs($fp, $packet);
			$data = ReadPWPacket($fp);			
			$a = new PacketStream($data);
			$this->type = $a->ReadCUInt32();
			$this->answlen = $a->ReadCUInt32();
			//while ((strlen($a->buffer)-$a->pos)<$this->answlen) { 
			//	$data.=fread($fp,8196);				
			//	$a->PacketStream($data,false);
			//}
			$this->localsid = $a->ReadInt32();
			$this->retcode = $a->ReadInt32();
			$this->storehouse = $a->ReadRoleStorehouse();
			$this->error = 0;
			if ($a->done!=true) $this->error = 1;		// Пакет разобран не до конца
			if ($a->overflow==true) $this->error = 2;	// Длинна пакета меньше ожидаемой
			//print_r($a);
			return $data;			
		}
	}
}
