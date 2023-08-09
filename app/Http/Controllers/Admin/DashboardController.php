<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

use Inertia\Inertia;
use Inertia\Response;

use App\PWServer\PWServerFacade as PWServer;
use App\PWServer\PWsshFacade as PWssh;

class DashboardController extends Controller
{
    //
    public function index(Request $request): Response {
        $ssh = PWssh::connect(1);
        $data = Array();
        if ( $ssh ) {
            $locations = PWServer::text('instances');
            $processes = PWssh::getProcessList($ssh);
            $locs = Array();
            foreach( $locations as $key => $name ) {
                $status = isset($processes[$key]) ? $processes[$key] : false;
                $locs[$key] = Array('id' => $key, 'name' => $name, 'status' => $status);
            }
            $proc = Array();
            $proc_list = Array('gs','logservices','glinkd','authd','gdeliveryd','gacd','gfactiond','uniquenamed','gamedbd');
            foreach( $processes as $key => $process ) {
                if ( !in_array($process['tag'], $proc_list) ) continue;
                $proc[$key] = $process;
            }
            $data['server'] = true;
            $data['server_cpu'] = PWssh::getCpuInfo($ssh); // [3][1] - CPU(s)
            $data['server_arch'] = PWssh::getArchInfo($ssh);
            $data['server_rel'] = PWssh::getReleaseInfo($ssh);
            $data['server_ram'] = PWssh::getRamInfo($ssh);
            $data['server_locations'] = $locs;
            $data['server_processes'] = $proc;
        } else $data['server'] = false;
        // dd( $data );
        return Inertia::render('Admin/Dashboard', $data);
    }
    //
    public function accounts(Request $request): Response {
        $accs = PWServer::sql()->select('SELECT ID, name, email, creatime FROM users ');
        if ( !$accs ) $accs = 'Нет пользователей';
        return Inertia::render('Admin/Accounts', ['accounts' => $accs]);
    }

    public function account(Request $request, $id): Response {
        $accs = \DB::connection('pwsql')->select('SELECT ID, name, email, creatime FROM users ');
        if ( !$accs ) $accs = 'Нет пользователей';
        return Inertia::render('Admin/Accounts', ['accounts' => $accs]);
    }

    public function persons(Request $request): Response {
        $accs = \DB::connection('pwsql')->select('SELECT ID, name, email, creatime FROM users ');
        $data = Array();
        if ( $accs ) {
            foreach ( $accs as $acc ) {
                $roles = PWServer::getUserRoles($acc->ID);
                foreach ( $roles as $role ) {
                    $user = PWServer::getRole($role->id);
                    $data[$role->id] = preparePlayerData($user);
                }
            }
        } else $data = false;
        return Inertia::render('Admin/Persons', ['persons' => $data]);
    }

    public function person( Request $request, $id ): Response {
        $role = PWServer::getRole($id)->value;
        return Inertia::render('Admin/Person', ['person' => (array)$role]);
    }
    
}
