<?php

namespace application\home\controller;

class Index extends Common {
    
    public function index() {
        return $this->fetch('', [
            'lists' => [
                0, 1, 2, 3, 4, 5, 6
            ]
        ]);
    }
}