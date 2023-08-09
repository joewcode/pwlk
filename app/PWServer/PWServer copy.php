<?php

namespace App\PWServer;

use App\PWServer\StaticData;

use App\PWServer\Models\GRole;
use App\PWServer\Models\DBBattleLoad;
use App\PWServer\Models\GMListOnlineUser;
use App\PWServer\Models\GFactionDetail;
use App\PWServer\Models\GUserRoles;


class PWServer {

    public function text() {
        return new StaticData;
    }

    public function getAccount( $id ) {
        $fp = fsockopen(GAMEDB_IP, GAMEDB_PORT);
        $r = new GUserRoles();
        $r->GetUserRoles($id, $fp);
        return $r;
    }

    public function getOnlineList() {
        $f = new GMListOnlineUser();
	    $f->GetList(1043); // GM ID
        return $f;
    }

    public function getUserId( $id ) {
        $fp = fsockopen(GAMEDB_IP, GAMEDB_PORT);
        $r = new GRole();
        $r->GetRole($id, 32, $fp);
        return $r;
    }

    public function getTerritory() {
        $fp = fsockopen(GAMEDB_IP, GAMEDB_PORT);
        $f = new DBBattleLoad();
	    $f->BattleLoad($fp);
        return $f;
    }

    public function getFactionDetail( $id ) {
        $fp = fsockopen(GAMEDB_IP, GAMEDB_PORT);
        $f = new GFactionDetail($id, $fp);
        return $f;
    }

}

