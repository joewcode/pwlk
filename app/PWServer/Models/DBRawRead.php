<?php

namespace App\PWServer\Models;

use App\PWServer\PacketStream;
use App\PWServer\Models\RawKeyValue;

class DBRawRead {
	private $type;		// cuInt32
	private $answlen;	// cuInt32
	var $retcode=0;		// Int32
	var $localsid=0;	// Int32
	var $handle='';		// Octets
	var $values;		// array of RawKeyValue
	var $error=0;		// Ошибка при разборке пакета
	
	function Read( $table, $key, $fp, $handle = '' ) {
		if (!$fp) die('Socket connect error');
		$p=new PacketStream();
		$p->WriteInt32(2147483649);
		$p->WriteOctets($table);
		$p->WriteOctets($handle);
		$p->WriteOctets($key);
		$packet = cuint(3055).$p->wcount.$p->buffer;
		fputs($fp, $packet);
		$data = ReadPWPacket($fp);
		$p = new PacketStream($data);
		$this->type = $p->ReadCUInt32();
		$this->answlen = $p->ReadCUInt32();
		//while ((strlen($p->buffer)-$p->pos)<$this->answlen) { 
		//	$data.=fread($fp,8196);				
		//	$p->PacketStream($data, false);
		//}
		$this->localsid = $p->ReadInt32();
		$this->retcode = $p->ReadInt32();
		$this->handle = $p->ReadOctets();
		$cnt = $p->ReadCUInt32();
		$this->values = array();
		if ($cnt > 0) {
			for($a=0; $a<$cnt; $a++){
				$this->values[$a] = new RawKeyValue();
				$this->values[$a]->key = $p->ReadOctets();
				$this->values[$a]->value = $p->ReadOctets();
			}
		}
		$this->error = 0;
		if ($p->done!=true) $this->error = 1;		// Пакет разобран не до конца
		if ($p->overflow==true) $this->error = 2;	// Длинна пакета меньше ожидаемой
		return $data;
	}

	function Walk( $table, $fp ) {
		if (!$fp) die('Socket connect error');
		$res = array();
		$this->Read($table, '', $fp);
		if ($this->retcode!=0 || $this->error!=0) return $res;
		$res = array_merge($res, $this->values);
		//print_r($this->values);
		while ($this->handle != '') {
			$this->Read($table, '', $fp, $this->handle);
			if ($this->retcode!=0 || $this->error!=0) return $res;
			$res = array_merge($res, $this->values);
			//print_r($this->values);
		}
		return $res;
	}

}
