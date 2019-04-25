<?php

namespace application\home\controller;

use system\library\Database;
use system\library\Load;

class Index extends Common {

    /**
     * @return array
     * @throws \system\library\exception\BaseException
     */
    public function index() {
        /*$users = Database::table('users');
        $users->delete(1);*/
        $example = Load::model('Example');
        $lists = $example->getLists();
        return [
            'title' => 'Demo',
            'lists' => $lists
        ];
    }
}