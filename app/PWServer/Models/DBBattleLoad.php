<?php

namespace App\PWServer\Models;

use App\PWServer\PacketStream;
use App\PWServer\Models\GTerritory;

class DBBattleLoad {
	private $type;		// cuInt32
	private $answlen;	// cuInt32
	var $retcode;		// Int32
	var $unk;		// Int16
	var $count;		// Byte
	var $terr;		// array of GTerritoty
	public $terrname = array (
				"","Замерзшие земли","Ледяной путь","Ущелье лавин","Лесной хребет","Древний путь","Роковой город","Город истоков","Великая стена","Равнина побед",
				"Город мечей","Сломанные горы","Крепость-Компас","Светлые горы","Деревня огня","Перечный луг","Равнина ветров","Поселок ветров","Изумрудный лес",
				"Земли драконов","Город оборотней","Шелковые горы","Портовый город","Город Драконов","Пахучий склон","Плато заката","Река Ришоу","Длинный откос",
				"Безопасный путь","Небесное озеро","Небесные скалы","Долина орхидей","Персиковый док","Высохшее море","Горы лебедя","Город Перьев","Тренога Юй-вана",
				"Бездушная топь","Туманная чаща","Поле костей","Южные горы","Белые горы","Черные горы","Горы мечтателей","Порт мечты");
	public $error = 0;	// Ошибка при разборке пакета

	//enum BATTLE_SETREASON {_BATTLE_INITIALIZE, _BATTLE_SETTIME};
	
	function BattleSet( $reason, $fp ) {
		$p = new PacketStream();
		$p->WriteInt32(2147483649);	
		$p->WriteInt16($reason);
		$p->WriteByte($this->count);
		foreach ($this->terr as $i => $val){
			$p->WriteInt16($val->id);
			$p->WriteInt16($val->level);			
			$p->WriteInt32($val->owner);
			$p->WriteInt32($val->occupy_time);
			$p->WriteInt32($val->challenger);
			$p->WriteInt32($val->deposit);
			$p->WriteInt32($val->cutoff_time);
			$p->WriteInt32($val->battle_time);
			$p->WriteInt32($val->bonus_time);
			$p->WriteInt32($val->color);
			$p->WriteInt32($val->status);
			$p->WriteInt32($val->timeout);
			$p->WriteInt32($val->maxbonus);
			$p->WriteInt32($val->challenge_time);
			$p->WriteOctets($val->challengerdetails);			
			$p->WriteByte($val->reserved1);
			$p->WriteByte($val->reserved2);
			$p->WriteByte($val->reserved3);
		}
		$packet = cuint(864).$p->wcount.$p->buffer;		
		if ( $fp ) {
			fputs($fp, $packet);
			$data = fread($fp,8096);
			//fclose($fp);
			$a=new PacketStream($data);
			$this->type = $a->ReadCUInt32();
			$this->answlen = $a->ReadCUInt32();
			$this->retcode = $a->ReadInt32();
			$this->unk = $a->ReadInt16();
			$this->error = 0;
			if ( $a->done != true ) $this->error = 1;		// Пакет разобран не до конца
			if ( $a->overflow == true ) $this->error = 2;	// Длинна пакета меньше ожидаемой
			return $data;
		}
	}
	
	function BattleLoad( $fp ) {
		$p = new PacketStream();
		$p->WriteInt32(2147483648);
		$p->WriteInt32(0);
		$packet=cuint(863).$p->wcount.$p->buffer;
		//$fp = fsockopen($ip, 29400);
		if ( $fp ) {
			fputs($fp, $packet);
			$data = ReadPWPacket($fp);
			//fclose($fp);
			$a = new PacketStream($data);
			$this->type = $a->ReadCUInt32();
			$this->answlen = $a->ReadCUInt32();
			$this->retcode = $a->ReadInt32();
			$this->unk = $a->ReadInt16();
			$this->count = $a->ReadCUInt32();
			$this->terr = array();
			for ($i=0; $i<$this->count; $i++) {
				$this->terr[$i] = new GTerritory();
				$this->terr[$i]->id = $a->ReadInt16();
				$this->terr[$i]->level = $a->ReadInt16();		
				$this->terr[$i]->owner = $a->ReadInt32();
				$this->terr[$i]->occupy_time = $a->ReadInt32();
				$this->terr[$i]->challenger = $a->ReadInt32();
				$this->terr[$i]->deposit = $a->ReadInt32();
				$this->terr[$i]->cutoff_time = $a->ReadInt32();
				$this->terr[$i]->battle_time = $a->ReadInt32();
				$this->terr[$i]->bonus_time = $a->ReadInt32();
				$this->terr[$i]->color = $a->ReadInt32();
				$this->terr[$i]->status= $a->ReadInt32();
				$this->terr[$i]->timeout = $a->ReadInt32();
				$this->terr[$i]->maxbonus = $a->ReadInt32();
				$this->terr[$i]->challenge_time = $a->ReadInt32();
				$this->terr[$i]->challengerdetails = $a->ReadOctets();
				$this->terr[$i]->reserved1 = $a->ReadByte();
				$this->terr[$i]->reserved2 = $a->ReadByte();
				$this->terr[$i]->reserved3 = $a->ReadByte();
			}
			$this->error = 0;
			if ( $a->done != true) $this->error = 1;		// Пакет разобран не до конца
			if ( $a->overflow == true ) $this->error = 2;	// Длинна пакета меньше ожидаемой
			return $data;
		}
	}

}
