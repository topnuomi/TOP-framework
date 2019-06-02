<?php

namespace top\decorator;

use top\decorator\ifs\DecoratorIfs;

/**
 * 辅助控制器的装饰器
 *
 * @author topnuomi 2018年11月22日
 */
class StringDecorator implements DecoratorIfs {

    public function before() {
        // TODO Auto-generated method stub
    }

    /**
     * 字符串则直接输出
     * @param array $data
     */
    public function after($data) {
        // TODO Auto-generated method stub
        // 如果是字符串，直接echo
        if (!is_array($data) && !is_bool($data) && !is_object($data)) {
            echo $data;
        }
    }
}