<?php

namespace Ovoform\BackOffice\Facade;

use Exception;
use Ovoform\BackOffice\System;

abstract class Facade{

    protected static $resolvedInstance;

    public static function getFacadeRoot()
    {
        return static::resolveFacadeInstance(static::getFacadeAccessor());
    }

    protected static function getFacadeAccessor()
    {
        throw new Exception('Facade does not implement getFacadeAccessor method.');
    }

    protected static function resolveFacadeInstance($name)
    {
        $bindClass = new System::$facades[$name];
        return new $bindClass; 
    }

    public static function __callStatic($method, $args)
    {
        $instance = static::getFacadeRoot();

        if (! $instance) {
            throw new Exception('A facade root has not been set.');
        }

        if (!method_exists($instance,$method)) {
            throw new Exception('Method doesn\'t exists to the targeted class');
        }

        return $instance->$method(...$args);
    }
}