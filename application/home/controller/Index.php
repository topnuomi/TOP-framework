<?php

namespace app\home\controller;

use app\home\model\Job;

class Index extends Common {

    public function _init() {
        echo 1;
    }

    public function index() {
        $model = model(Job::class);
        return [
            'title' => '测试模型高级操作',
            'lists' => $model->select(),
            'query' => $model->sql
        ];
    }

    public function testPage() {
        return $this->fetch('', [
            'a' => '测试页面',
        ]);
    }

}