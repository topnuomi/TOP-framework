<?php

namespace application\home\controller;

use system\library\Loader;

class Index extends Common {
    
    public function index() {
        $model = Loader::model('Category');
        // return $model->where(['id' => ['>', 9]])->delete;
        return [
            'title' => '测试模型高级操作',
            'lists' => $model->order('id desc')->all,
            'query' => $model->sql
        ];
    }

    public function testPage() {
        // return '测试页面';
        return $this->fetch('', [
            'title' => '测试页面',
            'content' => 'fetch方法输出'
        ]);
    }

}