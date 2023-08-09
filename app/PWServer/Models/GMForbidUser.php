<?php

namespace App\PWServer\Models;

use App\PWServer\PacketStream;

class GMForbidUser {
	private $type;		// cuInt32
	private $answlen;	// cuInt32
	var $retcode;		// Int32
	var $localsid;		// Int32
	var $operation;		// Byte
	var $time;		// Int32
	var $createtime;	// Int32
	var $reason;		// String
	var $error=0;		// Ошибка при разборке пакета

	function ForbidUser( $operation, $gmuserid, $source, $userid, $time, $reason, $fp ) {
		$p = new PacketStream();
		$p->WriteInt32(2147483648);
		$p->WriteByte($operation);
		$p->WriteInt32($gmuserid);
		$p->WriteInt32($source);
		$p->WriteInt32($userid);
		$p->WriteInt32($time);
		$p->WriteString($reason);
		$packet = cuint(8004).$p->wcount.$p->buffer;
		if ( $fp ) {
			fputs($fp, $packet);
			$data = fread($fp,8096);
			$a = new PacketStream($data);
			$this->type = $a->ReadCUInt32();
			$this->answlen = $a->ReadCUInt32();
			$this->localsid = $a->ReadInt32();
			$this->retcode = $a->ReadInt32();
			$this->operation = $a->ReadByte();				
			$this->time = $a->ReadInt32();
			$this->createtime = $a->ReadInt32();
			$this->reason = $a->ReadString();
			$this->error = 0;
			if ($a->done!=true) $this->error = 1;		// Пакет разобран не до конца
			if ($a->overflow==true) $this->error = 2;	// Длинна пакета меньше ожидаемой
			return $data;
		}
	}
}
