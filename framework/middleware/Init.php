<?php

namespace top\middleware;

use top\middleware\ifs\MiddlewareIfs;

/**
 * 默认中间件
 *
 * @author topnuomi 2018年11月20日
 */
class Init implements MiddlewareIfs
{
    public function handler(\Closure $next)
    {
        // echo '应用开始';
        $closure = $next();
        // echo '应用结束';
        return $closure;
    }
}
