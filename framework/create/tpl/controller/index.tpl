<?php

namespace app\{name}\controller;

use top\library\Controller;
use top\library\http\Request;

class Index extends Controller
{


    /**
     * 首页
     * @route /
     *
     * @param Request $request
     * @return array
     */
    public function index(Request $request)
    {
        $uri = $request->uri();
        (!$uri) && $this->redirect('index');
        return [
            'uri' => $uri,
            'controller' => $request->controllerFullName(),
            'method' => $request->method(),
        ];
    }
}

