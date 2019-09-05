<?php

namespace app\{name}\controller;

use app\{name}\model\Demo;

class Index
{

    public function index()
    {
        $model = model(Demo::class);
        return [
            'hello' => $model->get()
        ];
    }
}
