<?php
namespace app\{name}\controller;

use top\library\Controller;
use app\{name}\model\Demo;

class Index extends Controller {

    public function index() {
        $model = new Demo();
        return [
            'data' => $model->get(1)
        ];
    }
}