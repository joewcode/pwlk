<?php

namespace App\PWServer\Models;

use App\PWServer\PacketStream;

use App\PWServer\Models\StockLogs;
use App\PWServer\Models\StockLog;
use App\PWServer\Models\GPair;


class GUser {
	private $type;			// cuInt32
	private $answlen;		// cuInt32
	public $retcode;		// Int32
	public $localsid;		// Int32
	public $logicuid=0;		// Int32
	public $rolelist=0;		// Int32
	public $cash=0;		// Int32
	public $money=0;		// Int32
	public $cash_add=0;		// Int32
	public $cash_buy=0;		// Int32
	public $cash_sell=0;		// Int32
	public $cash_used=0;		// Int32
	public $add_serial=0;		// Int32
	public $use_serial=0;		// Int32
	public $exg_log;		// StockLogS
	public $addiction='';		// Octets
	public $cash_password='';	// Octets
	// 7
	public $reserved1;		// Int16
	public $reserved2;		// Int32
	public $reserved3;		// Int32
	public $reserved4;		// Int32
	// end 7
	public $autolock;		// std::vector<GNET::GPair, std::allocator<GNET::GPair> >
	public $status=0;		// Byte
	public $forbid;		// GNET::GetRoleForbid::GRoleForbidVector
	public $reference='';		// Octets
	public $consume_reward='';	// Octets
	public $taskcounter='';	// Octets
	// 60+
	public $cash_sysauction='';	// Octets
	public $login_record='';	// Octets
	public $reverded31=0;		// Byte		// only in 60
	public $mall_consumption='';	// Octets
	public $reserved32=0;		// Int16
	public $error=0;		// Ошибка при разборке пакета

	function PutUser($id,$fp){
		$p = new PacketStream();
		$p->WriteInt32(2147483649);
		$p->WriteInt32($id);
		$p->WriteInt32($this->userid);	
		$p->WriteInt32($this->rolelist);
		$p->WriteInt32($this->cash);
		$p->WriteInt32($this->money);
		$p->WriteInt32($this->cash_add);
		$p->WriteInt32($this->cash_buy);
		$p->WriteInt32($this->cash_sell);
		$p->WriteInt32($this->cash_used);
		$p->WriteInt32($this->add_serial);
		$p->WriteInt32($this->use_serial);
		$p->WriteByte($this->exg_log->count);
		if ($this->exg_log->count>0)
		foreach ($this->exg_log->stocklog as $i => $val){
			$p->WriteInt32($val->tid);
			$p->WriteInt32($val->time);
			$p->WriteInt16($val->result);
			$p->WriteInt16($val->volume);
			$p->WriteInt32($val->cost);
		}
		$p->WriteOctets($this->addiction);
		$p->WriteOctets($this->cash_password);
		if (PROTOCOL_VER <= 12 ) {
			$p->WriteInt16($this->reserved1);
			$p->WriteInt32($this->reserved2);
			$p->WriteInt32($this->reserved3);
			$p->WriteInt32($this->reserved4);
		} else {
			$p->WriteCUInt32(count($this->autolock));
			if (count($this->autolock)>0)
			foreach ($this->autolock as $i => $val){
				$p->WriteInt32($val->key);
				$p->WriteInt32($val->value);
			}
			$p->WriteByte($this->status);
			$p->WriteGRoleForbids($this->forbid);
			$p->WriteOctets($this->reference);
			$p->WriteOctets($this->consume_reward);
			$p->WriteOctets($this->taskcounter);
			if (PROTOCOL_VER >= 60 ) {
				$p->WriteOctets($this->cash_sysauction);
				$p->WriteOctets($this->login_record);
			}
			if (PROTOCOL_VER <= 27) {
				$p->WriteByte($this->reserved2);
				$p->WriteInt32($this->reserved3);
			} else 
			if (PROTOCOL_VER <= 63) {
				$p->WriteByte($this->reserved31);
				$p->WriteInt16($this->reserved32);
			} else 
			if (PROTOCOL_VER <= 70) {
				$p->WriteOctets($this->mall_consumption);
				$p->WriteInt16($this->reserved32);
			}
		}
		$packet=cuint(3001).$p->wcount.$p->buffer;		
		if ( $fp ) {
			fputs($fp, $packet);
			$data=fread($fp, 8096);
			//fclose($fp);
			$a = new PacketStream($data);
			$this->type = $a->ReadCUInt32();
			$this->answlen = $a->ReadCUInt32();
			$this->retcode = $a->ReadInt32();
			return $data;
		}
	}
	
	function GetUser( $id, $fp, $ip = '127.0.0.1' ) {
		$p = new PacketStream();
		$aid = floor($id/16)*16;
		$p->WriteInt32(2147483648);
		$p->WriteInt32($aid);
		$ips=explode('.', $ip);
		$pp = new PacketStream();
		$pp->WriteByte($ips[0]); 
		$pp->WriteByte($ips[1]); 
		$pp->WriteByte($ips[2]); 
		$pp->WriteByte($ips[3]);
		$ips = unpack('I', $pp->buffer);
		$p->WriteInt32(time());
		$p->WriteInt32($ips[1]);
		$packet = cuint(3002).$p->wcount.$p->buffer;		
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
			$this->logicuid = $a->ReadInt32();
			$this->rolelist = $a->ReadInt32();
			$this->cash = $a->ReadInt32();
			$this->money = $a->ReadInt32();
			$this->cash_add = $a->ReadInt32();
			$this->cash_buy = $a->ReadInt32();
			$this->cash_sell = $a->ReadInt32();
			$this->cash_used = $a->ReadInt32();
			$this->add_serial = $a->ReadInt32();
			$this->use_serial = $a->ReadInt32();
			$this->exg_log = new StockLogs();
			$this->exg_log->count = $a->ReadCUInt32();
			for ($i=0; $i<$this->exg_log->count; $i++){
				$this->exg_log->stocklog[$i] = new StockLog();
				$this->exg_log->stocklog[$i]->tid = $a->ReadInt32();
				$this->exg_log->stocklog[$i]->time = $a->ReadInt32();
				$this->exg_log->stocklog[$i]->result = $a->ReadInt16();
				$this->exg_log->stocklog[$i]->volume = $a->ReadInt16();
				$this->exg_log->stocklog[$i]->cost = $a->ReadInt32();
			}
			$this->addiction = $a->ReadOctets();
			$this->cash_password = $a->ReadOctets();
			if ( PROTOCOL_VER <= 12 ) {
				$this->reserved1 = $a->ReadInt16();
				$this->reserved2 = $a->ReadInt32();
				$this->reserved3 = $a->ReadInt32();
				$this->reserved4 = $a->ReadInt32();
			} else {
				$this->autolock = array();
				$cnt = $a->ReadCUInt32();
				for ($i=0; $i<$cnt; $i++) {
					$this->autolock[$i] = new GPair();
					$this->autolock[$i]->key = $a->ReadInt32();
					$this->autolock[$i]->value = $a->ReadInt32();
				}				
				$this->status = $a->ReadByte();
				$this->forbid = $a->ReadGRoleForbids();
				$this->reference = $a->ReadOctets();
				$this->consume_reward = $a->ReadOctets();
				$this->taskcounter = $a->ReadOctets();
				if (PROTOCOL_VER >= 60 ) {
					$this->cash_sysauction = $a->ReadOctets();
					$this->login_record = $a->ReadOctets();
				}
				if (PROTOCOL_VER <= 27) {
					$this->reserved2 = $a->ReadByte();
					$this->reserved3 = $a->ReadInt32();
				} elseif (PROTOCOL_VER <= 63) {
					$this->reserved31 = $a->ReadByte();
					$this->reserved32 = $a->ReadInt16();
				} else {
					$this->mall_consumption = $a->ReadOctets();
					$this->reserved32 = $a->ReadInt16();
				}
			}
			$this->error = 0;
			if ($a->done!=true) $this->error = 1;		// Пакет разобран не до конца
			if ($a->overflow==true) $this->error = 2;	// Длинна пакета меньше ожидаемой
			//print_r($a);echo "<br>";
			return $data;			
		}
	}
}
