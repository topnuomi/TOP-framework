<?php

namespace app\home\controller;

use top\blocks\Json;

class Index extends Common
{
    use Json;

    public function index()
    {
        return request()->module();
    }

    public function hello()
    {
        return 'hello';
    }

    public function testPage()
    {
        // return $this->fetch();
        return [];
    }
}
