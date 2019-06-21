<?php

namespace app\home\controller;

use app\home\model\Users;

class Index extends Common
{

    public function index()
    {
        $model = model(Users::class);
        $lists = $model->all;
        return [
            'lists' => $lists
        ];
    }

    public function hello()
    {
        // return $this->fetch();
        return true;
    }
}