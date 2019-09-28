<?php

namespace top\middleware\ifs;

/**
 * 中间件接口
 *
 * @author topnuomi 2018年11月22日
 */
interface MiddlewareIfs
{
    public function handler(\Closure $next);
}
