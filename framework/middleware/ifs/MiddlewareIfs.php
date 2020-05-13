<?php

namespace top\middleware\ifs;

use top\library\http\Request;

/**
 * 中间件接口
 *
 * @author topnuomi 2018年11月22日
 */
interface MiddlewareIfs
{
    public function handler(Request $request, \Closure $next);
}
