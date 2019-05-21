<?php

namespace application\home\controller;

use system\library\Loader;
use system\library\Database;

class Index extends Common {
    
    public function index() {
        $model = Loader::model('Category');
        // return $model->where(['id' => ['>', 9]])->delete;
        $db = Database::table('category');
        return [
            'title' => '测试模型高级操作',
            // 'lists' => $model->where('id', '>', 1)->order('id', 'desc')->limit(0, 100)->all,
            'lists' => $db->where(['id'=>['>', 100]])->order('id asc')->limit(0, 10)->select(),
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