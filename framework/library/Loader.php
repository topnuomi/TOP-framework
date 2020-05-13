<?php

namespace top\library;

class Loader
{

    private $prefixes = [];

    private static $number = 0;

    /**
     * 注册自动加载
     */
    public function register()
    {
        spl_autoload_register([$this, 'loadClass']);
    }

    /**
     * 添加命名空间映射
     * @param $name
     * @param $path
     */
    public function set($name, $path)
    {
        if (isset($this->prefixes[$name])) {
            array_push($this->prefixes[$name], $path);
        } else {
            $this->prefixes[$name] = [$path];
        }
    }

    /**
     * 尝试根据前缀和类名加载文件
     * @param $class
     * @return bool
     */
    private function loadClass($class)
    {
        // 首次，将前缀等于当前类名
        $prefix = $class;
        // 从最后一个反斜杠开始分割前缀与类名
        while (($pos = strrpos($prefix, '\\')) !== false) {
            // 取出当前位置反斜杠分割的前缀
            $prefix = substr($class, 0, $pos + 1);
            // 取出分割出的实际类名
            $className = substr($class, $pos + 1);
            // 尝试去加载文件
            $loadFile = $this->loadFile($prefix, $className);
            if ($loadFile) {
                return true;
            }
            $prefix = rtrim($prefix, '\\');
        }
        // 未找到文件
        return false;
    }

    /**
     * 加载文件
     * @param $prefix
     * @param $class
     * @return bool
     */
    private function loadFile($prefix, $class)
    {
        // echo $class . '<br>';
        $prefix = trim($prefix, '\\');
        // 如果存在此前缀
        if (isset($this->prefixes[$prefix])) {
            // 遍历当前前缀下的目录
            foreach ($this->prefixes[$prefix] as $key => $value) {
                // 拼接文件名
                $file = str_replace('\\', '/', $value . $class) . '.php';
                /*echo '<br>';
                echo $file . '<br>';*/
                // 如果文件存在则加载文件
                if (is_file($file)) {
                    require $file;
                    self::$number++;
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * 内置自动加载器加载的文件数
     * @return int
     */
    public static function getFileNumber()
    {
        return self::$number;
    }
}
