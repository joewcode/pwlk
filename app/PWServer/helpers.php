<?php

define('PROTOCOL_VER', env('PW_PROTOCOL_VER', 145));
define('GAMEDB_PORT', env('PW_GAMEDB_PORT', 29400));
define('DELIVERY_PORT', env('PW_DELIVERY_PORT', 29100));
define('GAMEDB_IP', env('PW_SERVER', '192.168.0.104'));
define('DELIVERY_IP', env('PW_SERVER', '192.168.0.104'));
// dd(PROTOCOL_VER);
//
define('b_func1', 0x1 << 17);
define('b_func2', 0x1 << 16);
define('b_func3', 0x1 << 15);
define('b_func4', 0x1 << 14);
define('b_func5', 0x1 << 13);

use App\PWServer\PWServerFacade as PWServer;
use App\PWServer\PacketStream;


function cuint( $data ) {
	if ( $data <= 0x7F ) return pack("C", $data);
    if ( $data < 16384 ) return pack("n", ($data | 0x8000));
    if ( $data < 536870912 ) return pack("N", ($data | 0xC0000000));
    return pack("c", -32) . pack("N", $data);
}

function ReadPWPacket( $fp ) {
	$data = fread($fp, 4096);
	$a = new PacketStream($data);
	$type = $a->ReadCUInt32();
	$answlen = $a->ReadCUInt32();
	$q = $a->pos;
	unset($a);
	$rp = 4096;	
	while ( strlen($data) < $answlen + $q ) {
		$rest = $answlen - strlen($data) + $q;
		$rp = fread($fp, $rest);
		if ( !$rp ) break;
		$data.= $rp;
	}	
	return $data;
}

// Сообщения в чат
function pw_broadcast( $channel, $emoticon, $roleid, $localsid, $msg, $ip='127.0.0.1' ) {
	$p = new PacketStream();
	$p->WriteByte($channel);
	$p->WriteByte($emoticon);
	$p->WriteInt32($roleid);
	$p->WriteInt32($localsid);
	$p->WriteString($msg);
	if ( PROTOCOL_VER >= 60 ) $p->WriteOctets('');
	$packet = cuint(79).$p->wcount.$p->buffer;
	$fp = fsockopen($ip, DELIVERY_PORT);
	if ( $fp ) {
		$data = fread($fp, 8096);		
		fputs($fp, $packet);		
		$data = fread($fp, 8096);
		fclose($fp); //Закрываем сокет
		return $data;
	}
}

function Int2Octets( $i ) {
	if ( $i == 0 ) return '';
	$p = new PacketStream();
	$p->WriteInt32($i);
	return $p->buffer;
}

function Octets2Int( $o ) {
	if ( $o == '' ) return 0;
	$p = new PacketStream($o);
	//$p->ReadByte();
	return $p->ReadInt32();
}

function Octets2String( $o ) {
	if ( $o == '' ) return $o;
	$p = new PacketStream($o);
	return $p->ReadString();
}

function CalcLevelProrepty($cls, $level, &$pr){
	$l = GetClassConfig($cls);
	$pr->max_hp = ($level - 1) * $l->lvlup_hp + $pr->vitality * $l->vit_hp;
	$pr->max_mp = ($level-1) * $l->lvlup_mp + $pr->energy * $l->eng_mp;
	$pr->damage_low = 1 + floor(($level-1) * $l->lvlup_dmg);
	$pr->damage_high = $pr->damage_low;
	$pr->damage_magic_low = 1 + floor(($level-1) * $l->lvlup_magic);
	$pr->damage_magic_high = $pr->damage_magic_low;
}

function ProcessStat(&$stat, $min, &$rest){
	if ($rest <= 0) return;
	if ($stat > $min) {
		if ($stat >= ($rest + $min)){
			$stat -= $rest;
			$rest = 0;
		} else {
			$a = $stat; $stat = $min;
			$rest = $rest - $a + $min;
		}
	}
}

function RecalculateLevelStats($cls, $oldlevel, $newlevel, &$pp, &$pr){
	if ($oldlevel == $newlevel) return false;
	if ($oldlevel < $newlevel) {
	  // Добавляем статы
	  $pp += ($newlevel - $oldlevel) * 5;
	} else {	
	  // Режем статы
	  $rest = ($oldlevel - $newlevel) * 5;
	  ProcessStat($pp, 0, $rest);
	  ProcessStat($pr->vitality, 5, $rest);
	  ProcessStat($pr->energy, 5, $rest);
	  ProcessStat($pr->strength, 5, $rest);
	  ProcessStat($pr->agility, 5, $rest);
	  if ($rest != 0) return false;
	}
	CalcLevelProrepty($cls, $newlevel, $pr);
	return true;
}

function preparePlayerData( $user ) {
	$a = Array();
	$a['id'] = $user->value->id;
	$a['userid'] = $user->value->userid;
	$a['name'] = $user->value->name;
	$a['level'] = $user->value->status->level;
	$a['education'] = PWServer::text('education', $user->value->status->level2);
	$a['hp'] = $user->value->status->hp;
	$a['mp'] = $user->value->status->mp;
	$a['worldtag'] = $user->value->status->worldtag;
	$a['reputation'] = $user->value->status->reputation;
	$a['race'] = $user->value->race;
	$a['cls'] = $user->value->cls;
	$a['gender'] = PWServer::text('gender', $user->value->gender);
	$a['factionid'] = $user->value->factionid;
	$a['factionrole'] = PWServer::text('factionrole', $user->value->factionrole); // $user->value->factionrole; // 
	$a['create_time'] = date('d.m.Y H:i', $user->value->create_time);
	$a['lastlogin_time'] = date('d.m.Y H:i', $user->value->lastlogin_time);
	$a['time_used'] = sprintf('%02dh %02dm', ($user->value->status->time_used / 3600), ($user->value->status->time_used / 60 % 60));
	return $a;
}

function checkParseErr( $a ) {
	$r = ( $a->done != true ) ? 1 : 0;	// 1 - Пакет разобран не до конца
	if ( $a->overflow == true ) $r = 2;	// Длинна пакета меньше ожидаемой
	return $r;
}
