<?php

namespace top\library;

use Exception;
use top\library\exception\DatabaseException;

/**
 * 基础模型
 * @author topnuomi 2018年11月23日
 *
 * @method $this alias($name)
 * @method $this distinct(bool $distinct)
 * @method $this field(string|array $field)
 * @method $this where($field, $condition = null, $value = null)
 * @method $this order(string $order)
 * @method $this limit(string|array $limit)
 * @method $this join(string $table, string $on, string $type = null)
 * @method $this sql()
 */
class Model
{

    /**
     * 当前表名
     * @var string
     */
    protected $table = '';

    /**
     * 主键
     * @var string
     */
    protected $pk = '';

    /**
     * 当前模型的表前缀
     * @var string
     */
    protected $prefix = '';

    /**
     * 字段映射
     * @var array
     */
    protected $map = [];

    /**
     * insert值映射
     * @var array
     */
    protected $insertReplace = [];

    /**
     * update值映射
     * @var array
     */
    protected $updateReplace = [];

    /**
     * 出库值映射
     * @var array
     */
    protected $outReplace = [];

    /**
     * 模型消息（请注意：在方法中赋值会覆盖掉数据验证的message）
     * @var string
     */
    protected $message = '';

    /**
     * 自动验证
     * @var array
     */
    protected $validate = [];

    /**
     * 是否为insert操作，决定如何验证数据
     * true：验证模型中配置的全部字段
     * false：仅验证$data中存在的字段
     * @var bool
     */
    private $isInsert = false;

    /**
     * 用数据库配置获取实例
     * Model constructor.
     * @param string $table
     */
    public function __construct($table = '')
    {
        if ($table) {
            $this->table = $table;
        } else if (!$this->table) {
            $table = get_table_name(static::class);
            $this->table = $table;
        }
    }

    /**
     * 获取Database实例
     * @return mixed
     */
    private function database()
    {
        return Database::table($this->table, $this->pk, $this->prefix);
    }

    // 可以静态调用的方法--开始

    private function _alias($name)
    {
        $this->database()->alias($name);
        return $this;
    }

    /**
     * 过滤重复值
     * @param $flag
     * @return $this
     */
    private function _distinct($flag = true)
    {
        $this->database()->distinct($flag);
        return $this;
    }

    /**
     * 指定字段
     * @param $field
     * @return $this
     */
    private function _field($field)
    {
        $this->database()->field($field);
        return $this;
    }

    /**
     * 查询条件
     * @return $this
     */
    private function _where()
    {
        call_user_func_array([
            $this->database(),
            'where'
        ], func_get_args());
        return $this;
    }

    /**
     * 排序
     * @return $this
     */
    private function _order()
    {
        call_user_func_array([
            $this->database(),
            'order'
        ], func_get_args());
        return $this;
    }

    /**
     * 限制
     * @return $this
     */
    private function _limit()
    {
        call_user_func_array([
            $this->database(),
            'limit'
        ], func_get_args());
        return $this;
    }

    /**
     * 多表
     * @param $table
     * @param $on
     * @param string $type
     * @return $this
     */
    private function _join($table, $on, $type = 'INNER')
    {
        $this->database()->join($table, $on, $type);
        return $this;
    }

    /**
     * 获取最后一次执行的SQL
     *
     * @return string
     */
    private function _sql()
    {
        return $this->database()->sql();
    }

    // 可静态调用的方法--结束

    /**
     * 插入记录
     * @param array $data
     * @return bool
     */
    public function insert($data = [])
    {
        $this->isInsert = true;
        $data = $this->processData($data);
        if ($data) {
            // 此处取消了数据验证，在$this->>data()方法中验证，减少一次数据库查询
            // 入库时最后的数据处理
            $data = $this->inHandle($data);
            return $this->database()->insert($data);
        }
        return false;
    }

    /**
     * 删除记录
     * @param string|bool $param
     * @return number|boolean
     */
    public function delete($param = false)
    {
        return $this->database()->delete($param);
    }

    /**
     * 更新记录
     * @param $data
     * @param string|bool $param
     * @return bool
     */
    public function update($data, $param = false)
    {
        $this->isInsert = false;
        $data = $this->processData($data);
        if ($data) {
            // 此处取消了数据验证，在$this->data()方法中验证，减少一次数据库查询
            // 入库时最后的数据处理
            $data = $this->inHandle($data);
            return $this->database()->update($data, $param);
        }
        return false;
    }

    /**
     * 查询单条记录
     * @param string|bool $param
     * @param bool $notRaw
     * @return array
     */
    public function find($param = false, $notRaw = true)
    {
        $result = $this->database()->find($param);
        if ($notRaw) {
            if (is_array($result)) {
                $result = $this->outHandle($result);
            }
        }
        return $result;
    }

    /**
     * 查询所有记录
     * @param string|bool $param
     * @param bool $notRaw
     * @return array
     */
    public function select($param = false, $notRaw = true)
    {
        $result = $this->database()->select($param);
        if ($notRaw) {
            if (is_array($result)) {
                $result = $this->outHandle($result);
            }
        }
        return $result;
    }

    /**
     * 计数
     * @param null $param
     * @return mixed
     */
    public function count($param = null)
    {
        return $this->database()->common($param, 'count');
    }

    /**
     * 平均值
     * @param null $param
     * @return mixed
     */
    public function avg($param = null)
    {
        return $this->database()->common($param, 'avg');
    }

    /**
     * 最大值
     * @param null $param
     * @return mixed
     */
    public function max($param = null)
    {
        return $this->database()->common($param, 'max');
    }

    /**
     * 最小值
     * @param null $param
     * @return mixed
     */
    public function min($param = null)
    {
        return $this->database()->common($param, 'min');
    }

    /**
     * 求和
     * @param null $param
     * @return mixed
     */
    public function sum($param = null)
    {
        return $this->database()->common($param, 'sum');
    }

    /**
     * 执行一条SQL
     * @param $query
     * @param array $params
     * @return mixed
     */
    public function query($query, $params = [])
    {
        return $this->database()->query($query, $params);
    }

    /**
     * MySQL事务
     * @param $action
     * @return bool
     */
    public function transaction($action)
    {
        $db = $this->database();
        // 开启事务
        $db->begin();
        try {
            $action();
            // 执行操作后提交
            $db->commit();
            return true;
        } catch (DatabaseException $exception) {
            // 回滚
            $db->rollback();
            return false;
        }
    }

    /**
     * 返回PDO
     * @return \PDO
     */
    public function getPDO()
    {
        return $this->database()->getPDO();
    }

    /**
     * 获取表单数据
     * @param array $data
     * @return array|bool
     */
    public function data($data = [])
    {
        return $this->processMapped($data);
    }

    /**
     * 处理字段映射并验证数据
     * @param array $data
     * @return array|bool
     */
    private function processMapped($data = [])
    {
        $data = (empty($data)) ? $_POST : $data;
        foreach ($data as $key => $value) {
            foreach ($this->map as $k => $v) {
                if ($key == $k) {
                    $data[$v] = $value;
                    unset($data[$key]);
                }
            }
        }
        // 验证数据
        if ($this->validate($data)) {
            return $data;
        }
        return false;
    }

    /**
     * 入库前进行数据处理
     * @param $data
     * @return array|bool
     */
    private function processData($data)
    {
        if (is_callable($data)) {
            // 如果$data是匿名函数，则处理$this->data()处理post的数据
            $modelData = $this->data();
            if ($modelData) {
                $data = $data($modelData);
            } else {
                return false;
            }
        } else if (empty($data)) {
            // 如果$data为空，则直接赋值为$this->data()
            $data = $this->data();
        } else {
            // 否则用$this->data()处理$data的字段映射
            $data = $this->data($data);
        }
        return $data;
    }

    /**
     * 入库时替换值
     *
     * @param array $data
     * @return array
     */
    private function inHandle($data)
    {
        $replace = ($this->isInsert) ? $this->insertReplace : $this->updateReplace;
        foreach ($replace as $key => $value) {
            if (!array_key_exists($key, $data)) {
                $data[$key] = '';
            }
            if (is_array($value)) {
                if (isset($value[1]) && $value[1] === true) {
                    $object = static::class;
                    if (method_exists($object, $value[0])) {
                        $methodName = $value[0];
                        $fieldValue = call_user_func_array([
                            new $object(),
                            $methodName
                        ], [
                            $data[$key]
                        ]);
                    } else {
                        throw new Exception('方法' . $object . '->' . $value[0] . '不存在');
                    }
                } else if (isset($value[0]) && function_exists($value[0])) {
                    $fieldValue = $value[0]($data[$key]);
                } else {
                    $fieldValue = isset($value[0]) ? $value[0] : $data[$key];
                }
            } else if (function_exists($value)) {
                $fieldValue = $value($data[$key]);
            } else {
                $fieldValue = $value;
            }
            $data[$key] = $fieldValue;
        }
        return $data;
    }

    /**
     * 出库时替换值
     *
     * @param array $data
     * @return array
     */
    private function outHandle($data)
    {
        foreach ($this->outReplace as $key => $value) {
            if (count($data) == count($data, 1)) {
                if (array_key_exists($key, $data)) {
                    $data[$key] = $this->callOutReplaceFunction($data[$key], $value);
                }
            } else {
                foreach ($data as $k => $v) {
                    if (array_key_exists($key, $v)) {
                        $data[$k][$key] = $this->callOutReplaceFunction($data[$k][$key], $value);
                    }
                }
            }
        }
        return $data;
    }

    /**
     * 调用函数、方法替换值
     * @param $value
     * @param $function
     * @return mixed
     */
    private function callOutReplaceFunction($value, $function)
    {
        if (is_array($function) && (isset($function[1]) && $function[1] === true)) {
            $value = $this->{$function[0]}($value);
        } else {
            if (is_array($function)) {
                $function = $function[0];
            }
            if (function_exists($function)) {
                $value = $function($value);
            }
        }
        return $value;
    }

    /**
     * 验证表单
     * @param $data
     * @return bool
     */
    private function validate($data)
    {
        foreach ($this->validate as $key => $value) {
            if (is_array($value)) {
                if (count($value) == count($value, 1)) {
                    if (!$this->validateCallUserFunction($key, $value, $data)) {
                        return false;
                    }
                } else {
                    foreach ($value as $k => $v) {
                        if (!$this->validateCallUserFunction($key, $v, $data)) {
                            return false;
                        }
                    }
                }
            } /*else {
                throw new BaseException('自动验证值必须为数组');
            }*/
        }
        return true;
    }

    /**
     * 调用对应验证函数（如果update时不想验证数据，请在之前unset掉对应键值）
     * @param string $key
     * @param $validate
     * @param $data
     * @return bool
     */
    private function validateCallUserFunction($key, $validate, $data)
    {
        $funcName = $validate[0];
        $tips = end($validate);
        // 将第一个值赋值为将要检查的值
        if (array_key_exists($key, $data)) {
            $validate[0] = $data[$key];
            unset($validate[count($validate) - 1]);
            if (call_user_func_array($funcName, $validate) === false) {
                $this->message = $tips;
                return false;
            }
        } else {
            if ($this->isInsert) {
                $this->message = $tips;
                return false;
            }
        }
        return true;
    }

    /**
     * 获取信息
     * @return string
     */
    public function getMessage()
    {
        return $this->message;
    }

    /**
     * 非静态调用连贯操作
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public function __call($name, $arguments)
    {
        $methodName = '_' . $name;
        if (method_exists($this, $methodName)) {
            return call_user_func_array([$this, $methodName], $arguments);
        } else throw new Exception('不存在的方法：' . $name);
    }
    
    /**
     * 静态调用连贯操作
     * @param string $name
     * @param array $arguments
     * @return mixed
     */
    public static function __callStatic($name, $arguments)
    {
        $methodName = '_' . $name;
        if (!method_exists(static::class, $methodName)) {
            $methodMap = ['all' => 'select', 'get' => 'find'];
            if (isset($methodMap[$name])) {
                $methodName = $methodMap[$name];
            } else throw new Exception('不存在的方法：' . $name);
        }
        return call_user_func_array([new static, $methodName], $arguments);
    }

}
