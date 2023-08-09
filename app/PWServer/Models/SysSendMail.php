<?php

namespace App\PWServer\Models;

use App\PWServer\PacketStream;


class SysSendMail {
	private $type;		// cuInt32
	private $answlen;	// cuInt32
	var 	$retcode;	// int16
	public $tid;		// int32
	public $sysid;		// int32
	public $sys_type;	// byte
	public $receiver;	// int32
	public $title;		// string
	public $context;	// string
	public $attach_obj;	// GRoleInventory
	public $attach_money;	// int32
	public $error=0;	// Ошибка при разборке пакета
	private $dev_data = null;
	
	public function __construct($tid, $sysid, $sys_type, $receiver, $title, $context, $attach_obj, $attach_money, $ip = '127.0.0.1') {
		$this->retcode= -1;
		$p = new PacketStream();
		$p->WriteInt32($tid);
		$p->WriteInt32($sysid);
		$p->WriteByte($sys_type);
		$p->WriteInt32($receiver);
		$p->WriteString($title);
		$p->WriteString($context);
		$p->WriteGRoleInventory($attach_obj);
		$p->WriteInt32($attach_money);		
		$packet = cuint(4214).$p->wcount.$p->buffer;		
		$fp = fsockopen($ip, 29100);
		if ( $fp ) {
			$data = fread($fp, 8096);
			fputs($fp, $packet);
			$data = fread($fp, 8096);
			fclose($fp);
			$a = new PacketStream($data);
			$this->type = $a->ReadCUInt32();
			$this->answlen = $a->ReadCUInt32();
			$this->retcode = $a->ReadInt16();	
			$this->error = 0;
			if ( $a->done != true ) $this->error = 1;		// Пакет разобран не до конца
			if ( $a->overflow == true ) $this->error = 2;	// Длинна пакета меньше ожидаемой
			$this->dev_data = $data;
			return $data;			
		}
		else {
			$this->retcode = 100;
		}
		
	}
}
