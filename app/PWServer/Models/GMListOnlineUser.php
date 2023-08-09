<?php

namespace App\PWServer\Models;

use App\PWServer\PacketStream;
use App\PWServer\Models\GMPlayerInfo;

class GMListOnlineUser {
	private $type;		// cuInt32
	private $answlen;	// cuInt32	
	public $retcode;		// Int32
	public $gmroleid;		// Int32
	public $localsid;		// UInt32
	public $handler;		// Int32
	public $count;		// CUInt
	public $userlist;		// array of GMPlayerInfo	
	public $error=0;		// Ошибка при разборке пакета
	
	function GetList( $gmroleid = 0, $localsid = 0, $handler = 0, $cond = '' ) {
		$p = new PacketStream();
		$p->WriteInt32($gmroleid);
		$p->WriteInt32($localsid);
		$p->WriteInt32($handler);
		$p->WriteOctets($cond);
		$packet = cuint(352).$p->wcount.$p->buffer;
		$fp = fsockopen(DELIVERY_IP, DELIVERY_PORT);
		if ( $fp ) {
			$data = fread($fp, 8096);
			fputs($fp, $packet);
			$data = ReadPWPacket($fp);
			fclose($fp);
			$a = new PacketStream($data);
			$this->type = $a->ReadCUInt32();
			$this->answlen = $a->ReadCUInt32();			
			$this->retcode = $a->ReadInt32();
			$this->gmroleid = $a->ReadInt32();
			$this->localsid = $a->ReadInt32();
			$this->handler = $a->ReadInt32();					
			$this->count = $a->ReadCUInt32();
			$this->userlist = array();
			if ( $this->count > 0 ) {
				for ($b=0; $b < $this->count; $b++) {
					$this->userlist[$b] = new GMPlayerInfo();
					$this->userlist[$b]->userid = $a->ReadInt32();
					$this->userlist[$b]->roleid = $a->ReadInt32();
					$this->userlist[$b]->linkid = $a->ReadInt32();
					$this->userlist[$b]->localsid = $a->ReadInt32();
					$this->userlist[$b]->gsid = $a->ReadInt32();
					$this->userlist[$b]->status = $a->ReadByte();
					$this->userlist[$b]->name = $a->ReadString();
				}
			}
			$this->error = 0;
			if ( $a->done != true ) $this->error = 1;
			if ( $a->overflow == true ) $this->error = 2;			
			return $data;
		}		
	}	
}
