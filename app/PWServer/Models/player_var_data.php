<?php

namespace App\PWServer\Models;

use App\PWServer\PacketStream;

class player_var_data {
	public $version;			// int32
	public $pk_count;			// int32
	public $pvp_cooldown;		// int32
	public $pvp_flag;			// bool
	public $dead_flag;			// char
	public $is_drop;			// bool
	public $resurrect_state;		// bool
	public $resurrect_exp_reduce;	// float
	public $instance_hash_key1;	// int32	
	public $instance_hash_key2;	// int32	
	public $trashbox_size;		// int32
	public $last_instance_timestamp;	// int32
	public $last_instance_tag;		// int32
	public $last_instance_pos_x;	// float
	public $last_instance_pos_y;	// float
	public $last_instance_pos_z;	// float
	public $dir; 			// int32
	public $resurrect_hp_factor;	// float
	public $resurrect_mp_factor;	// float

	function ReadVarData($data){
		$p = new PacketStream($data);
		$this->version = $p->ReadInt32(false);
		$this->pk_count = $p->ReadInt32(false);
		$this->pvp_cooldown = $p->ReadInt32(false);
		$this->pvp_flag = $p->ReadByte();
		$this->dead_flag = $p->ReadByte();
		$this->is_drop = $p->ReadByte();
		$this->resurrect_state = $p->ReadByte();
		$this->resurrect_exp_reduce = $p->ReadSingle(false);
		$this->instance_hash_key1 = $p->ReadInt32(false);
		$this->instance_hash_key2 = $p->ReadInt32(false);
		$this->trashbox_size = $p->ReadInt32(false);
		$this->last_instance_timestamp = $p->ReadInt32(false);
		$this->last_instance_tag = $p->ReadInt32(false);
		$this->last_instance_pos_x = $p->ReadSingle(false);
		$this->last_instance_pos_y = $p->ReadSingle(false);
		$this->last_instance_pos_z = $p->ReadSingle(false);
		$this->dir = $p->ReadInt32(false);
		$this->resurrect_hp_factor = $p->ReadSingle(false);
		$this->resurrect_mp_factor = $p->ReadSingle(false);
	}

	function WriteVarData(){
		$p = new PacketStream();
		$p->WriteInt32($this->version,false);
		$p->WriteInt32($this->pk_count,false);
		$p->WriteInt32($this->pvp_cooldown,false);
		$p->WriteByte($this->pvp_flag);
		$p->WriteByte($this->dead_flag);
		$p->WriteByte($this->is_drop);
		$p->WriteByte($this->resurrect_state);
		$p->WriteSingle($this->resurrect_exp_reduce,false);
		$p->WriteInt32($this->instance_hash_key1,false);
		$p->WriteInt32($this->instance_hash_key2,false);
		$p->WriteInt32($this->trashbox_size,false);
		$p->WriteInt32($this->last_instance_timestamp,false);
		$p->WriteInt32($this->last_instance_tag,false);
		$p->WriteSingle($this->last_instance_pos_x,false);
		$p->WriteSingle($this->last_instance_pos_y,false);
		$p->WriteSingle($this->last_instance_pos_z,false);
		$p->WriteInt32($this->dir,false);
		$p->WriteSingle($this->resurrect_hp_factor,false);
		$p->WriteSingle($this->resurrect_mp_factor,false);
		return $p->buffer;
	}
}
