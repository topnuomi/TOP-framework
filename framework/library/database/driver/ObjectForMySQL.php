<?php

namespace top\library\database\driver;

use top\library\database\ifs\DatabaseIfs;
use top\library\exception\DatabaseException;
use top\traits\Instance;

/**
 * MySQL数据库驱动
 * @author topnuomi 2018年11月20日
 */
class ObjectForMySQL implements DatabaseIfs
{

    use Instance;

    private $link;

    private $mysqli = null;

    private $sql;

    /**
     * 连接数据库
     * @param array $config
     * @return $this|DatabaseIfs
     */
    public function connect($config)
    {
        $this->mysqli = new \mysqli($config['host'], $config['user'], $config['passwd'], $config['dbname'], $config['port']);
        $this->mysqli->query('set names ' . $config['charset']);
        return $this;
    }

    /**
     * 插入记录
     * @param string $table
     * @param array $data
     * @return mixed
     * @throws DatabaseException
     */
    public function insert($table, $data)
    {
        // TODO Auto-generated method stub
        if (count($data) == count($data, 1)) { // 一维数组
            $query = 'insert into ' . $table;
            $field = ' (' . implode(',', array_keys($data)) . ')';
            $value = array_values($data);
            $value = '(' . implode(',', $this->checkNull($value)) . ')';
            $this->sql = $query .= $field . ' values ' . $value . ';';
            $this->query($query);
        } else { // 二维数组
            foreach ($data as $key => $value) {
                $query = 'insert into ' . $table;
                $allField = ' (' . implode(',', array_keys($value)) . ')';
                $allValue = '(' . implode(',', $this->checkNull($value)) . ')';
                $this->sql = $query .= $allField . ' values ' . $allValue . ';';
                $this->query($query);
            }
        }
        return $this->mysqli->insert_id;
    }

    /**
     * 更新记录
     * @param string $table
     * @param array $join
     * @param array|string $on
     * @param array|string $where
     * @param string $order
     * @param string $limit
     * @param array $data
     * @return int|mixed
     * @throws DatabaseException
     */
    public function update($table, $join, $on, $where, $order, $limit, $data)
    {
        // TODO Auto-generated method stub
        $tableInfo = $this->parseTable($table);
        $join = $this->parseJoin($join, $on);
        array_push($where, $tableInfo['where']);
        $where = $this->parseWhere($where);
        $order = $this->parseOrder($order);
        $limit = $this->parseLimit($limit);
        $query = 'update ' . $tableInfo['table'] . "{$join} set ";
        $updateData = [];
        foreach ($data as $key => $value) {
            if (!is_numeric($value) && !$value) {
                $value = 'NULL';
            } else {
                $value = '\'' . mysqli_real_escape_string($this->link, $value) . '\'';
            }
            $updateData[] = $key . '=' . $value;
        }
        $this->sql = $query .= implode(',', $updateData) . "{$where}{$order}{$limit}";
        $this->query($query);
        return $this->mysqli->affected_rows;
    }

    /**
     * 查询一条记录
     * @param string $table
     * @param $distinct
     * @param array|string $field
     * @param array $join
     * @param array|string $on
     * @param array|string $where
     * @param string $order
     * @return array|mixed|null
     * @throws DatabaseException
     */
    public function find($table, $distinct, $field, $join, $on, $where, $order)
    {
        // TODO Auto-generated method stub
        $tableInfo = $this->parseTable($table);
        $join = $this->parseJoin($join, $on);
        $distinct = $this->parseDistinct($distinct);
        if ($distinct) {
            $field = $distinct;
        } else {
            $field = $this->parseField($field);
        }
        array_push($where, $tableInfo['where']);
        $where = $this->parseWhere($where);
        $order = $this->parseOrder($order);
        $this->sql = "select {$field} from {$tableInfo['table']}{$join}{$where}{$order} limit 1";
        $result = $this->query($this->sql);
        return $result->fetch_assoc();
    }

    /**
     * 查询所有记录
     * @param string $table
     * @param $distinct
     * @param array|string $field
     * @param array $join
     * @param array|string $on
     * @param array|string $where
     * @param string $order
     * @param string $limit
     * @return array|mixed|null
     * @throws DatabaseException
     */
    public function select($table, $distinct, $field, $join, $on, $where, $order, $limit)
    {
        // TODO Auto-generated method stub
        $tableInfo = $this->parseTable($table);
        $join = $this->parseJoin($join, $on);
        $distinct = $this->parseDistinct($distinct);
        if ($distinct) {
            $field = $distinct;
        } else {
            $field = $this->parseField($field);
        }
        array_push($where, $tableInfo['where']);
        $where = $this->parseWhere($where);
        $order = $this->parseOrder($order);
        $limit = $this->parseLimit($limit);
        $this->sql = "select {$field} from {$tableInfo['table']}{$join}{$where}{$order}{$limit}";
        $result = $this->query($this->sql);
        return $result->fetch_all(MYSQLI_ASSOC);
    }

    /**
     * 删除记录
     * @param array|string $effect
     * @param string $table
     * @param array $join
     * @param array|string $on
     * @param array|string $where
     * @param string $order
     * @param string $limit
     * @return int|mixed
     * @throws DatabaseException
     */
    public function delete($effect, $table, $join, $on, $where, $order, $limit)
    {
        // TODO Auto-generated method stub
        $tableInfo = $this->parseTable($table);
        $effect = $this->parseEffect($effect);
        $join = $this->parseJoin($join, $on);
        array_push($where, $tableInfo['where']);
        $where = $this->parseWhere($where);
        $order = $this->parseOrder($order);
        $limit = $this->parseLimit($limit);
        $this->sql = "delete{$effect} from {$tableInfo['table']}{$join}{$where}{$order}{$limit}";
        $this->query($this->sql);
        return $this->mysqli->affected_rows;
    }

    /**
     * 获取表结构
     * @param $table
     * @return bool|mixed
     * @throws DatabaseException
     */
    public function tableDesc($table)
    {
        $sql = 'desc ' . $table;
        if (!$result = $this->query($sql)) {
            return false;
        }
        $data = $result->fetch_all(MYSQLI_ASSOC);
        return $data;
    }

    /**
     * 公共方法
     * @param $table
     * @param $distinct
     * @param $field
     * @param $join
     * @param $on
     * @param $where
     * @param $type
     * @return bool
     * @throws DatabaseException
     */
    public function common($table, $distinct, $field, $join, $on, $where, $type)
    {
        $tableInfo = $this->parseTable($table);
        $distinct = $this->parseDistinct($distinct);
        if ($distinct) {
            $field = $distinct;
        } else {
            $field = $this->parseField($field);
        }
        $join = $this->parseJoin($join, $on);
        array_push($where, $tableInfo['where']);
        $where = $this->parseWhere($where);
        $this->sql = "select {$type}({$field}) from {$tableInfo['table']}{$join}{$where}";
        $result = $this->query($this->sql);
        $data = $result->fetch_array();
        if (isset($data[0])) {
            return $data[0];
        } else {
            return false;
        }
    }

    /**
     * 开启事务
     */
    public function begin()
    {
        $this->mysqli->begin_transaction();
    }

    /**
     * 提交
     */
    public function commit()
    {
        $this->mysqli->commit();
    }

    /**
     * 回滚
     */
    public function rollback()
    {
        $this->mysqli->rollback();
    }

    /**
     * 执行SQL
     * @param string $query
     * @return mixed
     * @throws DatabaseException
     */
    public function query($query)
    {
        $result = $this->mysqli->query($query);
        if (!$result) {
            throw new DatabaseException($this->mysqli->error);
        }
        return $result;
    }

    /**
     * 获取执行的最后一条SQL
     *
     * @return string
     */
    public function sql()
    {
        return trim($this->sql, ' ');
    }

    /**
     * 解析表信息
     * @param $table
     * @return array
     */
    private function parseTable($table)
    {
        $info = [];
        // 如果是多表查询，给当前表名别名this
        if ($table[1] === true) {
            $info['table'] = $table[0] . ' as this';
            $info['where'] = [];
            // 如果存在主键的条件，给键名加上别名
            if (!empty($table[2])) {
                $field = 'this.' . array_keys($table[2])[0];
                $value = array_values($table[2])[0];
                $info['where'] = [$field => $value];
            }
        } else {
            $info['table'] = $table[0];
            $info['where'] = $table[2];
        }
        return $info;
    }

    /**
     * 解析多表的删除
     * @param $effect
     * @return string
     */
    public function parseEffect($effect)
    {
        if ($effect) {
            if (is_array($effect)) {
                $effect = implode(',', $effect);
            }
            return ' ' . $effect;
        }
        return '';
    }

    /**
     * 解析数据去重
     * @param $distinct
     * @return string
     */
    private function parseDistinct($distinct)
    {
        if ($distinct) {
            if (is_array($distinct)) {
                $distinct = implode(',', $distinct);
            }
            return 'distinct ' . $distinct;
        }
        return '';
    }

    /**
     * 组合字段
     * @param string|array $field
     * @return string
     */
    private function parseField($field)
    {
        if (!$field) {
            $field = '*';
        } else if (is_array($field)) {
            $field = implode(',', $field);
        }
        return $field;
    }

    /**
     * 组合where条件
     * @param array $array
     * @param string $glue
     * @return string
     */
    private function parseWhere(array $array, $glue = 'and')
    {
        $where = [];
        foreach ($array as $value) {
            if (empty($value)) continue;
            if (is_array($value)) {
                foreach ($value as $key => $val) {
                    if (is_array($val)) {
                        switch (strtolower($val[0])) {
                            case 'in':
                                $arr_ = (is_array($val[1])) ? $val[1] : explode(',', $val[1]);
                                $str = '';
                                for ($i = 0; $i < count($arr_); $i++) {
                                    $str .= (($i != 0) ? ',' : '') . $this->checkNull(trim($arr_[$i]));
                                }
                                $where[] = $key . ' ' . $val[0] . ' (' . $str . ')';
                                break;
                            case 'like':
                                $where[] = $key . ' ' . $val[0] . ' \'%' . $val[1] . '%\'';
                                break;
                            default:
                                $where[] = $key . ' ' . $val[0] . ' ' . $this->checkNull($val[1]);
                        }
                    } else {
                        $val = $this->checkNull($val);
                        $where[] = $key . '=' . $val;
                    }
                }
            } else {
                $where[] = $value;
            }
        }
        if (empty($where)) {
            return '';
        } else {
            return ' where ' . implode(' ' . $glue . ' ', $where);
        }
    }

    /**
     * 组合order
     * @param string $order
     * @return string
     */
    private function parseOrder($order = '')
    {
        if ($order) {
            $order = ' order by ' . $order;
        }
        return $order;
    }

    /**
     * 组合limit
     * @param string $limit
     * @return string
     */
    private function parseLimit($limit = '')
    {
        if ($limit) {
            if (is_array($limit)) {
                $limit = ' limit ' . implode(',', $limit);
            } else {
                $limit = ' limit ' . $limit;
            }
        }
        return $limit;
    }

    /**
     * 链接多表（join on）
     * @param array $data
     * @param string|array $on
     * @return string
     */
    private function parseJoin($data, $on)
    {
        $join = [];
        for ($i = 0; $i < count($data); $i++) {
            $string = null;
            if (isset($on[$i])) {
                if (is_array($on[$i])) {
                    $pieces = [];
                    foreach ($on[$i] as $key => $value) {
                        $pieces[] = $key . ' = ' . $value;
                    }
                    $string = ' on ' . implode(' and ', $pieces);
                } else {
                    $string = ' on ' . $on[$i];
                }
            }
            $join[] = $data[$i][0] . ' join ' . $data[$i][1] . ($data[$i][2] ? ' as ' . $data[$i][2] : '') . $string;
        }
        if (!empty($join)) {
            return ' ' . implode(' ', $join);
        }
        return '';
    }

    /**
     * 检查并处理空值
     * @param $value
     * @return array|string
     */
    private function checkNull($value)
    {
        if (is_array($value)) {
            foreach ($value as $k => $v) {
                if (!is_numeric($v) && !$v) {
                    $value[$k] = 'NULL';
                } else {
                    $value[$k] = '\'' . $this->mysqli->real_escape_string($v) . '\'';
                }
            }
        } else {
            if (!is_numeric($value) && !$value) {
                $value = 'NULL';
            } else {
                $value = '\'' . $this->mysqli->real_escape_string($value) . '\'';
            }
        }
        return $value;
    }

    /**
     * 关闭数据库连接
     */
    public function close()
    {
        if ($this->mysqli->close()) {
            return true;
        }
        return false;
    }

    public function __destruct()
    {
        $this->close();
    }
}
