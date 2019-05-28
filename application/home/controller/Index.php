<?php

namespace application\home\controller;

use framework\library\cache\FileCache;
use framework\library\Loader;
use framework\library\Database;

class Index extends Common {

    public function index() {
        $model = Loader::model('Category');
        FileCache::instance();
        // return $model->where(['id' => ['>', 9]])->delete;
        $db = Database::table('category');
        return [
            'title' => '测试模型高级操作',
            // 'lists' => $model->where('id', '>', 1)->order('id', 'desc')->limit(0, 100)->all,
            'lists' => $db->where('id', '<', 5)->order('id', 'asc')->select(),
            'query' => $model->sql
        ];
    }

    public function testPage() {
        // return '测试页面';
        return $this->fetch('', [
            'a' => '测试页面',
        ]);
    }

}