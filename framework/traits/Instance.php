<?php

namespace top\traits;


trait Instance
{
    private static $instance;

    public static function instance($param = null)
    {
        if (!self::$instance) {
            self::$instance = new self($param);
        }
        return self::$instance;
    }

    private function __construct()
    {
    }

    private function __clone()
    {
        // TODO: Implement __clone() method.
    }

}