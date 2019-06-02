<?php

namespace app\home\decorator;

use top\decorator\ifs\DecoratorIfs;
use top\library\Register;

class Log implements DecoratorIfs {

    public function before() {
        // TODO: Implement before() method.
    }

    /**
     * @param array $data
     * @throws \Exception
     */
    public function after($data) {
        // TODO: Implement after() method.
        $router = Register::get('Router');
        $message = '当前访问：';
        $message .= $router->module . '.';
        $message .= $router->ctrl . '.';
        $message .= $router->action;
        echo $message;
    }
}