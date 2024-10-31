<?php

namespace Ovoform\BackOffice\Database;

use Ovoform\BackOffice\Facade\DB;

class QueryBuilder
{
    protected $model;

    protected $table;

    private $select = '*';

    private $where;

    private $order;

    private $limit;

    private $join;

    private $cleanUp;

    public $finalSql;

    public $extra;

    public $attributes = [];
    public static $relations = [];

    public function __construct($model, $table)
    {
        $this->model = $model;
        $this->table = $table;
    }

    public function get($select = null)
    {
        if ($select) {
            $this->select = implode(',', $select);
        }
        $table = $this->table;
        $extra = $this->extra();
        if (!$extra) {
            $extra = $this->extra;
        }
        $sql = sprintf('SELECT %s FROM {{table_prefix}}%s%s', $this->select, $table, $extra);
        $this->finalSql = $sql;
        $results = DB::execute($this->finalSql);
        if (!empty(self::$relations)) {
            $results = $this->mergeWithRelation($results);
        }
        return $results;
    }

    private function mergeWithRelation($mainResult)
    {
        $relations = self::$relations;
        $oldTable = $this->table;


        if ($mainResult || !empty($mainResult)) {
            foreach ($relations as $relation) {
                //update some config
                self::$relations = [];
                $this->table = $relation['table'];
                $this->extra = array_key_exists('callable', $relation) ? $relation['callable']($this)->extra(true) : null;

                $relatedResults = $this->buildRelationalQuery($mainResult, $relation);

                $relationName = $this->buildRelationNameWithPrefix($relation);

                if (is_array($mainResult)) {
                    $mainResult = $this->mergeResultIfArray($relation, $relatedResults, $relationName, $mainResult);
                } else {
                    $mainResult = $this->mergeResultIfNotArray($relation, $relatedResults, $relationName, $mainResult);
                }
            }
        }


        self::$relations = $relations;
        $this->table = $oldTable;
        return $mainResult;
    }

    private function buildRelationalQuery($mainResult, $relation)
    {
        if (is_array($mainResult)) {
            $primaryKeys = array_column($mainResult, $relation['primary_key']);
            $relatedResults = $this->whereIn($relation['foreign_key'], array_unique($primaryKeys));
        } else {
            $primaryKey = $relation['primary_key'];
            $relatedResults = $this->where($relation['foreign_key'], $mainResult->$primaryKey);
        }

        if ($relation['type'] == 'count') {
            $relatedResults = $relatedResults->count();
        } elseif ($relation['type'] == 'sum') {
            $relatedResults = $relatedResults->sum($relation['select']);
        } else {
            $relatedResults = $relatedResults->get(explode(',', $relation['select']));
        }

        return $relatedResults;
    }

    private function buildRelationNameWithPrefix($relation)
    {
        $relationName = $relation['name'];
        if ($relation['type'] != 'get') {
            $relationName .= '_' . $relation['type'];
        }
        return $relationName;
    }

    private function mergeResultIfArray($relation, $relatedResults, $relationName, $mainResult)
    {
        if ($relation['type'] == 'belongs_to') {
            foreach ($mainResult as $main) {
                if ($relation['type'] != 'get') {
                    foreach ($relatedResults as $key => $relatedResult) {
                        if ($relatedResult->{$relation['foreign_key']} == $main->{$relation['primary_key']}) {
                            $main->$relationName = array_pop(array_reverse($relatedResults));
                        } else {
                            $main->$relationName = null;
                        }
                    }
                } else {
                    $main->$relationName = $relatedResults;
                }
            }
        } else {
            foreach ($mainResult as $main) {
                $main->$relationName = $relatedResults;
            }
        }
        return $mainResult;
    }

    private function mergeResultIfNotArray($relation, $relatedResults, $relationName, $mainResult)
    {
        if ($relation['type'] == 'belongs_to') {
            if ($relation['type'] != 'get') {
                foreach ($relatedResults as $key => $relatedResult) {
                    if ($relatedResult->{$relation['foreign_key']} == $mainResult->{$relation['primary_key']}) {
                        $mainResult->$relationName = array_pop(array_reverse($relatedResults));
                    } else {
                        $mainResult->$relationName = null;
                    }
                }
            } else {
                $mainResult->$relationName = $relatedResults;
            }
        } else {
            $mainResult->$relationName = $relatedResults;
        }

        return $mainResult;
    }

    public function where($field, $condition, $equal = null, $checkColumn = false)
    {
        $symbol = $condition;
        if (!$this->arithmeticSym($condition)) {
            $symbol = '=';
            $equal = $condition;
        }
        if (is_array($field)) {
            $this->__where($field, symbol: $symbol, checkColumn: $checkColumn);
        } else {
            $this->__where([$field => $equal], symbol: $symbol, checkColumn: $checkColumn);
        }

        return $this;
    }

    public function orWhere($field, $condition, $value)
    {
        $symbol = $condition;
        if (!$this->arithmeticSym($condition)) {
            $symbol = '=';
        }
        $where = $this->where;
        $where .= sprintf(" OR `%s` $symbol '%s'", $field, $this->escape_string($value));
        $this->where = $where;
        return $this;
    }

    public function whereNot($field, $condition, $equal = null)
    {
        $symbol = $condition;
        if (!$this->arithmeticSym($condition)) {
            $symbol = '=';
            $equal = $condition;
        }
        $where = $this->where;

        if (!is_array($field)) {
            $field = [$field => $equal];
        }

        $type = 'AND';

        foreach ($field as $row => $value) {
            if (empty($where)) {
                $where = sprintf("WHERE NOT `%s` $symbol '%s'", $row, $this->escape_string($value));
            } else {
                $where .= sprintf(" %s NOT `%s` $symbol '%s'", $type, $row, $this->escape_string($value));
            }
        }
        $this->where = $where;
        return $this;
    }

    public function orWhereNot($field, $condition, $equal = null)
    {
        $symbol = $condition;
        if (!$this->arithmeticSym($condition)) {
            $symbol = '=';
            $equal = $condition;
        }
        $where = $this->where;
        if (!is_array($field)) {
            $field = [$field => $equal];
        }

        foreach ($field as $row => $value) {
            $where .= sprintf(" OR NOT `%s` $symbol '%s'", $row, $this->escape_string($value));
        }
        $this->where = $where;
        return $this;
    }

    public function isNull($field)
    {
        $where = $this->where;
        if (!is_array($field)) {
            $field = [$field];
        }
        foreach ($field as $column) {
            if (empty($where)) {
                $where = sprintf("WHERE `%s` IS NULL", $column);
            } else {
                $where .= sprintf(" OR `%s` IS NULL", $column);
            }
        }
        $this->where = $where;
        return $this;
    }

    public function isNotNull($field)
    {
        $where = $this->where;
        if (!is_array($field)) {
            $field = [$field];
        }
        foreach ($field as $column) {
            if (empty($where)) {
                $where = sprintf("WHERE `%s` IS NOT NULL", $column);
            } else {
                $where .= sprintf(" OR `%s` IS NOT NULL", $column);
            }
        }
        $this->where = $where;
        return $this;
    }

    public function tableColumns($table)
    {
        $sql = "DESCRIBE $table";
        $this->finalSql = $sql;
        return DB::execute($this->finalSql);
    }

    public function whereIn($field, $info)
    {
        $where = $this->where;
        $value = '';

        foreach ($info as $inf) {
            $value .= sprintf("'%s'", $this->escape_string($inf)) . ',';
        }

        $value = rtrim($value, ',');

        if (empty($where)) {
            $where = sprintf("WHERE `%s` IN(%s)", $field, $value);
        } else {
            $where .= sprintf(" AND `%s` IN(%s)", $field, $value);
        }

        $this->where = $where;
        return $this;
    }

    public function whereBetween($field, $info)
    {
        $where = $this->where;
        if (empty($where)) {
            $where = sprintf("WHERE `%s` BETWEEN '%s' AND '%s'", $field, $this->escape_string($info[0]), $this->escape_string($info[1]));
        } else {
            $where .= sprintf(" AND `%s` BETWEEN '%s' AND '%s'", $field, $this->escape_string($info[0]), $this->escape_string($info[1]));
        }

        $this->where = $where;
        return $this;
    }

    public function limit($limit, $offset = 0)
    {
        $this->count = $limit;
        $this->limit = 'LIMIT ' . $limit;
        if ($offset) {
            $this->limit .= ' OFFSET ' . $offset;
        }
        return $this;
    }

    public function skip($offset)
    {
        $this->limit .= ' OFFSET ' . $offset;

        return $this;
    }

    public function orderBy($by, $order_type = 'ASC')
    {
        $order = $this->order;

        if (is_array($by)) {
            foreach ($by as $field => $type) {
                if (is_int($field) && !preg_match('/(DESC|desc|ASC|asc)/', $type)) {
                    $field = $type;
                    $type = $order_type;
                }
                if (empty($order)) {
                    $order = sprintf('ORDER BY %s %s', '{{table_prefix}}' . $this->table . '.' . $field, $type);
                } else {
                    $order .= sprintf(', %s %s', '{{table_prefix}}' . $this->table . '.' . $field, $type);
                }
            }
        } else {
            if (empty($order)) {
                $order = sprintf('ORDER BY %s %s', '{{table_prefix}}' . $this->table . '.' . $by, $order_type);
            } else {
                $order .= sprintf(', %s %s', '{{table_prefix}}' . $this->table . '.' . $by, $order_type);
            }
        }
        $this->order = $order;

        return $this;
    }

    public function distinct($column)
    {
        $distinct = sprintf('DISTINCT `%s`', $column);
        $this->select = $distinct;
        return $this;
    }

    public function selectRaw($sql)
    {
        $this->finalSql = $sql;
        return DB::execute($this->finalSql);
    }

    public function first()
    {
        $table = $this->table;
        $extra = $this->extra;
        if (!$extra) {
            $extra = $this->extra();
        }
        $sql = $sql = sprintf('SELECT %s FROM {{table_prefix}}%s%s', $this->select, $table, $extra);
        $this->finalSql = $sql;
        $result = DB::getRow($this->finalSql);
        if (!empty(self::$relations)) {
            $results = $this->mergeWithRelation($result);
        }
        if (!$result) {
            return null;
        }
        $modelInstance = new $this->model;
        $modelInstance->result_data = $result;
        $modelInstance->for_save = true;
        return $modelInstance;
    }

    public function find($id)
    {
        $this->where('id', $id);
        return $this->first();
    }

    public function findOrFail($id)
    {
        $result = $this->find($id);
        if (!$result) {
            ovoform_abort(404);
        }
        return $result;
    }

    public function firstOrFail()
    {
        $result = $this->first();
        if (!$result) {
            ovoform_abort(404);
        }
        return $result;
    }

    public function sum($colName)
    {
        $table = $this->table;
        $extra = $this->extra;
        if (!$extra) {
            $extra = $this->extra($this->cleanUp);
        }
        $sql = sprintf('SELECT SUM(%s) FROM {{table_prefix}}%s%s', $colName, $table, $extra);
        $this->finalSql = $sql;
        $sum = DB::getVar($this->finalSql);
        return (int) $sum;
    }

    public function avg($colName)
    {
        $table = $this->table;
        $extra = $this->extra;
        if (!$extra) {
            $extra = $this->extra($this->cleanUp);
        }
        $sql = sprintf('SELECT AVG(%s) FROM {{table_prefix}}%s%s', $colName, $table, $extra);
        $this->finalSql = $sql;
        return DB::getVar($this->finalSql);
    }

    public function min($colName)
    {
        $table = $this->table;
        $extra = $this->extra;
        if (!$extra) {
            $extra = $this->extra($this->cleanUp);
        }
        $sql = sprintf('SELECT MIN(%s) FROM {{table_prefix}}%s%s', $colName, $table, $extra);
        $this->finalSql = $sql;
        return DB::getVar($this->finalSql);
    }

    public function max($colName)
    {
        $table = $this->table;
        $extra = $this->extra;
        if (!$extra) {
            $extra = $this->extra($this->cleanUp);
        }
        $sql = sprintf('SELECT MAX(%s) FROM {{table_prefix}}%s%s', $colName, $table, $extra);
        $this->finalSql = $sql;
        return DB::getVar($this->finalSql);
    }

    public function count()
    {
        $table = $this->table;
        $extra = $this->extra;
        if (!$extra) {
            $extra = $this->extra($this->cleanUp);
        }
        $sql = sprintf('SELECT COUNT(*) FROM {{table_prefix}}%s%s', $table, $extra);
        $this->finalSql = $sql;
        $count = DB::getVar($this->finalSql);
        return (int) $count;
    }

    public function insert($data)
    {
        $table = $this->table;
        $fields = '';
        $values = '';

        foreach ($data as $col => $value) {
            $fields .= sprintf('`%s`,', $col);
            $values .= sprintf("'%s',", $this->escape_string($value));
        }

        $fields = substr($fields, 0, -1);
        $values = substr($values, 0, -1);

        $sql = sprintf('INSERT INTO {{table_prefix}}%s (%s) VALUES (%s)', $table, $fields, $values);
        $this->finalSql = $sql;
        return DB::query($this->finalSql);
    }

    public function update($info)
    {
        if (empty($this->where)) {
            return die('Where method is required');
        } else {
            $table = $this->table;

            $update = '';

            foreach ($info as $col => $value) {
                $update .= sprintf("`%s`='%s', ", $col, $this->escape_string($value));
            }
            $update = substr($update, 0, -2);
            $extra = $this->extra;
            if (!$extra) {
                $extra = $this->extra();
            }
            $sql = sprintf('UPDATE {{table_prefix}}%s SET %s%s', $table, $update, $extra);
            $this->finalSql = $sql;
            DB::query($this->finalSql);
        }
    }

    public function delete()
    {
        $table = $this->table;
        if (array_key_exists('for_save', @Model::$storedData[$this->model] ?? [])) {
            $modelId = Model::$storedData[$this->model]['result_data']->id;
            $this->where('id', $modelId);
        }
        if (empty($this->where)) {
            die("Where is not set. Can't delete whole table.");
        } else {
            $extra = $this->extra;
            if (!$extra) {
                $extra = $this->extra();
            }
            $sql = sprintf('DELETE FROM {{table_prefix}}%s%s', $table, $extra);
            $this->finalSql = $sql;
            DB::query($this->finalSql);
        }
    }

    public function save()
    {
        if (!array_key_exists('for_save', @Model::$storedData[$this->model] ?? [])) {
            return $this->insert($this->attributes);
        } else {
            $modelId = Model::$storedData[$this->model]['result_data']->id;
            $this->where('id', $modelId);
            $this->update($this->attributes);
        }
    }

    private function __where($info, $type = 'AND', $symbol = '=', $checkColumn = false)
    {
        $where = $this->where;
        foreach ($info as $row => $value) {
            if (empty($where)) {
                if (!$checkColumn) {
                    $where = sprintf("WHERE `%s` $symbol '%s'", $row, $this->escape_string($value));
                } else {
                    $where = sprintf("WHERE %s $symbol %s", $row, $this->escape_string($value));
                }
            } else {
                if (!$checkColumn) {
                    $where .= sprintf(" %s `%s` $symbol '%s'", $type, $row, $this->escape_string($value));
                } else {
                    $where .= sprintf(" %s %s $symbol %s", $type, $row, $this->escape_string($value));
                }
            }
        }
        $this->where = $where;
    }

    private function extra($cleanUp = true)
    {
        $extra = '';
        if (!empty($this->where)) {
            $extra .= ' ' . $this->where;
        }
        if (!empty($this->join)) {
            $extra .= ' ' . $this->join;
        }
        if (!empty($this->order)) {
            $extra .= ' ' . $this->order;
        }
        if (!empty($this->limit)) {
            $extra .= ' ' . $this->limit;
        }

        if ($cleanUp) {
            $this->cleanUp();
        }
        $this->extra = $extra;

        return $extra;
    }

    private function cleanUp()
    {
        // cleanup
        $this->where = null;
        $this->order = null;
        $this->limit = null;
    }

    public function escape_string($string = '')
    {
        $wpdb = DB::wpdb();
        return $wpdb->_real_escape($string);
    }

    private function arithmeticSym($symbol)
    {
        $symbols = [
            '=',
            '!=',
            '<',
            '>',
            '<=',
            '>=',
            'LIKE'
        ];
        return in_array($symbol, $symbols);
    }

    public function paginate($number)
    {
        $table         = $this->table;
        $this->cleanUp = false;
        $totalData     = $this->count($table);
        $pnum          = sanitize_text_field(@$_GET['pnum']);
        $pnum          = $pnum ? intval($pnum) : 1;
        $per_page      = $number;
        $query         = $this->limit($per_page, ($pnum - 1) * $per_page);
        $results       = $query->get();
        $data          = [
            'data'  => $results,
            'links' => $this->getPageLinks($totalData, $per_page)
        ];
        $data = ovoform_to_object($data);
        return $data;
    }

    private function getPageLinks($total = 0, $limit = 10)
    {
        $pnum = sanitize_text_field(@$_GET['pnum']);
        $page = (isset($pnum) && is_numeric($pnum)) ? $pnum : 1;

        $totalPages = ceil($total / $limit);

        // Prev + Next
        $prev = $page - 1;
        $next = $page + 1;

        $html = '';

        if ($total >= $limit) {
            $html = "<nav>";
            $html .= '<ul class="pagination justify-content-center">';
            $dNone = $page <= 1 ? 'd-none' : '';
            $html .= '<li class="page-item ' . $dNone . '"> <a class="page-link" href="' . ovoform_query_to_url(['pnum' => $prev]) . '"><i class="las la-angle-left"></i></a></li>';
            $linksPerPage = 6;
            $start        = max(1, $page - floor($linksPerPage / 2));
            $end          = min($totalPages, $start + $linksPerPage - 1);
            $start        = max(1, $end - $linksPerPage + 1);
            for ($i = $start; $i <= $end; $i++) {
                $active = ($page == $i) ? ' active' : '';
                $html .= '<li class="page-item' . $active . '"><a class="page-link" href="' . ovoform_query_to_url(['pnum' => $i]) . '">' . $i . '</a></li>';
            }

            if ($page >= $totalPages) {
                $html .= '<li class="page-item d-none">';
            } else {
                $html .= '<li class="page-item">';
            }

            $html .= '<a class="page-link" href="' . ovoform_query_to_url(['pnum' => $next]) . '"><i class="las la-angle-right"></i></a>
                    </li>
                </ul>
            </nav>';
        }
        return $html;
    }

    public function truncate()
    {
        $table = $this->table;
        $sql = sprintf('TRUNCATE TABLE {{table_prefix}}%s', $table);
        $this->finalSql = $sql;
        DB::query($this->finalSql);
    }

    public function with($relations)
    {
        $this->formatRelation($relations, 'get');
        return $this;
    }

    public function withCount($relations)
    {
        $this->formatRelation($relations, 'count');
        return $this;
    }

    public function withSum($relations)
    {
        $this->formatRelation($relations, 'sum');
        return $this;
    }

    public function whereHas($relations)
    {
        $this->formatRelation($relations, 'has_data');
        $this->buildWhereHasQuery();
        return $this;
    }

    private function buildWhereHasQuery()
    {
        $where = $this->where;
        $relations = self::$relations;
        $oldTable = $this->table;
        foreach ($relations as $key => $relation) {
            if ($relation['type'] == 'has_data') {
                $primaryKey = $relation['primary_key'];
                $foreignKey = $relation['foreign_key'];
                $relatedTable = $relation['table'];
                $this->where('{{table_prefix}}' . $this->table . '.' . $primaryKey, '{{table_prefix}}' . $relatedTable . '.' . $foreignKey, checkColumn: true);
                if (empty($where)) {
                    $where = "WHERE EXISTS (SELECT * from {{table_prefix}}$relatedTable $this->where)";
                } else {
                    $where .= sprintf(" EXISTS (SELECT * from {{table_prefix}}$relatedTable $this->where)");
                }
            }
        }
        $this->table = $oldTable;
        $this->where = $where;
    }

    private function formatRelation($relations, $type)
    {
        $model = $this->model;
        $i = 0;
        foreach ($relations as $key => $relation) {

            //find callable
            $callable = null;
            if (is_callable($relation)) {
                $callable = $relation;
                $relation = $key;
            }

            $relationName = $this->findRelations($relation)['name'];
            $select = $this->findRelations($relation)['select_columns'];
            $methods = get_class_methods($model);

            if (!in_array($relationName, $methods)) {
                throw new \Exception("$relationName relation is not defined", 1);
            }

            //execute relation
            $modelInstance = new $model;
            $modelInstance->$relationName();

            //store relational info
            self::$relations[$i]['name'] = $relationName;
            self::$relations[$i]['type'] = $type;
            if ($callable) {
                self::$relations[$i]['callable'] = $callable;
            }
            self::$relations[$i]['select'] = $select;
            $i++;
        }
    }

    private function findRelations($relation)
    {
        $relation = explode(':', $relation);
        return [
            'name' => $relation[0],
            'select_columns' => array_key_exists(1, $relation) ? $relation[1] : '*'
        ];
    }
}
