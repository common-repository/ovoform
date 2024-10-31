<?php

namespace Ovoform\BackOffice\Facade;

use Ovoform\BackOffice\Facade\Facade;

class DB extends Facade{
    protected static function getFacadeAccessor()
    {
        return 'db';
    }
}