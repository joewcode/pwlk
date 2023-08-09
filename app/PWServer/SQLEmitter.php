<?php

namespace App\PWServer;

class SQLEmitter {
    

    public function __construct() {
        return \DB::connection('pwsql');
    }

    public function select( $s ) {
        return $this->$s;
    }

}
