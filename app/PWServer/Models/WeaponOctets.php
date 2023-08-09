<?php

namespace App\PWServer\Models;

use App\PWServer\PacketStream;
use App\PWServer\Models\SlotInfo;
use App\PWServer\Models\BonusInfo;

class WeaponOctets {        // Оружие
	public $retcode=0;		// Код ошибки
	public $LvlReq=0;		// Int16 требуемый уровень
	public $ClassReq=0;		// Int16 требуемый класс
	public $StrReq=0;		// Int16 требуется силы
	public $ConReq=0;		// Int16 требуется телосложения
	public $DexReq=0;		// Int16 требуется ловкости
	public $IntReq=0;		// Int16 требуется интеллекта
	public $CurDurab=0;		// Int32 текущая прочность
	public $MaxDurab=0;		// Int32 максимальная прочность
	public $ItemClass=0;		// Int16 класс вещи
	public $ItemFlag=0;		// Byte флаг вещи
	public $Creator='';		// String создатель вещи
	public $NeedAmmo=0;		// Int32 потребность в боеприпасах
	public $WeaponClass=0;		// Int32 Класс оружия
	public $Rang=0;		// Int32 Ранг
	public $AmmoType=0;		// Int32 Тип боеприпасов
	public $MinPhysAtk=0;		// Int32 Мин физ атака
	public $MaxPhysAtk=0;		// Int32 Макс физ атака
	public $MinMagAtk=0;		// Int32 Мин маг атака
	public $MaxMagAtk=0;		// Int32 Макс маг атака
	public $AtkSpeed=0;		// Int32 Скорость атаки
	public $Distance=0;		// Single Дальность
	public $FragDistance=0;	// Single Дистанция хрупкости
	public $SlotInfo;		// SlotInfo Информация о ячейках
	public $BonusInfo;		// BonusInfo
	public $EnableDopInt;		// есть или нет добавочный int в конце
	public $dopint;		// int32

	function ReadOctets($data) {
		$this->retcode=0;
		$p = new PacketStream($data);
		$this->LvlReq = $p->ReadInt16(false);
		$this->ClassReq = $p->ReadInt16(false);
		$this->StrReq = $p->ReadInt16(false);
		$this->ConReq = $p->ReadInt16(false);
		$this->DexReq = $p->ReadInt16(false);
		$this->IntReq = $p->ReadInt16(false);
		$this->CurDurab = $p->ReadInt32(false);
		$this->MaxDurab = $p->ReadInt32(false);
		$this->ItemClass = $p->ReadInt16(false);
		if ($this->ItemClass!=44) {
			$this->retcode=1;	// Класс вещи не соответствует оружию
			return false;
		}
		$this->ItemFlag = $p->ReadByte();
		$this->Creator = $p->ReadString();
		$this->NeedAmmo = $p->ReadInt32(false);
		$this->WeaponClass = $p->ReadInt32(false);
		$this->Rang = $p->ReadInt32(false);
		$this->AmmoType = $p->ReadInt32(false);
		$this->MinPhysAtk = $p->ReadInt32(false);
		$this->MaxPhysAtk = $p->ReadInt32(false);
		$this->MinMagAtk = $p->ReadInt32(false);
		$this->MaxMagAtk = $p->ReadInt32(false);
		$this->AtkSpeed = $p->ReadInt32(false);
		$this->Distance = $p->ReadSingle(false);
		$this->FragDistance = $p->ReadSingle(false);
		$this->SlotInfo = new SlotInfo();
		$this->SlotInfo->ReadSlotInfo($p);
		$this->BonusInfo = new BonusInfo();
		$this->BonusInfo->ReadBonusInfo($p);
		$this->EnableDopInt = false;
		if ($p->done==false) {
			$this->dopint = $p->ReadInt32(false);		// костыль
			$this->EnableDopInt = true;
		}
		if ($p->done==false) {
			//printf('Pos: %d, length: %d<br><br>', $p->pos, strlen($p->buffer));
			$this->retcode=2;	// Пакет не разобран до конца
			return false;
		}
		if ($p->overflow==true) {
			$this->retcode=3;	// Длинна пакета меньше ожидаемой
			return false;
		}
		return true;
	}

	function WriteOctets(){
		$p = new PacketStream();
		$p->WriteInt16($this->LvlReq,false);
		$p->WriteInt16($this->ClassReq,false);
		$p->WriteInt16($this->StrReq,false);
		$p->WriteInt16($this->ConReq,false);
		$p->WriteInt16($this->DexReq,false);
		$p->WriteInt16($this->IntReq,false);
		$p->WriteInt32($this->CurDurab,false);
		$p->WriteInt32($this->MaxDurab,false);
		$p->WriteInt16($this->ItemClass,false);
		$p->WriteByte($this->ItemFlag);
		$p->WriteString($this->Creator);
		$p->WriteInt32($this->NeedAmmo,false);
		$p->WriteInt32($this->WeaponClass,false);
		$p->WriteInt32($this->Rang,false);
		$p->WriteInt32($this->AmmoType,false);
		$p->WriteInt32($this->MinPhysAtk,false);
		$p->WriteInt32($this->MaxPhysAtk,false);
		$p->WriteInt32($this->MinMagAtk,false);
		$p->WriteInt32($this->MaxMagAtk,false);
		$p->WriteInt32($this->AtkSpeed,false);
		$p->WriteSingle($this->Distance,false);
		$p->WriteSingle($this->FragDistance,false);
		$this->SlotInfo->WriteSlotInfo($p);
		$this->BonusInfo->WriteBonusInfo($p);
		if ($this->EnableDopInt) $p->WriteInt32($this->dopint);
		return $p->buffer;
	}
}
