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
        $join = $this->parseJoin($join, $on);
        $where = $this->parseWhere($where);
        $order = $this->parseOrder($order);
        $limit = $this->parseLimit($limit);
        $query = 'update ' . $table . "{$join} set ";
        $updateData = [];
        foreach ($data as $key => $value) {
            if (!is_numeric($value) && !$value) {
                $value = 'NULL';
            } else {
                $value = '\'' . $this->mysqli->real_escape_string($value) . '\'';
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
        $join = $this->parseJoin($join, $on);
        $distinct = $this->parseDistinct($distinct);
        if ($distinct) {
            $field = $distinct;
        } else {
            $field = $this->parseField($field);
        }
        $where = $this->parseWhere($where);
        $order = $this->parseOrder($order);
        $this->sql = "select {$field} from $table{$join}{$where}{$order} limit 1";
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
        $join = $this->parseJoin($join, $on);
        $distinct = $this->parseDistinct($distinct);
        if ($distinct) {
            $field = $distinct;
        } else {
            $field = $this->parseField($field);
        }
        $where = $this->parseWhere($where);
        $order = $this->parseOrder($order);
        $limit = $this->parseLimit($limit);
        $this->sql = "select {$field} from {$table}{$join}{$where}{$order}{$limit}";
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
        $effect = $this->parseEffect($effect);
        $join = $this->parseJoin($join, $on);
        $where = $this->parseWhere($where);
        $order = $this->parseOrder($order);
        $limit = $this->parseLimit($limit);
        $this->sql = "delete{$effect} from $table{$join}{$where}{$order}{$limit}";
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
     * 计数
     * @param $table
     * @param $field
     * @param $join
     * @param $on
     * @param $where
     * @return mixed
     * @throws DatabaseException
     */
    public function count($table, $field, $join, $on, $where)
    {
        $field = $this->parseField($field);
        $join = $this->parseJoin($join, $on);
        $where = $this->parseWhere($where);
        $this->sql = "select count({$field}) from $table{$join}{$where}";
        $result = $this->query($this->sql);
        $count = $result->fetch_array();
        return $count[0];
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
        $distinct = $this->parseDistinct($distinct);
        if ($distinct) {
            $field = $distinct;
        } else {
            $field = $this->parseField($field);
        }
        $join = $this->parseJoin($join, $on);
        $where = $this->parseWhere($where);
        $this->sql = "select {$type}({$field}) from {$table}{$join}{$where}";
        $result = $this->query($this->sql);
        $data = $result->fetch_array();
        if (isset($data[0])) {
            return $data[0];
        } else {
            return false;
        }
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
     *
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
            if (is_array($on[$i])) {
                $pieces = [];
                foreach ($on[$i] as $key => $value) {
                    $pieces[] = $key . ' = ' . $value;
                }
                $onString = implode(' and ', $pieces);
            } else {
                $onString = $on[$i];
            }
            $join[] = $data[$i][0] . ' join ' . $data[$i][1] . ($data[$i][2] ? ' as ' . $data[$i][2] : '') . ' on ' . $onString;
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
