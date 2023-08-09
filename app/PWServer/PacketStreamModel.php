<?php

namespace App\PWServer;

use App\PWServer\Models\RoleBase;
use App\PWServer\Models\GRoleForbids;
use App\PWServer\Models\RoleStatus;
use App\PWServer\Models\RolePocket;
use App\PWServer\Models\GRoleItems;
use App\PWServer\Models\RoleStorehouse;
use App\PWServer\Models\RoleTask;
use App\PWServer\Models\GRoleInventory;
use App\PWServer\Models\UserStorehouse;
use App\PWServer\Models\GFactionAlliance;
use App\PWServer\Models\GFactionRelationApply;
use App\PWServer\Models\GFactionUsers;
use App\PWServer\Models\GFactionUser;
use App\PWServer\Models\FactionAlliance;

abstract class PacketStreamModel {
	
	abstract protected function ReadByte();
	abstract protected function UpdateWriteCount();
	abstract protected function WriteByte( $byte );
	abstract protected function ReadInt32( $bigendian = true );
	abstract protected function ReadInt64( $bigendian = true );
	abstract protected function WriteInt32( $byte, $bigendian = true );
	abstract protected function WriteInt64( $byte, $bigendian = true );
	abstract protected function ReadInt16( $bigendian = true );
	abstract protected function WriteInt16( $byte, $bigendian = true );
	abstract protected function ReadSingle( $bigendian = true );
	abstract protected function WriteSingle( $byte, $bigendian = true );
	abstract protected function ReadCUInt32();
	abstract protected function WriteCUInt32( $byte, $bigendian = true );
	abstract protected function ReadOctets();
	abstract protected function ReadString();
	abstract protected function WriteOctets( $byteb );
	abstract protected function WriteString( $byte );


	public function ReadGRoleInventory() {
		$obj = new GRoleInventory();
		$obj->id = $this->ReadInt32();
		$obj->pos = $this->ReadInt32();
		$obj->count = $this->ReadInt32();
		$obj->max_count = $this->ReadInt32();
		$obj->data = $this->ReadOctets();
		$obj->proctype = $this->ReadInt32();
		$obj->expire_date = $this->ReadInt32();
		$obj->guid1 = $this->ReadInt32();
		$obj->guid2 = $this->ReadInt32();
		$obj->mask = $this->ReadInt32();
		return $obj;
	}

	public function WriteGRoleInventory( $obj ) {
		$this->WriteInt32($obj->id);			
		$this->WriteInt32($obj->pos);
		$this->WriteInt32($obj->count);
		$this->WriteInt32($obj->max_count);
		$this->WriteOctets($obj->data);
		$this->WriteInt32($obj->proctype);
		$this->WriteInt32($obj->expire_date);
		$this->WriteInt32($obj->guid1);
		$this->WriteInt32($obj->guid2);
		$this->WriteInt32($obj->mask);	
	}

	public function ReadGRoleItems() {
		$items = new GRoleItems();
		$items->count = $this->ReadCUInt32();
		$items->items = array();
		for ($i=0; $i<$items->count; $i++){
			$items->items[$i] = new GRoleInventory();
			$items->items[$i] = $this->ReadGRoleInventory();			
		}
		return $items;		
	}

	public function WriteGRoleItems( $items, $autosortitems = false ) {
		$this->buffer.= cuint(count($items->items));
		$this->UpdateWriteCount();
		$cnt = 0;	
		foreach ($items->items as $i => $val) {
			if ($autosortitems==true)
				$items->items[$i]->pos = $cnt; 
			$this->WriteGRoleInventory($items->items[$i]);
			$cnt++;
		}	
	}

	public function ReadGFactionUser() {
		$txtrank = Array("Мастер", "Маршал", "Майор", "Капитан", "Член");
		$obj = new GFactionUser();
		$obj->roleid = $this->ReadInt32();
		$obj->level = $this->ReadByte();
		$obj->occupation = $this->ReadByte();
		$obj->froleid = $this->ReadByte();
		$obj->rank = $txtrank[$obj->froleid-2];
		$obj->loginday = $this->ReadInt16();
		$obj->online_status = $this->ReadByte();
		$obj->name = $this->ReadString();
		$obj->nickname = $this->ReadString();
		$obj->contrib = (PROTOCOL_VER >= 60) ? $this->ReadInt32() : 0;
		if (PROTOCOL_VER >= 85) {
			$obj->delayexpel = $this->ReadByte();
			$obj->expeltime = $this->ReadInt32();
		}
		if (PROTOCOL_VER >= 145) {
			$obj->reputation = $this->ReadInt32();
			$obj->reincarn_times = $this->ReadByte();
			$obj->gender = $this->ReadByte();
		}
		return $obj;
	}

	public function WriteGFactionUser( $obj ) {
		$this->WriteInt32($obj->roleid);
		$this->WriteByte($obj->level);
		$this->WriteByte($obj->occupation);
		$this->WriteByte($obj->froleid);
		$this->WriteInt16($obj->loginday);
		$this->WriteByte($obj->online_status);
		$this->WriteString($obj->name);
		$this->WriteString($obj->nickname);
		if (PROTOCOL_VER >= 60) $this->WriteInt32($obj->contrib);
		if (PROTOCOL_VER >= 85) {
			$this->WriteByte($obj->delayexpel);
			$this->WriteInt32($obj->expeltime);
		}
		if (PROTOCOL_VER >= 145) {
			$this->WriteInt32($obj->reputation);
			$this->WriteByte($obj->reincarn_times);
			$this->WriteByte($obj->gender);
		}
	}

	public function ReadGFactionUsers() {
		$obj = new GFactionUsers();
		//$obj->unk = $this->ReadByte();				// хз что это
		$obj->count = $this->ReadCUInt32();
		$obj->members = array();
		for ($i=0; $i<$obj->count; $i++){
			$obj->members[$i] = $this->ReadGFactionUser();
		}
		return $obj;		
	}

	public function WriteGFactionUsers( $obj ) {
		$this->WriteByte($obj->unk);
		$this->buffer.= cuint( count($obj->members) );	
		$this->UpdateWriteCount();
		foreach ($obj->members as $i => $val){
			$this->WriteGFactionUser($obj->members[$i]);
		}	
	}

	public function ReadGRoleForbid() {
		$obj = new GRoleForbid();
		$obj->type = $this->ReadByte();
		$obj->time = $this->ReadInt32();
		$obj->createtime = $this->ReadInt32();
		$obj->reason = $this->ReadString();
		return $obj;
	}

	public function WriteGRoleForbid( $obj ) {
		$this->WriteByte($obj->type);
		$this->WriteInt32($obj->time);
		$this->WriteInt32($obj->createtime);
		$this->WriteString($obj->reason);
	}

	public function ReadGRoleForbids() {
		$obj = new GRoleForbids();
		$obj->count = $this->ReadCUInt32();
		$obj->forbids = array();
		for ($i=0; $i<$obj->count; $i++){
			$obj->forbids[$i] = $this->ReadGRoleForbid();			
		}
		return $obj;		
	}

	public function WriteGRoleForbids( $obj ) {
		$this->WriteByte(count($obj->forbids));
		$this->UpdateWriteCount();
		foreach ($obj->forbids as $i => $val){
			$this->WriteGRoleForbid($obj->forbids[$i]);
		}	
	}

	public function ReadGFriendInfo() {
		$obj = new GFriendInfo();
		$obj->id = $this->ReadInt32();
		$obj->cls = $this->ReadByte();
		$obj->onl = $this->ReadByte();
		$obj->name = $this->ReadString();
		return $obj;
	}

	public function WriteGFriendInfo( $obj ) {
		$this->WriteInt32($obj->id);
		$this->WriteByte($obj->cls);
		$this->WriteByte($obj->onl);
		$this->WriteString($obj->name);
	}

	public function ReadGFriends() {
		$obj = new GFriends();
		$obj->count = $this->ReadCUInt32();
		$obj->friend = array();
		for ($i=0; $i<$obj->count; $i++){
			$obj->friend[$i] = $this->ReadGFriendInfo();			
		}
		return $obj;		
	}

	public function WriteGFriends( $obj ) {
		$this->WriteByte(count($obj->friend));
		$this->UpdateWriteCount();
		foreach ($obj->friend as $i => $val){
			$this->WriteGFriendInfo($obj->friend[$i]);
		}	
	}
	
	public function ReadFactionAlliance() {
		$obj = new FactionAlliance();
		$obj->fid = $this->ReadInt32();
		$obj->end_time = $this->ReadInt32();
		return $obj;
	}

	public function WriteFactionAlliance( $obj ) {
		$this->WriteInt32($obj->fid);
		$this->WriteInt32($obj->end_time);
	}
	
	public function ReadGFactionAlliance() {
		$obj = new GFactionAlliance();
		$obj->count = $this->ReadCUInt32();
		$obj->alliances = array();
		for ($i=0; $i<$obj->count; $i++){
			$obj->alliances[$i] = $this->ReadFactionAlliance();			
		}
		return $obj;
	}

	public function WriteGFactionAlliance( $obj ) {
		$this->WriteCUInt32(count($obj->alliances));
		foreach ($obj->alliances as $i => $val){
			$this->WriteFactionAlliance($val);
		}
	}
	
	public function ReadFactionRelationApply() {
		$obj = new FactionRelationApply();
		$obj->type = $this->ReadInt32();
		$obj->fid = $this->ReadInt32();
		$obj->end_time = $this->ReadInt32();
		return $obj;
	}
	
	public function ReadGFactionRelationApply() {
		$obj = new GFactionRelationApply();
		$obj->count = $this->ReadCUInt32();
		$obj->applys = array();
		for ($i=0; $i<$obj->count; $i++){
			$obj->applys[$i] = $this->ReadFactionRelationApply();			
		}
		return $obj;
	}
	
	public function ReadRoleBase() {
		$obj = new RoleBase();
		$obj->version = $this->ReadByte();
		$obj->id = $this->ReadInt32();
		$obj->name = $this->ReadString();
		$obj->race = $this->ReadInt32();
		$obj->cls = $this->ReadInt32();
		$obj->gender = $this->ReadByte();
		$obj->custom_data = $this->ReadOctets();
		$obj->config_data = $this->ReadOctets();
		$obj->custom_stamp = $this->ReadInt32();
		$obj->status = $this->ReadByte();
		$obj->delete_time = $this->ReadInt32();
		$obj->create_time = $this->ReadInt32();
		$obj->lastlogin_time = $this->ReadInt32();
		$obj->forbid = $this->ReadGRoleForbids();
		$obj->help_states = $this->ReadOctets();
		$obj->spouse = $this->ReadInt32();
		$obj->userid = $this->ReadInt32();
		if (PROTOCOL_VER < 80) {
			$obj->reserved2 = $this->ReadInt32();
		} else {
			$obj->cross_data = $this->ReadOctets();	
			$obj->reserved2_ = $this->ReadByte();	
			$obj->reserved3 = $this->ReadByte();
			$obj->reserved4 = $this->ReadByte();
		}
		return $obj;		
	}
	
	public function WriteRoleBase( $obj ) {
		$this->WriteByte($obj->version);
		$this->WriteInt32($obj->id);
		$this->WriteString($obj->name);
		$this->WriteInt32($obj->race);
		$this->WriteInt32($obj->cls);
		$this->WriteByte($obj->gender);
		$this->WriteOctets($obj->custom_data);
		$this->WriteOctets($obj->config_data);
		$this->WriteInt32($obj->custom_stamp);
		$this->WriteByte($obj->status);
		$this->WriteInt32($obj->delete_time);
		$this->WriteInt32($obj->create_time);
		$this->WriteInt32($obj->lastlogin_time);
		$this->WriteGRoleForbids($obj->forbid);
		$this->WriteOctets($obj->help_states);
		$this->WriteInt32($obj->spouse);
		$this->WriteInt32($obj->userid);
		if (PROTOCOL_VER < 80) {
			$this->WriteInt32($obj->reserved2);
		} else {
			$this->WriteOctets($obj->cross_data);
			$this->WriteByte($obj->reserved2_);
			$this->WriteByte($obj->reserved3);
			$this->WriteByte($obj->reserved4);
		}
	}
	
	public function ReadRoleStatus() {
		$obj = new RoleStatus();
		$obj->version = $this->ReadByte();
		$obj->level = $this->ReadInt32();
		$obj->level2 = $this->ReadInt32();
		$obj->exp = $this->ReadInt32();
		$obj->sp = $this->ReadInt32();
		$obj->pp = $this->ReadInt32();
		$obj->hp = $this->ReadInt32();
		$obj->mp = $this->ReadInt32();
		$obj->posx = $this->ReadSingle();
		$obj->posy = $this->ReadSingle();
		$obj->posz = $this->ReadSingle();
		$obj->worldtag = $this->ReadInt32();
		$obj->invader_state = $this->ReadInt32();
		$obj->invader_time = $this->ReadInt32();
		$obj->pariah_time = $this->ReadInt32();
		$obj->reputation = $this->ReadInt32();
		$obj->custom_status = $this->ReadOctets();
		$obj->filter_data = $this->ReadOctets();
		$obj->charactermode = $this->ReadOctets();
		$obj->instancekeylist = $this->ReadOctets();
		$obj->dbltime_expire = $this->ReadInt32();
		$obj->dbltime_mode = $this->ReadInt32();
		$obj->dbltime_begin = $this->ReadInt32();
		$obj->dbltime_used = $this->ReadInt32();
		$obj->dbltime_max = $this->ReadInt32();
		$obj->time_used = $this->ReadInt32();
		$obj->dbltime_data = $this->ReadOctets();
		$obj->storesize = $this->ReadInt16();
		$obj->petcorral = $this->ReadOctets();
		$obj->property = $this->ReadOctets();
		$obj->var_data = $this->ReadOctets();
		$obj->skills = $this->ReadOctets();
		$obj->storehousepasswd = $this->ReadOctets();
		$obj->waypointlist = $this->ReadOctets();		
		$obj->coolingtime = $this->ReadOctets();
		if (PROTOCOL_VER >= 27) {
			$obj->npc_relation = $this->ReadOctets();
		}
		if (PROTOCOL_VER >= 60) {			
			$obj->multi_exp_ctrl = $this->ReadOctets();
			$obj->storage_task = $this->ReadOctets();
			$obj->faction_contrib = $this->ReadOctets();
		}
		if (PROTOCOL_VER >= 63) $obj->force_data = $this->ReadOctets();
		if (PROTOCOL_VER >= 69) {
			$obj->online_award = $this->ReadOctets();
			$obj->profit_time_data = $this->ReadOctets();
		}
		if (PROTOCOL_VER >= 70) $obj->country_data = $this->ReadOctets();
		if (PROTOCOL_VER >= 101) {
			$obj->king_data = $this->ReadOctets();
			$obj->meridian_data = $this->ReadOctets();
			$obj->extraprop = $this->ReadOctets();
			$obj->title_data = $this->ReadOctets();
			$obj->reincarnation_data = $this->ReadOctets();
			$obj->realm_data = $this->ReadOctets();
			$obj->reserved2 = $this->ReadByte();
			$obj->reserved3 = $this->ReadByte();
		} else
		if (PROTOCOL_VER >= 88) {
			$obj->king_data = $this->ReadOctets();
			$obj->meridian_data = $this->ReadOctets();
			$obj->extraprop = $this->ReadOctets();
			$obj->title_data = $this->ReadOctets();
			$obj->reserved5 = $this->ReadInt32();
		} else
		if (PROTOCOL_VER >= 85) {
			$obj->king_data = $this->ReadOctets();
			$obj->meridian_data = $this->ReadOctets();
			$obj->extraprop = $this->ReadOctets();
			$obj->reserved43 = $this->ReadByte();
			$obj->reserved5 = $this->ReadInt32();
		} else
        	if (PROTOCOL_VER >= 70) {
			$obj->reserved4 = $this->ReadInt32();
			$obj->reserved5 = $this->ReadInt32();
		} else
        	if (PROTOCOL_VER == 69) {
			$obj->reserved32 = $this->ReadByte();
			$obj->reserved4 = $this->ReadInt32();
			$obj->reserved5 = $this->ReadInt32();
		} else
        	if (PROTOCOL_VER >= 63) {
			$obj->reserved31 = $this->ReadByte();
			$obj->reserved32 = $this->ReadInt16();
			$obj->reserved4 = $this->ReadInt32();
			$obj->reserved5 = $this->ReadInt32();
		} else
        	if (PROTOCOL_VER >= 60) {
			$obj->reserved3 = $this->ReadInt32();
			$obj->reserved4 = $this->ReadInt32();
			$obj->reserved5 = $this->ReadInt32();
		} else
		if (PROTOCOL_VER >= 27) {
			$obj->reserved1 = $this->ReadByte();
			$obj->reserved2 = $this->ReadInt16();
			$obj->reserved3 = $this->ReadInt32();
			$obj->reserved4 = $this->ReadInt32();
			$obj->reserved5 = $this->ReadInt32();
		} else
        	if (PROTOCOL_VER >= 7) {
			$obj->reserved1 = $this->ReadInt32();
			$obj->reserved2 = $this->ReadInt32();
			$obj->reserved3 = $this->ReadInt32();
			$obj->reserved4 = $this->ReadInt32();
		}
		return $obj;		
	}
	
	public function WriteRoleStatus( $obj ) {
		$this->WriteByte($obj->version);	
		$this->WriteInt32($obj->level);
		$this->WriteInt32($obj->level2);
		$this->WriteInt32($obj->exp);
		$this->WriteInt32($obj->sp);
		$this->WriteInt32($obj->pp);
		$this->WriteInt32($obj->hp);
		$this->WriteInt32($obj->mp);
		$this->WriteSingle($obj->posx);
		$this->WriteSingle($obj->posy);
		$this->WriteSingle($obj->posz);
		$this->WriteInt32($obj->worldtag);
		$this->WriteInt32($obj->invader_state);
		$this->WriteInt32($obj->invader_time);
		$this->WriteInt32($obj->pariah_time);
		$this->WriteInt32($obj->reputation);
		$this->WriteOctets($obj->custom_status);
		$this->WriteOctets($obj->filter_data);
		$this->WriteOctets($obj->charactermode);
		$this->WriteOctets($obj->instancekeylist);
		$this->WriteInt32($obj->dbltime_expire);
		$this->WriteInt32($obj->dbltime_mode);
		$this->WriteInt32($obj->dbltime_begin);
		$this->WriteInt32($obj->dbltime_used);
		$this->WriteInt32($obj->dbltime_max);
		$this->WriteInt32($obj->time_used);
		$this->WriteOctets($obj->dbltime_data);
		$this->WriteInt16($obj->storesize);		
		$this->WriteOctets($obj->petcorral);
		$this->WriteOctets($obj->property);
		$this->WriteOctets($obj->var_data);
		$this->WriteOctets($obj->skills);
		$this->WriteOctets($obj->storehousepasswd);
		$this->WriteOctets($obj->waypointlist);
		$this->WriteOctets($obj->coolingtime);
		if (PROTOCOL_VER >= 27) {
			$this->WriteOctets($obj->npc_relation);
		}
		if (PROTOCOL_VER >= 60) {			
			$this->WriteOctets($obj->multi_exp_ctrl);
			$this->WriteOctets($obj->storage_task);
			$this->WriteOctets($obj->faction_contrib);
		}
		if (PROTOCOL_VER >= 63) $this->WriteOctets($obj->force_data);
		if (PROTOCOL_VER >= 69) {
			$this->WriteOctets($obj->online_award);
			$this->WriteOctets($obj->profit_time_data);
		}
		if (PROTOCOL_VER >= 70) $this->WriteOctets($obj->country_data);
		if (PROTOCOL_VER >= 101) {
			$this->WriteOctets($obj->king_data);
			$this->WriteOctets($obj->meridian_data);
			$this->WriteOctets($obj->extraprop);
			$this->WriteOctets($obj->title_data);
			$this->WriteOctets($obj->reincarnation_data);
			$this->WriteOctets($obj->realm_data);
			$this->WriteByte($obj->reserved2);
			$this->WriteByte($obj->reserved3);
		} else
		if (PROTOCOL_VER >= 88) {
			$this->WriteOctets($obj->king_data);
			$this->WriteOctets($obj->meridian_data);
			$this->WriteOctets($obj->extraprop);
			$this->WriteOctets($obj->title_data);
			$this->WriteInt32($obj->reserved5);
		} else
		if (PROTOCOL_VER >= 85) {
			$this->WriteOctets($obj->king_data);
			$this->WriteOctets($obj->meridian_data);
			$this->WriteOctets($obj->extraprop);
			$this->WriteByte($obj->reserved43);
			$this->WriteInt32($obj->reserved5);
		} else
		if (PROTOCOL_VER >= 70) {
			$this->WriteInt32($obj->reserved4);
			$this->WriteInt32($obj->reserved5);
		} else
        	if (PROTOCOL_VER == 69) {
			$this->WriteByte($obj->reserved32);
			$this->WriteInt32($obj->reserved4);
			$this->WriteInt32($obj->reserved5);
		} else
        	if (PROTOCOL_VER >= 63) {
			$this->WriteByte($obj->reserved31);
			$this->WriteInt16($obj->reserved32);
			$this->WriteInt32($obj->reserved4);
			$this->WriteInt32($obj->reserved5);
		} else
        	if (PROTOCOL_VER >= 60) {
			$this->WriteInt32($obj->reserved3);
			$this->WriteInt32($obj->reserved4);
			$this->WriteInt32($obj->reserved5);
		} else
		if (PROTOCOL_VER >= 27) {
			$this->WriteByte($obj->reserved1);
			$this->WriteInt16($obj->reserved2);
			$this->WriteInt32($obj->reserved3);
			$this->WriteInt32($obj->reserved4);
			$this->WriteInt32($obj->reserved5);
		} else
        	if (PROTOCOL_VER >= 7) {
			$this->WriteInt32($obj->reserved1);
			$this->WriteInt32($obj->reserved2);
			$this->WriteInt32($obj->reserved3);
			$this->WriteInt32($obj->reserved4);
		}
	}

	public function ReadGMembers() {
		$obj = array();
		$cnt = $this->ReadCUInt32();
		if ($cnt == 0) return $obj;
		for ($a=0; $a < $cnt; $a++){
			$obj[$a] = new GMember();
			$obj[$a]->rid = $this->ReadInt32();
			$obj[$a]->role = $this->ReadByte();
		}
		return $obj;
	}

	public function ReadFactionInfo() {
		$obj = new GFactionInfo();
		$obj->fid = $this->ReadInt32();
		$obj->name = $this->ReadString();
		$obj->level = $this->ReadByte();
		$obj->masterid = $this->ReadInt32();
		$obj->masterrole = $this->ReadByte();
		$obj->members = $this->ReadGMembers();
		$obj->announce = $this->ReadString();
		$obj->sysinfo = $this->ReadOctets();
		return $obj;
	}
	
	public function ReadRolePocket() {
		$obj = new RolePocket();
		$obj->capacity = $this->ReadInt32();
		$obj->timestamp = $this->ReadInt32();
		$obj->money = $this->ReadInt32();
		$obj->items = $this->ReadGRoleItems();
		$obj->reserved1 = $this->ReadInt32();
		$obj->reserved2 = $this->ReadInt32();
		return $obj;
	}
	
	public function WriteRolePocket( $obj ) {
		$this->WriteInt32($obj->capacity);
		$this->WriteInt32($obj->timestamp);
		$this->WriteInt32($obj->money);
		$this->WriteGRoleItems($obj->items);
		$this->WriteInt32($obj->reserved1);
		$this->WriteInt32($obj->reserved2);
	}
	
	public function ReadRoleStorehouse() {
		$obj = new RoleStorehouse();
		$obj->capacity = $this->ReadInt32();
		$obj->money = $this->ReadInt32();
		$obj->items = $this->ReadGRoleItems();
		if (PROTOCOL_VER < 27) {
			$obj->reserved1 = $this->ReadInt32();
			$obj->reserved2 = $this->ReadInt32();
		} else {
			$obj->size1 = $this->ReadByte();
			$obj->size2 = $this->ReadByte();
			$obj->dress = $this->ReadGRoleItems();
			$obj->material = $this->ReadGRoleItems();
			if (PROTOCOL_VER >= 101) {
				$obj->size3 = $this->ReadByte();
				$obj->generalcard = $this->ReadGRoleItems();
				$obj->reserved = $this->ReadInt16();
			} else {
				$obj->reserved = $this->ReadInt32();
			}
		}
		return $obj;
	}
	
	public function ReadUserStorehouse() {
		$obj = new UserStorehouse();
		$obj->capacity = $this->ReadInt32();
		$obj->money = $this->ReadInt32();
		$obj->items = $this->ReadGRoleItems();
		$obj->reserved1 = $this->ReadInt32();
		$obj->reserved2 = $this->ReadInt32();
		$obj->reserved3 = $this->ReadInt32();
		$obj->reserved4 = $this->ReadInt32();
		return $obj;
	}
	
	public function WriteRoleStorehouse( $obj ) {

		$this->WriteInt32($obj->capacity);
		$this->WriteInt32($obj->money);
		$this->WriteGRoleItems($obj->items);
		if (PROTOCOL_VER < 27) {
			$this->WriteInt32($obj->reserved1);
			$this->WriteInt32($obj->reserved2);
		} else {
			$this->WriteByte($obj->size1);
			$this->WriteByte($obj->size2);
			$this->WriteGRoleItems($obj->dress);
			$this->WriteGRoleItems($obj->material);
			if (PROTOCOL_VER >= 101) {
				$this->WriteByte($obj->size3);
				$this->WriteGRoleItems($obj->generalcard);
				$this->WriteInt16($obj->reserved);
			} else {
				$this->WriteInt32($obj->reserved);
			}
		}
	}
	
	public function WriteUserStorehouse( $obj ) {
		$this->WriteInt32($obj->capacity);
		$this->WriteInt32($obj->money);
		$this->WriteGRoleItems($obj->items);
		$this->WriteInt32($obj->reserved1);
		$this->WriteInt32($obj->reserved2);
		$this->WriteInt32($obj->reserved3);
		$this->WriteInt32($obj->reserved4);
	}
	
	public function ReadRoleTask() {
		$obj = new RoleTask();
		$obj->task_data = $this->ReadOctets();
		$obj->task_complete = $this->ReadOctets();
		$obj->task_finishtime = $this->ReadOctets();
		$obj->task_inventory = $this->ReadGRoleItems();
		return $obj;
	}
	
	public function WriteRoleTask( $obj ) {
		$this->WriteOctets($obj->task_data);
		$this->WriteOctets($obj->task_complete);
		$this->WriteOctets($obj->task_finishtime);
		$this->WriteGRoleItems($obj->task_inventory);
	}

}
