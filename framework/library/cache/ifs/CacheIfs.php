<?php

namespace top\library\cache\ifs;

interface CacheIfs
{

    public function set($name, $value, $timeout = null);

    public function get($name = null, $callable = null);

    public function remove($name = null);
}
