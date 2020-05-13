<?php

namespace top\library;

use top\traits\Instance;

/**
 * 配置类
 * @author topnuomi 2018年11月20日
 */
class Config
{

    use Instance;

    // 已加载的文件
    private static $files;

    // 保存配置的变量
    private $config = [];

    /**
     * Config constructor.
     */
    private function __construct()
    {
        // 加载默认配置文件
        $configFile = FRAMEWORK_PATH . 'config/config.php';
        $this->config = require $configFile;
    }

    /**
     * 添加配置
     * @param string $name
     * @param string $value
     */
    public function set($name, $value)
    {
        // 组合为数组
        $config = [
            $name => $value
        ];

        // 与原有的配置项合并
        $this->config = array_merge($this->config, $config);
    }

    /**
     * 获取配置
     * @param string $name
     * @return array|mixed
     * @throws \Exception
     */
    public function get($name = '')
    {
        // 加载用户配置文件
        $file = CONFIG_DIR . 'config.php';
        if (!isset(self::$files[$file])) {
            if (file_exists($file)) {
                $config = require $file;
                if (is_array($config) && !empty($config)) {
                    // 合并配置项
                    foreach ($config as $key => $value) {
                        if (array_key_exists($key, $this->config) && is_array($value)) {
                            $this->config[$key] = array_merge($this->config[$key], $value);
                        } else {
                            $this->config[$key] = $value;
                        }
                    }
                }
                self::$files[$file] = true;
            }
        }

        if (empty($this->config)
            || !isset($this->config)
            || !$this->config
            || !isset($this->config[$name])
            || !$this->config[$name]
        ) {
            return [];
        }

        return $this->config[$name];
    }

    /**
     * 从配置中删除某项
     * @param string $name
     */
    public function rm($name)
    {
        $this->config[$name] = null;
        unset($this->config[$name]);
    }
}
