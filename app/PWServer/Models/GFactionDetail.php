<?php

namespace App\PWServer\Models;

use App\PWServer\PacketStream;

class GFactionDetail {
	private $type;		// cuInt32
	private $answlen;	// cuInt32
	public $retcode;		// Int32
	public $localsid;		// Int32
	public $fid=0;		// Int32
	public $name='';		// String
	public $level=0;		// Byte
	public $master=0;		// Int32
	public $announce='';	// Octets
	public $sysinfo='';	// Octets
	public $members;		// GFactionUsers
	// 1.4.4+
	public $last_op_time=0;	// Int32
	public $alliance;		// GNET::GFactionDetail::GFactionAllianceVector
	public $hostile;		// GNET::GFactionDetail::GFactionHostileVector
	public $apply;		// GNET::GFactionDetail::GFactionRelationApplyVector
	// 145+
	public $unifid;		// int64
	
	public $error=0;		// Ошибка при разборке пакета

	public function __construct( $id, $fp ) {
		if ( $id == 0 ) return;
		$p = new PacketStream();
		$p->WriteInt32(2147483649);
		$p->WriteInt32($id);
		$packet = cuint(4608).$p->wcount.$p->buffer;
		//$fp = fsockopen($ip, 29400);
		if ( $fp ) {
			fputs($fp, $packet);
			$data = ReadPWPacket($fp);
			//fclose($fp);
			$a = new PacketStream($data);
			$this->type = $a->ReadCUInt32();
			$this->answlen = $a->ReadCUInt32();
			if ( (strlen($a->buffer)-$a->pos) < $this->answlen ) { 
				$data.= fread($fp, 8096);				
				$a->PacketStream($data, false);
			}
			$this->retcode = $a->ReadInt32();
			$this->localsid = $a->ReadInt32();
			$this->fid = $a->ReadInt32();
			$this->name = $a->ReadString();
			$this->level = $a->ReadByte();
			$this->master = $a->ReadInt32();
			$this->announce = $a->ReadString();
			$this->sysinfo = $a->ReadString();
			$this->members = $a->ReadGFactionUsers();
			if ( PROTOCOL_VER >= 60 ) {
				$this->last_op_time = $a->ReadInt32();
				$this->alliance = $a->ReadGFactionAlliance();
				$this->hostile = $a->ReadGFactionAlliance();
				$this->apply = $a->ReadGFactionRelationApply();
			}
			if ( PROTOCOL_VER >= 145 ) {
				$this->unifid = $a->ReadInt64();
			}
			$this->error = checkParseErr($a);
			return $data;
		}
	}
}
