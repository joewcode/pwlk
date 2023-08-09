<?php

namespace App\PWServer\Models;

use App\PWServer\Models\GRoleBase;
use App\PWServer\Models\GRoleStatus;
use App\PWServer\Models\GRolePocket;
use App\PWServer\Models\GRoleEquipment;
use App\PWServer\Models\GRoleStorehouse;
use App\PWServer\Models\GRoleTask;


class GRoleData {
	public $type;			// CUInt
	public $localsid;
	public $retcode;
	public $base;			// RoleBase
	public $status;		// RoleStatus
	public $pocket;		// RolePocket
	public $equipment;		// RoleEquipment
	public $storehouse;		// RoleStorehouse
	public $task;			// RoleTask
	public $error = 0;
	
	function PutRoleData( $id, $fp ) {
		if ( PROTOCOL_VER < 27 ) {
			// Base
			$t = new GRoleBase();
			$t->base = $this->base;
			$t->PutRoleBase($id,$fp);			
			$this->retcode = $t->retcode;			
			// Status
			$t = new GRoleStatus();
			$t->status = $this->status;
			$t->PutRoleStatus($id, $fp);			
			$this->retcode+= $t->retcode;			
			// Pocket
			$t = new GRolePocket();
			$t->pocket = $this->pocket;
			$t->PutRolePocket($id, $fp);			
			$this->retcode+= $t->retcode;			
			// Equipment
			$t = new GRoleEquipment();
			$t->items = $this->equipment;
			$t->PutRoleEquipment($id, $fp);			
			$this->retcode+= $t->retcode;			
			// Storehouse
			$t = new GRoleStorehouse();
			$t->storehouse = $this->storehouse;
			$t->PutRoleStorehouse($id, $fp);			
			$this->retcode+= $t->retcode;			
			// Task
			$t = new GRoleTask();
			$t->task = $this->task;
			$data = $t->PutRoleTask($id, $fp);			
			$this->retcode+= $t->retcode;			
			return true;
		}
		$p = new PacketStream();
		$p->WriteInt32(2147483648);
		$p->WriteInt32($id);
		$p->WriteByte(1);//Overwrite
		$p->WriteRoleBase($this->base);
		$p->WriteRoleStatus($this->status);
		$p->WriteRolePocket($this->pocket);
		$p->WriteGRoleItems($this->equipment->items);
		$p->WriteRoleStorehouse($this->storehouse);
		$p->WriteRoleTask($this->task);
		$packet = cuint(8002).$p->wcount.$p->buffer;
		if ( $fp ) {	
			fputs($fp, $packet);
			$data = ReadPWPacket($fp);			
			//$data=fread($fp,8196);			
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
	
	
	function GetRoleData( $id, $fp ) {
		if ( PROTOCOL_VER < 27 ) {
			// Base
			$t = new GRoleBase();
			$t->GetRoleBase($id, $fp);
			$this->localsid = $t->localsid;
			$this->base = $t->base;
			$this->retcode = $t->retcode;
			$this->error = $t->error;
			if ( $t->error ) return false;
			// Status
			$t = new GRoleStatus();
			$t->GetRoleStatus($id, $fp);
			$this->status = $t->status;
			$this->retcode += $t->retcode;
			$this->error = $t->error;
			if ( $t->error ) return false;
			// Pocket
			$t = new GRolePocket();
			$t->GetRolePocket($id, $fp);
			$this->pocket = $t->pocket;
			$this->retcode += $t->retcode;
			$this->error = $t->error;
			if ($t->error) return false;
			// Equipment
			$t = new GRoleEquipment();
			$t->GetRoleEquipment($id, $fp);
			$this->equipment = $t->items;
			$this->retcode += $t->retcode;
			$this->error = $t->error;
			if ( $t->error ) return false;
			// Storehouse
			$t = new GRoleStorehouse();
			$t->GetRoleStorehouse($id, $fp);
			$this->storehouse = $t->storehouse;
			$this->retcode += $t->retcode;
			$this->error = $t->error;
			if ( $t->error ) return false;
			// Task
			$t = new GRoleTask();
			$data = $t->GetRoleTask($id, $fp);
			$this->task = $t->task;
			$this->retcode += $t->retcode;
			$this->error = $t->error;
			if ( $t->error ) return false;
			return $data;
		}
		$p=new PacketStream();
		$p->WriteInt32(2147483648);
		$p->WriteInt32($id);				
		$packet=cuint(8003).$p->wcount.$p->buffer;
		if ( $fp ) {	
			fputs($fp, $packet);
			$data = ReadPWPacket($fp);
			$a = new PacketStream($data);
			$this->type = $a->ReadCUInt32();
			$this->answlen = $a->ReadCUInt32();
			//$d = unpack('H*',$data);
			//echo $d[1]."<br>\n\n";
			//echo 'Anwslen: '.$this->answlen.', readed: '.strlen($data);die();
			//while ((strlen($a->buffer)-$a->pos)<$this->answlen) { 
			//	$data.=fread($fp,8196);				
			//	$a->PacketStream($data,false);
			//}
			$this->localsid = $a->ReadInt32();
			$this->retcode = $a->ReadInt32();
			$this->base = $a->ReadRoleBase();
			$this->status = $a->ReadRoleStatus();
			$this->pocket = $a->ReadRolePocket();
			$this->equipment = new GRoleEquipment();
			$this->equipment->items = $a->ReadGRoleItems();
			$this->storehouse = $a->ReadRoleStorehouse();
			$this->task = $a->ReadRoleTask();
			$this->error = 0;
			if ( $a->done != true ) $this->error = 1;		// Пакет разобран не до конца
			if ( $a->overflow == true ) $this->error = 2;	// Длинна пакета меньше ожидаемой
			return $data;
		}
	}
}
