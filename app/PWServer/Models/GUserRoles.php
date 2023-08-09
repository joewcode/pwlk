<?php

namespace App\PWServer\Models;

use App\PWServer\PacketStream;
use App\PWServer\Models\GUserRole;

class GUserRoles {
	private $type;		// cuInt32
	private $answlen;	// cuInt32
	public $retcode;		// Int32
	public $unk;		// Int32
	public $count;		// Byte
	public $roles;		// array of GUserRole
	public $error=0;		// Ошибка при разборке пакета

	function GetUserRoles( $id = 32, $fp ) {
		$aid = floor($id/16)*16;
		$p = new PacketStream();
		$p->WriteInt32(2147483648);
		$p->WriteInt32($aid);
		$pid = (PROTOCOL_VER < 27) ? 3032 : 3401;
		$packet = cuint($pid).$p->wcount.$p->buffer;
		//$fp = fsockopen($ip, 29400);
		if ( $fp ) {
			fputs($fp, $packet);
			$data = ReadPWPacket($fp);
			//fclose($fp);
			$a=new PacketStream($data);
			$this->type = $a->ReadCUInt32();
			$this->answlen = $a->ReadCUInt32();
			$this->retcode = $a->ReadInt32();
			$this->unk = $a->ReadInt32();
			$this->count = $a->ReadByte();
			$this->roles = array();
			for ($i=0; $i<$this->count; $i++){
				$this->roles[$i] = new GUserRole();
				$this->roles[$i]->id = $a->ReadInt32();
				//if (PROTOCOL_VER < 127)
				$this->roles[$i]->name = $a->ReadString();// else $this->roles[$i]->name = '';
			}
			$this->error = 0;
			if ($a->done!=true) $this->error = 1;		// Пакет разобран не до конца
			if ($a->overflow==true) $this->error = 2;	// Длинна пакета меньше ожидаемой
			return $data;
		}
	}
	
	function GetUserName( $id = 32, $fp ) {
		$n = '';
		$f = new GUserRoles();
		$f->GetUserRoles($id, $fp);
		for ($i=0; $i<$f->count; $i++) {
			if ( $f->roles[$i]->id == $id ) 
                $n=$f->roles[$i]->name;
		}
		return $n;
	}
}
