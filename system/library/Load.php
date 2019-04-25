<?php

namespace system\library;


use system\library\exception\BaseException;

class Load {

    // 已加载的文件
    private static $files;

    // 模型类实例
    private static $classInstance = [];

    /**
     * 自动加载
     * @param $class
     * @return bool
     * @throws BaseException
     */
    public static function _Autoload($class) {
        // 文件从未被加载过
        if (!isset(self::$files[$class])) {
            $classPath = str_replace('\\', '/', $class);
            $file = BASEDIR . '/' . $classPath . '.php';
            // 文件存在
            if (file_exists($file)) {
                self::$files[$class] = $file;
                require $file;
            } else {
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
     * @throws BaseException
     */
    public static function model($name, $module = '') {
        (!$module) && $module = Register::get('Route')->module;
        if (!isset(self::$classInstance[$module . $name])) {
            $className = '\\' . APPNAMESPACE . '\\' . $module . '\\model\\' . $name;
            if (class_exists($className)) {
                self::$classInstance[$module . $name] = new $className();
            } else {
                throw new BaseException('Model ' . $className . ' doesn\'t exist');
            }
        }
        return self::$classInstance[$module . $name];
    }
}