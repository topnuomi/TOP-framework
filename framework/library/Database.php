<?php

namespace top\library;

use top\library\database\ifs\DatabaseIfs;

/**
 * 数据库操作类
 * @author topnuomi 2018年11月21日
 */
class Database
{

    // 数据库驱动
    private static $driver;

    // 当前类实例
    private static $instance = [];

    // 当前表结构
    private static $tableDesc = [];

    // 数据库配置
    private $config = [];

    // 当前操作的表
    private $table = '';

    // 当前表的主键
    private $pk = '';

    // 多个表（仅delete操作）
    private $effect = '';

    private $distinct = '';

    // 操作的字段
    private $field = '';

    // 条件
    private $where = [];

    // 排序
    private $order = '';

    // 范围
    private $limit = '';

    // 多表
    private $join = [];

    // 关联
    private $on = [];

    private $data = null;

    /**
     * Database constructor.
     * @param $table
     * @param $pk
     */
    private function __construct($table, $pk)
    {
        $driver = Register::get('DBDriver');
        $this->config = $config = Register::get('Config')->get('db');
        $this->table = $config['prefix'] . $table;
        $this->pk = $pk;
        $this->setDriver($driver, $this->config);
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

    /**
     * 指定数据库驱动
     *
     * @param DatabaseIfs $driver
     * @param array $config
     */
    private function setDriver(DatabaseIfs $driver, $config)
    {
        self::$driver = $driver->connect($config);
    }

    /**
     * 指定表
     * @param $table
     * @param string $pk
     * @return mixed
     */
    public static function table($table, $pk = '')
    {
        if (!isset(self::$instance[$table])) {
            self::$instance[$table] = new self($table, $pk);
        }
        return self::$instance[$table];
    }

    /**
     * 指定多张表
     * @param $effect
     * @return \top\library\Database
     */
    public function effect($effect)
    {
        $this->effect = $effect;
        return $this;
    }

    /**
     * @param $field
     * @return \top\library\Database
     */
    public function distinct($field)
    {
        $this->distinct = $field;
        return $this;
    }

    /**
     * 设置操作字段
     * @param $field
     * @return \top\library\Database
     */
    public function field($field)
    {
        $this->field = $field;
        return $this;
    }

    /**
     * 设置条件
     * @return \top\library\Database
     */
    public function where()
    {
        $where = func_get_args();
        if (!empty($where)) {
            switch (count($where)) {
                case 3:
                    $this->where[] = [
                        $where[0] => [
                            $where[1],
                            $where[2]
                        ]
                    ];
                    break;
                case 2:
                    $this->where[] = [
                        $where[0] => $where[1]
                    ];
                    break;
                default:
                    $this->where[] = $where[0];
                    break;
            }
        }
        return $this;
    }

    /**
     * 设置排序
     * @return \top\library\Database
     */
    public function order()
    {
        $order = func_get_args();
        if (!empty($order)) {
            if (count($order) > 1) {
                $this->order = $order[0] . ' ' . $order[1];
            } else {
                $this->order = $order[0];
            }
        }
        return $this;
    }

    /**
     * 设置记录范围
     * @return \top\library\Database
     */
    public function limit()
    {
        $limit = func_get_args();
        if (!empty($limit)) {
            if (count($limit) > 1) {
                $this->limit = $limit[0] . ', ' . $limit[1];
            } else {
                $this->limit = $limit[0];
            }
        }
        return $this;
    }

    /**
     * 多表
     *
     * @param string $type
     * @param string $table
     * @param string $name
     * @return \top\library\Database
     */
    public function join($type, $table, $name)
    {
        $this->join[] = [
            $type,
            $this->config['prefix'] . $table,
            $name
        ];
        return $this;
    }

    /**
     * 多表关联
     * @param string $on
     * @return \top\library\Database
     */
    public function on($on)
    {
        $this->on[] = $on;
        return $this;
    }

    /**
     * 插入记录
     *
     * @param array $data
     * @return int|boolean
     */
    public function insert($data)
    {
        $result = self::$driver->insert($this->table, $data);
        return $result;
    }

    /**
     * 查询一条记录
     * @param bool $param
     * @return object
     */
    public function find($param = false)
    {
        if (is_callable($param))
            $param($this);
        $field = $this->getPk();
        if (!empty($this->join)) {
            $this->table .= ' as this';
            $field = 'this.' . $field;
        }
        if (!is_bool($param) && !is_callable($param))
            $this->where([$field => $param]);
        $result = self::$driver->find($this->table, $this->distinct, $this->field, $this->join, $this->on, $this->where, $this->order);
        $this->_reset();
        return (object)$result;
    }

    /**
     * 查询所有记录
     *
     * @param callable|string|bool $param
     * @return array|boolean
     */
    public function select($param = false)
    {
        if (is_callable($param))
            $param($this);
        $field = $this->getPk();
        if (!empty($this->join)) {
            $this->table .= ' as this';
            $field = 'this.' . $field;
        }
        if (!is_bool($param) && !is_callable($param))
            $this->where([$field => $param]);
        $result = self::$driver->select($this->table, $this->distinct, $this->field, $this->join, $this->on, $this->where, $this->order, $this->limit);
        $this->_reset();
        foreach ($result as $k => $v)
            $result[$k] = (object)$v;
        return $result;
    }

    /**
     * 更新记录
     *
     * @param array $data
     * @param callable|string|bool $param
     * @return int|boolean
     */
    public function update($data, $param = false)
    {
        if (is_callable($param))
            $param($this);
        $field = $this->getPk();
        if (!empty($this->join)) {
            $this->table .= ' as this';
            $field = 'this.' . $field;
        }
        if (!is_bool($param) && !is_callable($param))
            $this->where([$field => $param]);
        $result = self::$driver->update($this->table, $this->join, $this->on, $this->where, $this->order, $this->limit, $data);
        $this->_reset();
        return $result;
    }

    /**
     * 删除记录
     *
     * @param callable|string|bool $param
     * @return int|boolean
     */
    public function delete($param = false)
    {
        if (is_callable($param)) {
            $param($this);
        }
        $field = $this->getPk();
        if (!empty($this->join)) {
            $this->table .= ' as this';
            $field = 'this.' . $field;
        }
        if (!is_bool($param) && !is_callable($param)) {
            $this->where([$field => $param]);
        }
        $result = self::$driver->delete($this->effect, $this->table, $this->join, $this->on, $this->where, $this->order, $this->limit);
        $this->_reset();

        return $result;
    }

    /**
     * 公共方法 （sum、avg等等使用函数包裹字段的方法）
     *
     * @param $param
     * @param $type
     * @return mixed
     */
    public function common($param, $type)
    {
        if (is_callable($param)) {
            $param($this);
        }
        if (!empty($this->join)) {
            $this->table .= ' as this';
        }
        if (empty($this->field) && $param && !is_callable($param)) {
            $this->field = $param;
        }
        $result = self::$driver->common($this->table, $this->distinct, $this->field, $this->join, $this->on, $this->where, $type);
        $this->_reset();

        return $result;
    }

    /**
     * 获取表结构
     *
     * @param string $table
     * @return mixed
     */
    public function tableDesc($table = '')
    {
        $table = ($table) ? $table : $this->table;
        if (!isset(self::$tableDesc[$table])) {
            self::$tableDesc[$table] = self::$driver->tableDesc($table);
        }

        return self::$tableDesc[$table];
    }

    /**
     * 执行一条SQL
     *
     * @param string $query
     * @return resource|bool
     */
    public function query($query)
    {
        $result = self::$driver->query($query);
        return $result;
    }

    /**
     * 获取最后执行的SQL语句
     *
     * @return string
     */
    public function _sql()
    {
        return self::$driver->sql();
    }

    /**
     * 重置查询条件
     */
    private function _reset()
    {
        $this->effect = '';
        $this->distinct = '';
        $this->field = '';
        $this->join = [];
        $this->on = [];
        $this->where = [];
        $this->order = '';
        $this->limit = '';
        $this->table = str_ireplace(' as this', '', $this->table);
    }

    /**
     * 获取主键
     *
     * @return string
     */
    private function getPk()
    {
        if (!$this->pk) {
            $tableInfo = $this->tableDesc();
            $pk = '';
            foreach ($tableInfo as $value) {
                if ($value['Key'] == 'PRI') {
                    $pk = $value['Field'];
                    break;
                }
            }
            return $pk;
        }
        return $this->pk;
    }
}
