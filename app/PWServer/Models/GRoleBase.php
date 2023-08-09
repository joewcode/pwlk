<?php

namespace App\PWServer\Models;

use App\PWServer\PacketStream;

class GRoleBase {
	private $type;			// cuInt32
	private $answlen;		// cuInt32
	var	$localsid;		// Int32
	var	$retcode=-1;		// Int32
	var 	$base=1;		// RoleBase
	var	$error=0;		// Ошибка при разборке пакета

	function PutRoleBase( $id, $fp ) {
		$p = new PacketStream();
		$p->WriteInt32(2147483649);
		$p->WriteInt32($id);
		$p->WriteRoleBase($this->base);		
		$packet = cuint(3012).$p->wcount.$p->buffer;		
		if($fp) {
			fputs($fp, $packet);
			$data = fread($fp, 8096);
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
	
	function GetRoleBase( $id, $fp ) {
		$p=new PacketStream();
		$p->WriteInt32(2147483648);
		$p->WriteInt32($id);				
		$packet=cuint(3013).$p->wcount.$p->buffer;		
		if ( $fp ) {	
			fputs($fp, $packet);
			$data = ReadPWPacket($fp);
			$a=new PacketStream($data);
			$this->type = $a->ReadCUInt32();
			$this->answlen = $a->ReadCUInt32();
			//if ((strlen($a->buffer)-$a->pos)<$this->answlen) { 
			//	$data.=fread($fp,16384);				
			//	$a->PacketStream($data,false);
			//}
			$this->localsid = $a->ReadInt32();
			$this->retcode = $a->ReadInt32();
			$this->base = $a->ReadRoleBase();			
			$this->error = 0;
			if ($a->done!=true) $this->error = 1;		// Пакет разобран не до конца
			if ($a->overflow==true) $this->error = 2;	// Длинна пакета меньше ожидаемой
			return $data;			
		}
	}
}
