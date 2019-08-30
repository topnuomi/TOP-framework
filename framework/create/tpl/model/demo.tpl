<?php

namespace app\{name}\model;

use top\library\Model;

class Demo extends Model
{

    protected $table = '';
    protected $pk = '';
    protected $map = [];
    
    public function get()
    {
        return '模块{name}正在运行...';
    }
}
