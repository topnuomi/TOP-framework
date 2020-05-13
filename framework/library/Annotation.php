<?php

namespace top\library;

use top\traits\Instance;

/**
 * 获取类注解
 * Class Annotation
 * @package top\library
 */
class Annotation
{

    use Instance;

    /**
     * 获取到的类注解
     * @var array
     */
    private static $annotations = [];

    /**
     * 获取方法注解
     * @param $className
     * @param $methodName
     * @param null $annotation
     * @return array
     */
    public static function getMethodAnnotation($className, $methodName, $annotation = null)
    {
        return self::getAnnotation($className, $methodName, $annotation);
    }

    /**
     * 获取类注解
     * @param $className
     * @param null $annotation
     * @return array
     */
    public static function getClassAnnotation($className, $annotation = null)
    {
        return self::getAnnotation($className, null, $annotation);
    }

    /**
     * 获取注解
     * @param $className
     * @param null $methodName
     * @param null $annotation
     * @return mixed
     */
    private static function getAnnotation($className, $methodName = null, $annotation = null)
    {
        $ident = md5($className . $methodName);
        if (!isset(self::$annotations[$ident])) {
            // echo '获取' . $className . '::' . $methodName . PHP_EOL;
            $self = self::instance();
            $reflectionClass = Application::getReflectionClass($className);
            if ($methodName) {
                $doc = $reflectionClass->getMethod($methodName)->getDocComment();
            } else {
                $doc = $reflectionClass->getDocComment();
            }
            self::$annotations[$ident] = $self->parseAnnotation($doc);
        }
        return ($annotation) ? self::$annotations[$ident][$annotation] : self::$annotations[$ident];
    }

    /**
     * 解析出注解
     * @param $doc
     * @return array
     */
    private function parseAnnotation($doc)
    {
        $result = [];
        preg_match_all('/@([a-zA-Z]+)\s(.*)/', $doc, $matches);
        if (!empty($matches)) {
            for ($i = 0; $i < count($matches[0]); $i++)
                $result[$matches[1][$i]] = trim($matches[2][$i]);
        }
        $matches = null;
        return $result;
    }
}
