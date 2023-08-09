<?php

namespace App\PWServer\Models;

use App\PWServer\PacketStream;
use App\PWServer\Models\SlotInfo;
use App\PWServer\Models\BonusInfo;

class ArmorOctets {			// Броня и бижутерия
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
	public $PhysDef=0;		// Int32 Защита в броне, физ атака в бижутерии
	public $Dodge=0;		// Int32 Уклон в броне, маг атака в бижутерии
	public $Mana=0;		// Int32 Мана
	public $HP=0;			// Int32 Здоровье
	public $MetalDef=0;		// Int32 Защита от металла
	public $WoodDef=0;		// Int32 Защита от дерева
	public $WaterDef=0;		// Int32 Защита от воды
	public $FireDef=0;		// Int32 Защита от огня
	public $EarthDef=0;		// Int32 Защита от земли
	public $SlotInfo;		// SlotInfo Информация о ячейках
	public $BonusInfo;		// BonusInfo

	function ReadOctets($data){
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
		if ($this->ItemClass!=36) {
			$this->retcode=1;	// Класс вещи не соответствует броне
			return false;
		}
		$this->ItemFlag = $p->ReadByte();
		$this->Creator = $p->ReadString();
		$this->PhysDef = $p->ReadInt32(false);
		$this->Dodge = $p->ReadInt32(false);
		$this->Mana = $p->ReadInt32(false);
		$this->HP = $p->ReadInt32(false);
		$this->MetalDef = $p->ReadInt32(false);
		$this->WoodDef = $p->ReadInt32(false);
		$this->WaterDef = $p->ReadInt32(false);
		$this->FireDef = $p->ReadInt32(false);
		$this->EarthDef = $p->ReadInt32(false);		
		$this->SlotInfo = new SlotInfo();
		$this->SlotInfo->ReadSlotInfo($p);
		$this->BonusInfo = new BonusInfo();
		$this->BonusInfo->ReadBonusInfo($p);
		if ($p->done==false) {
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
		$p->WriteInt32($this->PhysDef,false);
		$p->WriteInt32($this->Dodge,false);
		$p->WriteInt32($this->Mana,false);
		$p->WriteInt32($this->HP,false);
		$p->WriteInt32($this->MetalDef,false);
		$p->WriteInt32($this->WoodDef,false);
		$p->WriteInt32($this->WaterDef,false);
		$p->WriteInt32($this->FireDef,false);
		$p->WriteInt32($this->EarthDef,false);
		$this->SlotInfo->WriteSlotInfo($p);
		$this->BonusInfo->WriteBonusInfo($p);
		return $p->buffer;
	}
}
