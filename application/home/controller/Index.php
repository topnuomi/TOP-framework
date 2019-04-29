<?php

namespace application\home\controller;

class Index extends Common {

    /**
     * @return mixed
     * @throws \system\library\exception\BaseException
     */
    public function index() {
        return $this->fetch('A/index', [
            'lists' => [
                0, 1, 2, 3, 4, 5, 6
            ]
        ]);
    }
}