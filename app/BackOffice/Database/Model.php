<?php

namespace Ovoform\BackOffice\Database;

use Exception;
use Ovoform\BackOffice\Database\QueryBuilder;

class Model{
    protected static $table;
    protected static $attributes = [];
    public $attrs = [];
    public $data;
    public static $model;

    public static $storedData = [];

    private static $methods = [
        'get',
        'first',
        'where',
        'orWhere',
        'whereIn',
        'whereBetween',
        'limit',
        'skip',
        'orderBy',
        'selectRaw',
        'sum',
        'min',
        'max',
        'count',
        'insert',
        'update',
        'delete',
        'find',
        'findOrFail',
        'avg',
        'paginate',
        'save',
        'filter',
        'distinct',
        'truncate',
        'with',
        'withCount',
        'withSum',
        'whereHas',
    ];

    public function __construct()
    {
        self::$storedData[static::class] = [];
    }

    public static function __callStatic($name, $arguments)
	{
        $table = static::$table;
        $model = static::class;
        self::checkMethod($name);
        $builder = new QueryBuilder($model,$table);
        return $builder->$name(...$arguments);
        
	}

    public function __call($name, $arguments)
	{
        $table = static::$table;
        self::checkMethod($name);
        $model = static::class;
        
        $builder = new QueryBuilder($model,$table);
        if ($name == 'save') {
            $builder->attributes = self::$attributes[static::class];
        }
        $callMethod = $builder->$name(...$arguments);
        if($name == 'save' && !array_key_exists('for_save', self::$storedData[static::class] ?? [])){
            $insertId['id'] = $callMethod;
            $this->attrs[static::class] = array_merge($insertId,self::$attributes[static::class]);
        }
        return $callMethod;
	}

    public function __get($name)
    {
        if ($name == 'result_data') {
            return self::$storedData[static::class][$name];
        }else{
            if (!empty($this->attrs) && array_key_exists(static::class,$this->attrs) && array_key_exists($name,$this->attrs[static::class])){
                return $this->attrs[static::class][$name];
            }else{
                if (array_key_exists('result_data',self::$storedData[static::class])) {
                    $databaseResult = self::$storedData[static::class]['result_data'];
                    return $databaseResult->$name;
                }
            }
        }
    }

    public function __set($name,$value)
    {
        if ($name != 'result_data' && $name != 'for_save') {
            self::$attributes[static::class][$name] = $value;
            $this->attrs[static::class][$name] = $value;
        }else{
            self::$storedData[static::class][$name] = $value;
        }

        if (array_key_exists(static::class,self::$storedData) && array_key_exists('result_data',self::$storedData[static::class])) {
            $this->data = self::$storedData[static::class]['result_data'];
        }
    }

    private static function checkMethod($method)
    {
        if (!(in_array($method,self::$methods))) {
            throw new Exception('Undefined method '.$method);
        }
    }

    public function getTableName()
    {
        return static::$table;
    }


    public function hasMany($model,$foreignKey,$primaryKey)
    {
        $relationalTable = $model::$table;
        QueryBuilder::$relations[] = [
            'type'=>'has_many',
            'table'=>$relationalTable,
            'primary_key'=>$primaryKey,
            'foreign_key'=>$foreignKey,
        ];
    }
    public function belongsTo($model,$foreignKey,$primaryKey)
    {
        $relationalTable = $model::$table;
        QueryBuilder::$relations[] = [
            'type'=>'belongs_to',
            'table'=>$relationalTable,
            'primary_key'=>$primaryKey,
            'foreign_key'=>$foreignKey,
        ];
    }
}