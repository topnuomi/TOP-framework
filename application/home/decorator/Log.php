<?php

namespace app\home\decorator;

use top\decorator\ifs\DecoratorIfs;
use top\library\Register;

class Log implements DecoratorIfs
{

    public function before()
    {
    }

    /**
     * @param array $data
     * @throws \Exception
     */
    public function after($data)
    {
        $router = Register::get('Router');
        $message = '当前访问：';
        $message .= $router->module . '.';
        $message .= $router->ctrl . '.';
        $message .= $router->action;
        echo $message;
    }
}