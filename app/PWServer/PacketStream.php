<?php

namespace App\PWServer;

use App\PWServer\PacketStreamModel;

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


class PacketStream extends PacketStreamModel {
	
    public string $buffer = "";
    public int $count = 0;
    public $wcount;
    public int $pos = 0;             // Позиция байта
    public bool $done = false;	    // Окончание процесса чтения пакета
	public bool $overflow = false;	// Перебор чтения из пакета

    public function __construct( $string = "", $start = false ) {
        $this->buffer = $string; 
		if ( $start ) $this->pos = $start;
		$this->count = strlen($string);
    }

	public function ReadByte() {
		if ( $this->pos < $this->count ) {
			$t = unpack("C", substr($this->buffer, $this->pos, 1));
			$this->pos++;
			if ( $this->pos >= $this->count ) {
				$this->done = true;
			}
			return $t[1];
		} else {
			$this->overflow = true;
			return 0;
		}
	}
	
	public function UpdateWriteCount() {
		$this->wcount = cuint( strlen($this->buffer) );
	}

	public function WriteByte( $b ) {
		$this->buffer.= pack("C", $b);	
		$this->UpdateWriteCount();
	}

	public function ReadInt32( $bigendian = true ) {
		if ( $this->pos+3 < $this->count ) {
			$t = ( $bigendian == true ) 
				? unpack("i", strrev(substr($this->buffer, $this->pos, 4)))
				: unpack("i", substr($this->buffer, $this->pos, 4));
			$this->pos+= 4;
			if ( $this->pos >= $this->count ) {
				$this->done = true;
			}
			return $t[1];
		} else {
			$this->overflow = true;
			return 0;
		}
	}

	public function ReadInt64( $bigendian = true ) {
		if ( $this->pos+7 < $this->count ) {
			if ( PHP_VERSION_ID >= 50603 && PHP_INT_SIZE != 4 ) {
				// Если пыха 5.6.3+
				$t = ( $bigendian == true )
					? unpack("q", strrev(substr($this->buffer, $this->pos, 8)))
					: unpack("q", substr($this->buffer, $this->pos, 8));

			} else {
				// Если нет - используем велосипед
				$t = ( $bigendian == true )
					? unpack("i*", strrev(substr($this->buffer, $this->pos, 8)))
					: unpack("i*", substr($this->buffer, $this->pos, 8));
				$t[1] = $t[2] << 32 | $t[1] & 0xFFFFFFFF;
			}
			$this->pos+= 8;
			if ( $this->pos >= $this->count )
				$this->done = true;
			return $t[1];
		} else {
			$this->overflow = true;
			return 0;
		}
	}

	public function WriteInt32( $b, $bigendian = true ) {
		$this->buffer.= ( $bigendian == true ) ? pack("N", $b) : pack("V", $b);
		$this->UpdateWriteCount();		
	}

	public function WriteInt64( $b, $bigendian = true ) {
		if ( PHP_VERSION_ID >= 50603 && PHP_INT_SIZE != 4 ) {
			// Если пыха 5.6.3+
			$this->buffer.= ($bigendian) ? pack("J", $b) : pack("P", $b);
		} else {
			$t = pack("i", $b).pack("i", $b >> 32);
			if ($bigendian) $t = strrev($t);
			$this->buffer.= $t;
		}
		$this->UpdateWriteCount();		
	}

	public function ReadInt16( $bigendian = true ) {
		if ( $this->pos+1 < $this->count ) {
			$t = ( $bigendian == true ) 
				? unpack("n", substr($this->buffer, $this->pos, 2))
				: unpack("v", substr($this->buffer, $this->pos, 2));
			$this->pos+= 2;
			if ( $this->pos >= $this->count )
				$this->done = true;
			return $t[1];
		} else {
			$this->overflow = true;
			return 0;
		}
	}

	public function WriteInt16( $b, $bigendian = true ) {
		$this->buffer.= ( $bigendian == true ) ? pack("n", $b) : pack("v", $b);
		$this->UpdateWriteCount();	
	}

	public function ReadSingle( $bigendian = true ) {
		if  ($this->pos+3 < $this->count ) {
			$t = ( $bigendian == true )
				? unpack("f", strrev(substr($this->buffer, $this->pos, 4)))
				: unpack("f", substr($this->buffer, $this->pos, 4));
			$this->pos+= 4;
			if ( $this->pos >= $this->count ) 
				$this->done = true;
			return $t[1];
		} else {
			$this->overflow = true;
			return 0;
		}
	}

	public function WriteSingle( $b, $bigendian = true ) {
		$this->buffer.= ( $bigendian == true ) ? strrev(pack("f", $b)) : pack("f", $b);
		$this->UpdateWriteCount();	
	}

	public function ReadCUInt32() {
		$b = $this->ReadByte();
		if ( $this->overflow == true ) 
			return 0;
		$this->pos-= 1;
		switch ($b & 0xE0) {
			case 224:
				$b = $this->ReadByte();
				return $this->ReadInt32();				
			case 192:
				return $this->ReadInt32() & 0x3FFFFFFF;				
			case 128:
			case 160:
				return $this->ReadInt16() & 0x7FFF;				
		}
		return $this->ReadByte();
	}

	public function WriteCUInt32( $b, $bigendian = true ) {
		if ( $b <= 127 ) {
			$this->WriteByte($b);
      	} elseif ( $b < 16384 ) {
			$this->WriteInt16($b | 0x8000, $bigendian);
      	} elseif ( $b < 536870912 ) {
			$this->WriteInt32($b | 0xC0000000);
      	}		
	}

	public function ReadOctets() {
		//$t=new Octets();
		if ( $this->pos < $this->count ) {			
			$size = $this->ReadCUInt32();
			$t = substr($this->buffer, $this->pos, $size);
			$this->pos+= $size;
			if ($this->pos >= $this->count)
				$this->done = true;
			//echo $size.' - '.$this->pos.' - '.$this->count.'<br>';
			# return $t;
			return base64_encode($t);
		};
	}

	public function ReadString() {
		//$t=new Octets();
		if ( $this->pos < $this->count ) {			
			$size = $this->ReadCUInt32();
			$t = substr($this->buffer, $this->pos, $size);
			$this->pos+= $size;
			// $t = iconv("UTF-16", "UTF-8", $t);
			$t = iconv("UTF-16LE", "UTF-8", $t);
			if ( $this->pos >= $this->count )
				$this->done = true;
			return $t;
		};
	}

	public function WriteOctets( $b ) {		
		$this->buffer.= cuint(strlen($b)).$b;
		$this->UpdateWriteCount();	
	}

	public function WriteString( $b ) {
		$a = iconv("UTF-8", "UTF-16LE", $b);
		$this->buffer.= cuint(strlen($a)).$a;	
		$this->UpdateWriteCount();	
	}

}
