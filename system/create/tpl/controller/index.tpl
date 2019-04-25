<?php
namespace {namespace}\{name}\controller;

use system\top\Controller;
use {namespace}\{name}\model\demo;

class index extends Controller {

    public function index() {
        $model = new demo();
        return [
            'data' => $model->get(1)
        ];
    }
}