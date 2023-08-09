<?php

namespace App\PWServer;

use Illuminate\Support\Facades\Facade;

class PWServerFacade extends Facade {
    protected static function getFacadeAccessor() {
        return 'pwserver';
    }
}