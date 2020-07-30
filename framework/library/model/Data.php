<?php
/**
 * Author: TopNuoMi
 * Date: 2020/07/07
 */

namespace top\library\model;

use ArrayAccess;
use Iterator;

class Data implements ArrayAccess, Iterator
{
    private $data = [];

    public function __construct($data)
    {
        $this->data = $data;
    }

    /**
     * 获取$this->>data中的值
     * @param $key
     * @return mixed|null
     */
    private function getValue($key)
    {
        if (isset($this->data[$key]) && array_key_exists($key, $this->data)) {
            return $this->data[$key];
        }
        return false;
    }

    /**
     * 外部获取$this->data中的值
     * @param $key
     * @return mixed|null
     */
    public function value($key)
    {
        return $this->getValue($key);
    }

    /**
     * 将字段当做属性调用
     * @param $name
     * @return mixed|null
     */
    public function __get($name)
    {
        return $this->getValue($name);
    }

    /**
     * Whether a offset exists
     * @link https://php.net/manual/en/arrayaccess.offsetexists.php
     * @param mixed $offset <p>
     * An offset to check for.
     * </p>
     * @return boolean true on success or false on failure.
     * </p>
     * <p>
     * The return value will be casted to boolean if non-boolean was returned.
     * @since 5.0.0
     */
    public function offsetExists($offset)
    {
        return false !== $this->getValue($offset);
    }

    /**
     * Offset to retrieve
     * @link https://php.net/manual/en/arrayaccess.offsetget.php
     * @param mixed $offset <p>
     * The offset to retrieve.
     * </p>
     * @return mixed Can return all value types.
     * @since 5.0.0
     */
    public function offsetGet($offset)
    {
        if ($this->offsetExists($offset)) {
            return $this->getValue($offset);
        }
        trigger_error('不存在的数组索引：' . $offset);
    }

    /**
     * Offset to set
     * @link https://php.net/manual/en/arrayaccess.offsetset.php
     * @param mixed $offset <p>
     * The offset to assign the value to.
     * </p>
     * @param mixed $value <p>
     * The value to set.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetSet($offset, $value)
    {
        $this->data[$offset] = $value;
    }

    /**
     * Offset to unset
     * @link https://php.net/manual/en/arrayaccess.offsetunset.php
     * @param mixed $offset <p>
     * The offset to unset.
     * </p>
     * @return void
     * @since 5.0.0
     */
    public function offsetUnset($offset)
    {
        unset($this->data[$offset]);
    }

    /**
     * Return the current element
     * @link https://php.net/manual/en/iterator.current.php
     * @return mixed Can return any type.
     * @since 5.0.0
     */
    public function current()
    {
        return current($this->data);
    }

    /**
     * Move forward to next element
     * @link https://php.net/manual/en/iterator.next.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function next()
    {
        return next($this->data);
    }

    /**
     * Return the key of the current element
     * @link https://php.net/manual/en/iterator.key.php
     * @return mixed scalar on success, or null on failure.
     * @since 5.0.0
     */
    public function key()
    {
        return key($this->data);
    }

    /**
     * Checks if current position is valid
     * @link https://php.net/manual/en/iterator.valid.php
     * @return boolean The return value will be casted to boolean and then evaluated.
     * Returns true on success or false on failure.
     * @since 5.0.0
     */
    public function valid()
    {
        return $this->current() !== false;
    }

    /**
     * Rewind the Iterator to the first element
     * @link https://php.net/manual/en/iterator.rewind.php
     * @return void Any returned value is ignored.
     * @since 5.0.0
     */
    public function rewind()
    {
        reset($this->data);
    }

    /**
     * 转数组
     * @return array
     */
    public function toArray()
    {
        return $this->data;
    }

    /**
     * 转JSON
     * @return string
     */
    public function toJson()
    {
        return json_encode($this->data, JSON_UNESCAPED_UNICODE);
    }

}
