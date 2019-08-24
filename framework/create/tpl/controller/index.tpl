<?php
namespace app\{name}\controller;

use top\library\Controller;

class Index extends Controller
{

    public function index()
    {
        $model = model(\app\{name}\model\Demo::class);
        return [
            'hello' => $model->get()
        ];
    }
}
