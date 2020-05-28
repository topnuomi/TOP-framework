<?php

namespace top\traits;

trait Magic
{
    private static $magicParameters = [];

    /**
     * 使用成员变量调用某些类
     * @param $name
     * @return mixed
     */
    public function __get($name)
    {
        if (!isset(self::$magicParameters[$name])) {
            $stringArray = str_split($name);
            $length = count($stringArray);
            $start = 0;
            $prefix = '';
            for ($i = 0; $i < $length; $i++) {
                $ord = ord($stringArray[$i]);
                if ($ord > 64 && $ord < 91 && $i != 0) { // 找大写字母
                    $start = $i;
                    break;
                }
                $prefix .= $stringArray[$i];
            }
            if ($prefix != '' && $start > 0) { // 存在前缀，并且存在大写字母
                $value = substr($name, $start, $length);
                switch ($prefix) {
                    case 'model':
                        self::$magicParameters[$name] = model($value);
                        break;
                    case 'logic':
                        self::$magicParameters[$name] = logic($value);
                        break;
                }
            } else {
                // 无法被处理，抛出异常
                throw new \Exception('变量' . $name . '不存在');
            }
        }

        return self::$magicParameters[$name];
    }
}
