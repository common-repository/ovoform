<?php

namespace Ovoform\BackOffice\Facade;

use Ovoform\BackOffice\Facade\Facade;

class Session extends Facade{
    protected static function getFacadeAccessor()
    {
        return 'session';
    }
}