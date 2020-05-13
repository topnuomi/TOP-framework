<?php

namespace top\middleware;

use top\library\Annotation;
use top\library\Application;
use top\library\http\Request;
use top\middleware\ifs\MiddlewareIfs;

/**
 * 执行前置后置方法（前置后置操作中应尽量避免过多的数据库操作）
 *
 * @author topnuomi 2018年11月20日
 */
class Action implements MiddlewareIfs
{
    public function handler(Request $request, \Closure $next)
    {
        $className = $request->controllerFullName();
        $methodAnnotations = Annotation::getMethodAnnotation($className, $request->method());
        $reflectionClass = Application::getReflectionClass($className);

        // 前置操作
        if (isset($methodAnnotations['beforeAction'])) {
            $functions = explode('|', $methodAnnotations['beforeAction']);
            foreach ($functions as $function) {
                if (substr($function, 0, 1) == '_' && $reflectionClass->hasMethod($function)) {
                    $beforeData = (Application::getReflectionMethod($request->controllerFullName(), $function))->invoke(Application::getInstance($request->controllerFullName()));
                    if (!empty($beforeData)) return $beforeData;
                }
            }
            $functions = null;
        }

        $closure = $next();

        // 后置操作
        if (isset($methodAnnotations['afterAction'])) {
            $functions = explode('|', $methodAnnotations['afterAction']);
            foreach ($functions as $function) {
                if (substr($function, 0, 1) == '_' && $reflectionClass->hasMethod($function)) {
                    (Application::getReflectionMethod($request->controllerFullName(), $function))->invoke(Application::getInstance($request->controllerFullName()));
                }
            }
        }

        return $closure;
    }
}
