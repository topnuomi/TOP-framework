<?php

namespace top\traits;

/**
 * Trait Instance
 * @package top\traits
 */
trait Instance
{
    /**
     * 实例
     * @var object
     */
    private static $instance;

    /**
     * 获取类实例
     * @param null $param
     * @return static
     */
    public static function instance($param = null)
    {
        if (!self::$instance) {
            self::$instance = new self($param);
        }
        return self::$instance;
    }

    /**
     * 私有化构造方法
     * Instance constructor.
     */
    private function __construct()
    {
    }

    /**
     * 私有化克隆方法
     */
    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

}