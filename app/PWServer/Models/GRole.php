<?php

namespace App\PWServer\Models;

use App\PWServer\PacketStream;
use App\PWServer\Models\GRoleDetail;
use App\PWServer\Models\ShopLogs;

class GRole {
	private $type;			// cuInt32
	private $answlen;		// cuInt32
	private $unk;			// Int32
	public $retcode=-1;		// Int32
	public $data_mask=0;		// Int32
	public $gameserver_id=0;	// Byte
	public $value;			// GRoleDetail

	function GetRole( $id, $mask, $fp ) {
		$p = new PacketStream();
		$p->WriteInt32(2147483648);
		$p->WriteInt32($id);
		$p->WriteInt32($mask);				
		$packet = cuint(3005).$p->wcount.$p->buffer;		
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
			$this->unk = $a->ReadInt32();
			$this->retcode = $a->ReadInt32();
			$this->data_mask = $a->ReadInt32();
			$this->gameserver_id = $a->ReadByte();			
			$this->value = new GRoleDetail();
			$this->value->version = $a->ReadByte();
			$this->value->id = $a->ReadInt32();
			if ( PROTOCOL_VER >= 27 ) $this->value->userid = $a->ReadInt32(); else $this->value->userid = floor($id/16)*16;
			$this->value->status = $a->ReadRoleStatus();
			$this->value->name = $a->ReadString();
			$this->value->race = $a->ReadInt32();
			$this->value->cls = $a->ReadInt32();
			$this->value->spouse = $a->ReadInt32();
			$this->value->gender = $a->ReadByte();
			$this->value->create_time = $a->ReadInt32();
			if ( PROTOCOL_VER >= 27 ) {
				$this->value->lastlogin_time = $a->ReadInt32();
				$this->value->cash_add = $a->ReadInt32();
			}
			$this->value->cash_total = $a->ReadInt32();
			$this->value->cash_used = $a->ReadInt32();
			$this->value->cash_serial = $a->ReadInt32();
			$this->value->factionid = $a->ReadInt32();
			$this->value->factionrole = $a->ReadInt32();
			$this->value->custom_data = $a->ReadOctets();
			$this->value->custom_stamp = $a->ReadInt32();
			$this->value->inventory = $a->ReadRolePocket();
			$this->value->equipment = $a->ReadGRoleItems();
			$this->value->storehouse = $a->ReadRoleStorehouse();
			$this->value->task = $a->ReadRoleTask();
			$this->value->addiction = $a->ReadOctets();
			$this->value->logs = new ShopLogs();
			$this->value->logs->ReadLogs($a);
			if ( PROTOCOL_VER >= 27 ) {
				$this->value->bonus_add = $a->ReadInt32();
				$this->value->bonus_reward = $a->ReadInt32();
				$this->value->bonus_used = $a->ReadInt32();
				$this->value->referrer = $a->ReadInt32();
				$this->value->userstorehouse = $a->ReadUserStorehouse();
				$this->value->taskcounter = $a->ReadOctets();
			}
			if ( PROTOCOL_VER >= 60 ) {
				$this->value->factionalliance = $a->ReadGFactionAlliance();
				$this->value->factionhostile = $a->ReadGFactionAlliance();
			}
			if ( PROTOCOL_VER >= 69 ) $this->value->mall_consumption = $a->ReadInt32();
			if ( PROTOCOL_VER >= 85 ) $this->value->src_zoneid = $a->ReadInt32();
			if ( PROTOCOL_VER >= 145 ) $this->value->unifid = $a->ReadInt64();
			if ( PROTOCOL_VER >= 156 ) {
				$this->value->vip_level = $a->ReadInt32();
				$this->value->score_add = $a->ReadInt32();
				$this->value->score_cost = $a->ReadInt32();
				$this->value->score_consume = $a->ReadInt32();
				$this->value->day_clear_stamp = $a->ReadInt32();
				$this->value->week_clear_stamp = $a->ReadInt32();
				$this->value->month_clear_stamp = $a->ReadInt32();
				$this->value->year_clear_stamp = $a->ReadInt32();
				$this->value->purchase_limit_data = $a->ReadOctets();
				$this->value->home_level = $a->ReadInt32();
			}
			$this->error = 0;
			if ( $a->done != true ) $this->error = 1;		// Пакет разобран не до конца
			if ( $a->overflow == true ) $this->error = 2;	// Длина пакета меньше ожидаемой
			return $data;			
		}
	}

	function PutRole($id,$mask,$fp){
		$p=new PacketStream();
		$p->WriteInt32(2147483649);
		$p->WriteInt32($id);
		$p->WriteInt32($mask);
		$p->WriteByte(0);
		// GRoleDetail
		$this->value->inventory->timestamp++;
		$p->WriteByte($this->value->version);
		$p->WriteInt32($this->value->id);
		if ( PROTOCOL_VER >= 27 ) $p->WriteInt32($this->value->userid);
		$p->WriteRoleStatus($this->value->status);
		$p->WriteString($this->value->name);
		$p->WriteInt32($this->value->race);
		$p->WriteInt32($this->value->cls);
		$p->WriteInt32($this->value->spouse);
		$p->WriteByte($this->value->gender);
		$p->WriteInt32($this->value->create_time);
		if ( PROTOCOL_VER >= 27 ) {
			$p->WriteInt32($this->value->lastlogin_time);
			$p->WriteInt32($this->value->cash_add);
		}
		$p->WriteInt32($this->value->cash_total);
		$p->WriteInt32($this->value->cash_used);
		$p->WriteInt32($this->value->cash_serial);
		$p->WriteInt32($this->value->factionid);
		$p->WriteInt32($this->value->factionrole);
		$p->WriteOctets($this->value->custom_data);
		$p->WriteInt32($this->value->custom_stamp);
		$p->WriteRolePocket($this->value->inventory);
		$p->WriteGRoleItems($this->value->equipment);
		$p->WriteRoleStorehouse($this->value->storehouse);
		$p->WriteRoleTask($this->value->task);
		$p->WriteOctets($this->value->addiction);
		$this->value->logs->WriteLogs($p);
		if ( PROTOCOL_VER >= 27 ) {
			$p->WriteInt32($this->value->bonus_add);
			$p->WriteInt32($this->value->bonus_reward);
			$p->WriteInt32($this->value->bonus_used);
			$p->WriteInt32($this->value->referrer);
			$p->WriteUserStorehouse($this->value->userstorehouse);
			$p->WriteOctets($this->value->taskcounter);
		}
		if ( PROTOCOL_VER >= 60 ) {
			$p->WriteGFactionAlliance($this->value->factionalliance);
			$p->WriteGFactionAlliance($this->value->factionhostile);
		}
		if ( PROTOCOL_VER >= 69 ) $p->WriteInt32($this->value->mall_consumption);
		if ( PROTOCOL_VER >= 85 ) $p->WriteInt32($this->value->src_zoneid);
		if ( PROTOCOL_VER >= 145 ) $p->WriteInt64($this->value->unifid);
		if ( PROTOCOL_VER >= 156 ) {
			$p->WriteInt32($this->value->vip_level);
			$p->WriteInt32($this->value->score_add);
			$p->WriteInt32($this->value->score_cost);
			$p->WriteInt32($this->value->score_consume);
			$p->WriteInt32($this->value->day_clear_stamp);
			$p->WriteInt32($this->value->week_clear_stamp);
			$p->WriteInt32($this->value->month_clear_stamp);
			$p->WriteInt32($this->value->year_clear_stamp);
			$p->WriteOctets($this->value->purchase_limit_data);
			$p->WriteInt32($this->value->home_level);
		}
		$packet=cuint(3024).$p->wcount.$p->buffer;		
		if($fp) {	
			fputs($fp, $packet);
			$data = ReadPWPacket($fp);
			$a = new PacketStream($data);
			$this->type = $a->ReadCUInt32();
			$this->answlen = $a->ReadCUInt32();			
			$this->unk = $a->ReadInt32();
			$this->retcode = $a->ReadInt32();
			$this->data_mask = $a->ReadInt32();
			$this->gameserver_id = $a->ReadByte();
			$this->error = 0;
			if ( $a->done != true ) $this->error = 1;		// Пакет разобран не до конца
			if ( $a->overflow == true ) $this->error = 2;	// Длина пакета меньше ожидаемой
			return $data;			
		}
	}
}
