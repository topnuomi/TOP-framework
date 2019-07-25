<?php

namespace top\middleware;

use top\library\http\Response;
use top\library\Register;
use top\middleware\ifs\MiddlewareIfs;

class View implements MiddlewareIfs
{

    public function before()
    {
        // TODO: Implement before() method.
        if (!DEBUG) {
            if (isset($_SERVER['REQUEST_URI'])) {
                $fileIdent = md5($_SERVER['REQUEST_URI']);
            } else {
                $fileIdent = request()->module() . request()->controller() . request()->method();
            }
            $config = Register::get('Config')->get('view');
            $filename = $config['cacheDir'] . $fileIdent;
            $cache = Register::get('FileCache');
            if ($cache->check($filename, $config['cacheTime'])) {
                echo Response::instance()->dispatch(file_get_contents($filename));
                exit;
            }
        }
    }

    public function after($data)
    {
        // TODO: Implement after() method.
    }

}