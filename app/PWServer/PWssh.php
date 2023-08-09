<?php

namespace App\PWServer;

use phpseclib3\Net\SSH2;

class PWssh {

    public function connect( $timeout = 5 ) {
        $ssh = false;
        try {
            $ssh = new SSH2( env('PW_SERVER'), 22, $timeout );
            if ( !$ssh->login('root', env('PW_SERVER_ROOT')) ) 
                throw new \Exception('Не верный пароль');
        } catch ( \Exception $e ) {
            // dd($e);
            $ssh = false;
        }
        return $ssh;
    }

    public function getCpuInfo( $ssh ) {
        $data = $ssh->exec('lscpu');
        $arr = explode("\n", $data);
        $res = Array();
        foreach( $arr as $k => $ar ) {
            $a = explode(":", trim($ar));
            if ( empty($a[0]) ) continue;
            $res[] = Array($a[0], trim($a[1]));
        }
        $res[11][1] = preg_replace("/\s/", " ", $res[11][1]);
        if ( mb_strlen($res[11][1]) > 16 ) $res[11][1] = mb_substr($res[11][1], 0, 17);
        return $res;
    }
    
    public function getArchInfo( $ssh ) {
        return trim($ssh->exec('uname -m'));
    }

    public function getReleaseInfo( $ssh ) {
        $data = $ssh->exec('lsb_release -d');
        $data = explode(":", $data)[1];
        return trim($data);
    }
    
    public function getRamInfo( $ssh ) {
        $data = $ssh->exec('cat /proc/meminfo');
        $arr = explode("\n", $data);
        $res = Array();
        foreach( $arr as $ar ) {
            $a = explode(":", trim($ar));
            if ( empty($a[0]) ) continue;
            $res[$a[0]] = explode(" ", trim($a[1]))[0];
        }
        $res["MemAvailable"] = (int)($res["MemAvailable"] / 1024);
        $res["MemFree"] = (int)($res["MemFree"] / 1024);
        $res["SwapTotal"] = (int)($res["SwapTotal"] / 1024);
        $res["SwapFree"] = (int)($res["SwapFree"] / 1024);
        return $res;
    }

    public function getIdServiceByName( $ssh, $name ) {
        return explode("\n", $ssh->exec('pidof '.$name));
    }

    public function getProcessList( $ssh ) {
        $data = $ssh->exec("ps -eo pid,%cpu,%mem,args --sort %mem | egrep 'gs|logservices|glinkd|authd|gdeliveryd|gacd|gfactiond|uniquenamed|gamedbd'");
        $arr = explode("\n", $data);
        $res = Array();
        foreach( $arr as $ar ) {
            if ( empty($ar) ) continue;
            $a = explode(" ", $ar);
            $id = (int)$a[1];
            $cpu = (float)$a[3];
            $mem = (float)$a[5];
            $a = array_diff_key($a, array_flip([0,1,2,3,4,5]));
            $command = implode(" ", $a);
            $ssid = $sid = str_replace("./", "", $a[6]);
            if ( $sid == "glinkd" ) {
                $i = explode(" ", $command)[2];
                $ssid = $sid.$i;
                $command = 'link '.$i;
            }
            if ( $sid == "java" ) {
                $ssid = 'authd';
                $sid = 'authd';
            }
            
            $res[$ssid] = Array( 'id' => $id, 'cpu' => $cpu, 'mem' => $mem, 'tag' => $sid, 'command' => $command );
        }
        return $res;
    }


}
