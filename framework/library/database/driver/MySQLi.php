<?php

namespace top\library\database\driver;

use top\library\database\ifs\DatabaseIfs;
use top\library\exception\DatabaseException;
use top\traits\Instance;

/**
 * MySQLi数据库驱动
 * @author topnuomi 2018年11月20日
 */
class MySQLi implements DatabaseIfs
{

    use Instance;

    private $link;

    private $sql;

    /**
     * 连接数据库
     * @param array $config
     * @return $this
     * @throws \Exception
     */
    public function connect($config)
    {
        $link = $this->link = @mysqli_connect($config['host'], $config['user'], $config['passwd'], $config['dbname'], $config['port']);
        if ($link === false) {
            throw new DatabaseException(mysqli_connect_error());
        }
        mysqli_query($link, 'set names ' . $config['charset']);

        return $this;
    }

    /**
     * 插入
     * @param string $table
     * @param array $data
     * @return int|string
     * @throws \Exception
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
        return mysqli_insert_id($this->link);
    }

    /**
     * 更新
     * @param string $table
     * @param array $join
     * @param array|string $on
     * @param array|string $where
     * @param string $order
     * @param string $limit
     * @param array $data
     * @return int
     * @throws \Exception
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
        $query = "update {$tableInfo['table']}{$join} set ";
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
        return mysqli_affected_rows($this->link);
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
     * @return array|null
     * @throws \Exception
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
        return mysqli_fetch_assoc($result);
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
     * @return array|null
     * @throws \Exception
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
        return mysqli_fetch_all($result, MYSQLI_ASSOC);
    }

    /**
     * 删除
     * @param array|string $effect
     * @param string $table
     * @param array $join
     * @param array|string $on
     * @param array|string $where
     * @param string $order
     * @param string $limit
     * @return int
     * @throws \Exception
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
        return mysqli_affected_rows($this->link);
    }

    /**
     * 获取表结构
     * @param $table
     * @return array|bool|null
     * @throws \Exception
     */
    public function tableDesc($table)
    {
        $sql = 'desc ' . $table;
        if (!$result = $this->query($sql)) {
            return false;
        }
        $data = mysqli_fetch_all($result, MYSQLI_ASSOC);
        return $data;
    }

    /**
     * 公共方法
     * @param $table
     * @param $field
     * @param $join
     * @param $on
     * @param $where
     * @param $type
     * @return bool
     * @throws \Exception
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
        $data = mysqli_fetch_array($result);
        if (isset($data[0])) {
            return $data[0];
        } else {
            return false;
        }
    }

    /**
     * 事务
     */
    public function begin()
    {
        mysqli_begin_transaction($this->link);
    }

    /**
     * 提交
     */
    public function commit()
    {
        mysqli_commit($this->link);
    }

    /**
     * 回滚
     */
    public function rollback()
    {
        mysqli_rollback($this->link);
    }

    /**
     * 执行SQL
     * @param string $query
     * @return bool|\mysqli_result
     * @throws \Exception
     */
    public function query($query)
    {
        $result = mysqli_query($this->link, $query);
        if (!$result) {
            throw new DatabaseException(mysqli_error($this->link));
        }
        $this->writeLogs($result, $query);
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
                                $arr_ = explode(',', $val[1]);
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
                    $value[$k] = '\'' . mysqli_real_escape_string($this->link, $v) . '\'';
                }
            }
        } else {
            if (!is_numeric($value) && !$value) {
                $value = 'NULL';
            } else {
                $value = '\'' . mysqli_real_escape_string($this->link, $value) . '\'';
            }
        }
        return $value;
    }

    /**
     * SQL存文件
     * @param $result
     * @param $query
     */
    private function writeLogs($result, $query)
    {
        if (DEBUG) {
            $error = '';
            if (!$result) {
                $error = mysqli_error($this->link);
            }
            $nowTime = date('Y-m-d H:i:s', time());
            $content = "[{$nowTime}] SQL: {$query} {$error}" . PHP_EOL;
            file_put_contents('./runtime/database_logs.txt', $content, FILE_APPEND);
        }
    }

    /**
     * 关闭数据库连接
     */
    public function close()
    {
        if ($this->link) {
            if (mysqli_close($this->link)) {
                return true;
            }
            return false;
        }
        return true;
    }

    public function __destruct()
    {
        $this->close();
    }
}
