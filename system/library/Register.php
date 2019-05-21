<?php

namespace system\library;
use system\library\exception\BaseException;

/**
 * 注册器
 *
 * @author topnuomi 2018年11月19日
 */
class Register {

    // 存放类的变量
    public static $register;

    /**
     * 注册类
     *
     * @param string $name
     * @param string $value
     * @return boolean
     */
    public static function set($name, $value) {
        if (!isset(self::$register[$name])) {
            self::$register[$name] = $value();
        }
        return true;
    }

    /**
     * 获取类实例
     * @param $name
     * @param array $param
     * @return mixed
     * @throws BaseException
     */
    public static function get($name, $param = []) {
        if (!isset(self::$register[$name])) {
            throw new BaseException($name . '尚未注册');
        }
        return self::$register[$name];
    }

    /**
     * 删除类实例
     *
     * @param string $name
     */
    public static function _unset($name) {
        unset(self::$register[$name]);
    }
}