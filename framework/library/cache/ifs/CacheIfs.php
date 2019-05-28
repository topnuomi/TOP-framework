<?php
namespace framework\library\cache\ifs;

interface CacheIfs {

    public function set($name = '', $value = '');

    public function get($name = '');

    public function _unset($name = '');
}