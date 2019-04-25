<?php
/**
 * Created by PhpStorm.
 * User: TOP糯米
 * Date: 2019/3/30/0030
 * Time: 21:54
 */

namespace application\home\controller;


use system\library\Register;
use system\top\Controller;

class Common extends Controller {

    protected $secret = '';

    public function __construct() {
        $this->secret = Register::get('Config')->get('secret');
    }

}