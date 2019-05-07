<?php

namespace system\library;

use system\library\exception\BaseException;
use system\top\Model;

class Loader {

    // 已加载的文件
    private static $files;

    // 模型类实例
    private static $classInstance = [];

    /**
     * 文件自动加载
     * @param $class
     * @return bool
     * @throws BaseException
     */
    public static function _Autoload($class) {
        // 文件从未被加载过
        if (!isset(self::$files[$class])) {
            $classPath = str_replace('\\', '/', $class);
            $file = BASEDIR . '/' . $classPath . '.php';
            if (file_exists($file)) {
                // 文件存在
                self::$files[$class] = $file;
                require $file;
            } else if (file_exists(BASEDIR . '/composer.json')) {
                self::$files[$class] = $file;
            } else {
                // 文件不存在并且没有composer.json则抛出异常
                throw new BaseException('文件' . $file . '不存在');
            }
        }
        return true;
    }

    /**
     * 手动加载模型
     * @param $name
     * @param string $module
     * @return mixed
     */
    public static function model($name, $module = '') {
        (!$module) && $module = Register::get('Router')->module;
        if (!isset(self::$classInstance[$module . $name])) {
            $className = '\\' . APPNS . '\\' . $module . '\\model\\' . $name;
            if (class_exists($className)) {
                self::$classInstance[$module . $name] = new $className();
            } else {
                self::$classInstance[$module . $name] = new Model($name);
            }
        }
        return self::$classInstance[$module . $name];
    }
}