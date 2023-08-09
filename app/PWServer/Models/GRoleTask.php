<?php

namespace App\PWServer\Models;

use App\PWServer\PacketStream;

class GRoleTask {
	private $type;			// cuInt32
	private $answlen;		// cuInt32
	public $localsid;			// Int32
	public $retcode=-1;		// Int32
	public $task;			// RoleTast
	public $error=0;		// Ошибка при разборке пакета

	function PutRoleTask( $id, $fp, $autosortitems = false ) {
		$p = new PacketStream();
		$p->WriteInt32(2147483677);
		$p->WriteInt32($id);
		$p->WriteRoleTask($this->task);
		$packet=cuint(3018).$p->wcount.$p->buffer;
		//$fp = fsockopen($ip, 29400);
		if ( $fp ) {
			fputs($fp, $packet);
			$data = fread($fp, 8096);
			//fclose($fp);
			$a = new PacketStream($data);
			$this->type = $a->ReadCUInt32();
			$this->answlen = $a->ReadCUInt32();
			$this->localsid = $a->ReadInt32();
			$this->retcode = $a->ReadInt32();
			$this->error = 0;
			if ($a->done!=true) $this->error = 1;		// Пакет разобран не до конца
			if ($a->overflow==true) $this->error = 2;	// Длинна пакета меньше ожидаемой			
			return $data;
		}
	}

	function GetRoleTask( $id, $fp ) {
		$p = new PacketStream();
		$p->WriteInt32(2147483648);
		$p->WriteInt32($id);				
		$packet=cuint(3019).$p->wcount.$p->buffer;		
		//$fp = fsockopen($ip, 29400);
		if ( $fp ) {	
			fputs($fp, $packet);
			$data = ReadPWPacket($fp);
			//fclose($fp);
			$a = new PacketStream($data);
			$this->type = $a->ReadCUInt32();
			$this->answlen = $a->ReadCUInt32();
			//while ((strlen($a->buffer)-$a->pos)<$this->answlen) { 
			//	$data.=fread($fp,8196);				
			//	$a->PacketStream($data,false);
			//}
			$this->localsid = $a->ReadInt32();
			$this->retcode = $a->ReadInt32();
			$this->task = $a->ReadRoleTask();
			$this->error = 0;
			if ($a->done!=true) $this->error = 1;		// Пакет разобран не до конца
			if ($a->overflow==true) $this->error = 2;	// Длинна пакета меньше ожидаемой
			return $data;
		}
	}
}
