<?php

namespace App\PWServer\Models;

use App\PWServer\Models\pet_data;

class pet {
	public $index;			// int32
	public $data;			// pet_data
	
	function ReadPet( $p ) {
		$this->index = $p->ReadInt32();
		$this->data	= new pet_data();
		$this->data->ReadPetData($p);
	}
	
	function WritePet( $p ) {
		$p->WriteInt32($this->index);
		$this->data->WritePetData($p);
	}
}
