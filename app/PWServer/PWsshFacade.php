<?php

namespace App\PWServer;

use Illuminate\Support\Facades\Facade;

class PWsshFacade extends Facade {
    protected static function getFacadeAccessor() {
        return 'pwssh';
    }
}
