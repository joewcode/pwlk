<?php

namespace App\PWServer;

use App\PWServer\StaticData;
use App\PWServer\SQLEmitter;

use App\PWServer\Models\GUserRoles;
use App\PWServer\Models\GRole;
use App\PWServer\Models\GMListOnlineUser;
use App\PWServer\Models\DBBattleLoad;
use App\PWServer\Models\GFactionDetail;

class PWServer extends StaticData {

    public function text( $func, $par = null ) {
        return parent::$func($par);
    }

    public function sql() {
        $db = null;
        try {
            $db = new SQLEmitter();
            if ( !$db ) {
                throw new \Exception('Нет соединения с SQL PW');
            }
        } catch ( \Exception $e ) {
            $db = $e;
        }
        return $db;
    }


    # Получить персонажей на аккаунте
    public function getUserRoles( $id ) {
        $fp = fsockopen(GAMEDB_IP, GAMEDB_PORT);
        $r = new GUserRoles();
        $r->GetUserRoles($id, $fp);
        return $r->roles;
    }

    # Получить данные персонажа 
    public function getRole( $id ) {
        $fp = fsockopen(GAMEDB_IP, GAMEDB_PORT);
        $r = new GRole();
        $r->GetRole($id, 32, $fp);
        return $r;
    }

    # Получить список персонажей онлайн 
    public function getOnlineList() {
        $f = new GMListOnlineUser();
	    $f->GetList(1043); // GM ID
        return $f->userlist;
    }

    # Получить инфо по территориям
    public function getTerritory() {
        $fp = fsockopen(GAMEDB_IP, GAMEDB_PORT);
        $f = new DBBattleLoad();
	    $f->BattleLoad($fp);
        return $f->terr;
    }

    # Получить информацию по клану
    public function getFactionDetail( $id ) {
        $fp = fsockopen(GAMEDB_IP, GAMEDB_PORT);
        $f = new GFactionDetail($id, $fp);
        return $f;
    }

}

