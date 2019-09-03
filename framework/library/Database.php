<?php

namespace top\library;

use top\library\database\ifs\DatabaseIfs;

/**
 * 数据库操作类
 * @author topnuomi 2018年11月21日
 */
class Database
{

    /**
     * 数据库驱动
     * @var null
     */
    private static $driver = null;

    /**
     * 当前类实例
     * @var array
     */
    private static $instance = [];

    /**
     * 当前表结构
     * @var array
     */
    private static $tableDesc = [];

    /**
     * 数据库配置
     * @var array
     */
    private $config = [];

    /**
     * 当前操作的表
     * @var string
     */
    private $table = '';

    /**
     * 当前表的主键
     * @var string
     */
    private $pk = '';

    /**
     * 多个表（仅delete操作）
     * @var null
     */
    private $effect = null;

    /**
     * 数据去重
     * @var null
     */
    private $distinct = null;

    /**
     * 操作的字段
     * @var null
     */
    private $field = null;

    /**
     * 条件
     * @var array
     */
    private $where = [];

    /**
     * 排序
     * @var null
     */
    private $order = null;

    /**
     * 范围
     * @var null
     */
    private $limit = null;

    /**
     * 多表
     * @var array
     */
    private $join = [];

    /**
     * 关联
     * @var array
     */
    private $on = [];

    /**
     * Database constructor.
     * @param $table
     * @param $pk
     * @param $prefix
     * @throws \Exception
     */
    private function __construct($table, $pk, $prefix)
    {
        $driver = Register::get('DBDriver');
        $this->config = $config = Config::instance()->get('db');
        $this->table = (($prefix) ? $prefix : $config['prefix']) . $table;
        $this->pk = $pk;
        $this->setDriver($driver, $this->config);
    }

    /**
     * 指定表
     * @param $table
     * @param string $pk
     * @param string $prefix
     * @return mixed
     * @throws \Exception
     */
    public static function table($table, $pk = '', $prefix = '')
    {
        if (!isset(self::$instance[$table])) {
            self::$instance[$table] = new self($table, $pk, $prefix);
        }
        return self::$instance[$table];
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
        $tableName = null;
        if (is_array($table) && isset($table[0]) && isset($table[1])) {
            $tableName = $table[0] . $table[1];
        } else {
            $tableName = $this->config['prefix'] . $table;
        }
        $this->join[] = [
            $type,
            $tableName,
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
     * @return mixed
     */
    public function find($param = false)
    {
        if (is_callable($param))
            $param($this);
        $field = $this->getPk();
        $pkWhere = [];
        if (!is_bool($param) && !is_callable($param))
            $pkWhere = [$field => $param];
        $result = self::$driver->find([
            $this->table,
            !empty($this->join),
            $pkWhere
        ], $this->distinct, $this->field, $this->join, $this->on, $this->where, $this->order);
        $this->_reset();
        return $result;
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
        $pkWhere = [];
        if (!is_bool($param) && !is_callable($param))
            $pkWhere = [$field => $param];
        $result = self::$driver->select([
            $this->table,
            !empty($this->join),
            $pkWhere
        ], $this->distinct, $this->field, $this->join, $this->on, $this->where, $this->order, $this->limit);
        $this->_reset();
        foreach ($result as $k => $v)
            $result[$k] = $v;
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
        $pkWhere = [];
        if (!is_bool($param) && !is_callable($param))
            $pkWhere = [$field => $param];
        $result = self::$driver->update([
            $this->table,
            !empty($this->join),
            $pkWhere
        ], $this->join, $this->on, $this->where, $this->order, $this->limit, $data);
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
        $pkWhere = [];
        if (!is_bool($param) && !is_callable($param))
            $pkWhere = [$field => $param];
        $result = self::$driver->delete($this->effect, [
            $this->table,
            !empty($this->join),
            $pkWhere
        ], $this->join, $this->on, $this->where, $this->order, $this->limit);
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
        if (empty($this->field) && $param && !is_callable($param)) {
            $this->field = $param;
        }
        $result = self::$driver->common([
            $this->table,
            !empty($this->join),
            []
        ], $this->distinct, $this->field, $this->join, $this->on, $this->where, $type);
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
     * 开启事务
     */
    public function begin()
    {
        self::$driver->begin();
    }

    /**
     * 提交
     */
    public function commit()
    {
        self::$driver->commit();
    }

    /**
     * 回滚
     */
    public function rollback()
    {
        self::$driver->rollback();
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
        $this->effect = null;
        $this->distinct = null;
        $this->field = null;
        $this->join = [];
        $this->on = [];
        $this->where = [];
        $this->order = null;
        $this->limit = null;
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

    private function __clone()
    {
    }

}
