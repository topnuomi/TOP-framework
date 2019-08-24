<?php

namespace app\home\middleware;

use top\middleware\ifs\MiddlewareIfs;

class Test implements MiddlewareIfs
{

    public function before()
    {
        return true;
    }

    public function after($data)
    {
    }

}