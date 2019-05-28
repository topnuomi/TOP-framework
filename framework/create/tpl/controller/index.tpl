<?php
namespace {namespace}\{name}\controller;

use system\top\Controller;
use {namespace}\{name}\model\Demo;

class Index extends Controller {

    public function index() {
        $model = new Demo();
        return [
            'data' => $model->get(1)
        ];
    }
}