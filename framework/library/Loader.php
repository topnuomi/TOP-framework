<?php

namespace framework\library;

class Loader {

    // 已加载的文件
    private static $files;

    // 模型类实例
    private static $classInstance = [];

    /**
     * 文件自动加载
     */
    public static function register() {
        $autoload = function ($className = '') {
            // 文件从未被加载过
            if (!isset(self::$files[$className])) {
                $classPath = str_replace('\\', '/', $className);
                $file = BASEDIR . '/' . $classPath . '.php';
                if (file_exists($file)) {
                    // 文件存在
                    self::$files[$className] = $file;
                    require $file;
                } else if (file_exists(BASEDIR . '/composer.json')) {
                    self::$files[$className] = $file;
                } else {
                    return false;
                }
            }
            return true;
        };
        spl_autoload_register($autoload);
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