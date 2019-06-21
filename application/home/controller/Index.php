<?php

namespace app\home\controller;

use app\home\model\Job;

class Index extends Common
{

    public function index()
    {
        $model = model(Job::class);

        return [
            'title' => 'test',
            'lists' => $model->order('id desc')->select(),
            'query' => $model->sql
        ];
    }

    public function testPage()
    {
        return $this->fetch('', [
            'a' => '测试页面',
        ]);
    }

}