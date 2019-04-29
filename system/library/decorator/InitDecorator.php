<?php

namespace system\library\decorator;

use system\library\Register;
use system\library\decorator\ifs\DefaultDecoratorIfs;
use system\top\View;
use system\library\cache\FileCache;

/**
 * 初始化
 *
 * @author topnuomi 2018年11月20日
 */
class InitDecorator implements DefaultDecoratorIfs {

    /**
     * 注册一些可能会用到的类
     * {@inheritdoc}
     * @see \system\library\decorator\ifs\DefaultDecoratorIfs::before()
     * @throws \system\library\exception\BaseException
     */
    public function before() {
        $route = Register::get('Route');
        $sessionConfig = Register::get('Config')->get('session');
        if (!empty($sessionConfig) && $sessionConfig['open'] === true)
            session_start();
        // 数据库驱动
        $config = Register::get('Config')->get('db');
        $driver = $config['driver'] ? $config['driver'] : 'MySQLi';
        Register::set('DBDriver', function () use ($driver) {
            $class = '\\system\\library\\database\\driver\\' . $driver;
            return $class::instance();
        });
        // 视图文件缓存
        Register::set('ViewCache', function () {
            return FileCache::instance();
        });
        // 配置文件中配置的注册
        $initRegister = Register::get('Config')->get('register');
        if (!empty($initRegister)) {
            foreach ($initRegister as $key => $value) {
                Register::set($key, function () use ($value) {
                    $value = '\\' . str_replace('.', '\\', ltrim($value, '.'));
                    return $value::instance();
                });
            }
        }
        // 注册视图
        Register::set('View', function () {
            return View::instance();
        });
        // 加载系统函数库
        require BASEDIR . '/system/top/functions/functions.php';
        // 加载用户函数库
        $funcFile = BASEDIR . '/' . APPNAMESPACE . '/' . $route->module . '/functions.php';
        if (file_exists($funcFile)) {
            require $funcFile;
        }
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \system\library\decorator\ifs\DefaultDecoratorIfs::after()
     */
    public function after($data) {
        // TODO Auto-generated method stub
    }
}