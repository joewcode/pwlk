<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Inertia\Response;

use App\PWServer\PWServerFacade as PWServer;
use App\PWServer\PWsshFacade as PWssh;


use App\PWServer\Models\GRoleInventory;

class ServerAPIController extends Controller
{
    public function __construct() {
    }

    // Аккаунт по ID персонажи на нем
    public function getAccount( Request $request, Int $id ) {
        $data = Array();
        if ( $id > 0 ) {
            try {
                $acc = \DB::connection('pwsql')->select('SELECT ID, name, email, creatime FROM users WHERE ID = ?', [$id]);
                if ( !$acc ) throw new \Exception('Пользователь не найден');
                $dbacc = collect([PWServer::getUserRoles($id)])->toArray();
                $data = ['data' => $acc[0], 'roles' => $dbacc[0]];
            } catch (\Exception $e) {
                $data['error'] = $e->getMessage();
            }
        } else $data['error'] = 'Bad request params';
        return response()->json($data);
    }

    // Все аккаунты и персонажи на них
    public function getAccounts(Request $request) {
        $data = Array();
        try {
            $accs = \DB::connection('pwsql')->select('SELECT ID, name, email, creatime FROM users ');
            if ( !$accs ) throw new \Exception('Нет пользователей');
            foreach ( $accs as $acc ) {
                $data[$acc->ID] = ['account' => $acc, 'roles' => collect([PWServer::getUserRoles($acc->ID)])->toArray()];
            }
        } catch (\Exception $e) {
            $data['error'] = $e->getMessage();
        }
        return response()->json($data);
    }

    // Данные персонажа по ID
    public function getPlayer( Request $request, Int $id ) {
        $data = Array();
        try {
            $user = PWServer::getRole($id);
            if ( $user->value->id != $id ) throw new \Exception('Пользователь не найден');
            // dd($user);
            $data = preparePlayerData($user);
        } catch (\Exception $e) {
            $data['error'] = $e->getMessage();
        }
        return response()->json($data);
    }

    // Получить всех персонажей и аккаунт
    public function getPlayers( Request $request ) {
        $data = Array();
        try {
            $accs = \DB::connection('pwsql')->select('SELECT ID, name, email, creatime FROM users ');
            if ( !$accs ) throw new \Exception('Нет аккаунтов для поиска');
            foreach ( $accs as $acc ) {
                $roles = PWServer::getUserRoles($acc->ID);
                if ( !$roles ) continue;
                foreach ( $roles as $role ) {
                    $user = PWServer::getRole($role->id);
                    $data[$acc->ID] = ['account' => $acc, 'roles' => preparePlayerData($user)];
                }
            }
        } catch (\Exception $e) {
            $data['error'] = $e->getMessage();
        }
        return response()->json($data);
    }

    // Получить список персонажей онлайн
    public function getOnline( Request $request ) {
        $data = Array();
        try {
            $online = PWServer::getOnlineList();
            $data = $online;
        } catch (\Exception $e) {
            $data['error'] = $e->getMessage();
        }
        return response()->json($data);
    }

    // Получить инфу по карте территорий
    public function getTerritory( Request $request ) {
        $data = Array();
        try {
            $data = PWServer::getTerritory();
        } catch (\Exception $e) {
            $data['error'] = $e->getMessage();
        }
        return response()->json($data);
    }

    // Инфа по клану
    public function getFaction( Request $request, Int $id ) {
        $data = Array();
        try {
            $guild = PWServer::getFactionDetail($id);
            if ( $guild->fid < 1 ) throw new \Exception('Гильдия не найдена');
            $data = $guild;
        } catch (\Exception $e) {
            $data['error'] = $e->getMessage();
        }
        return response()->json($data);
    }

    
  
    
}
