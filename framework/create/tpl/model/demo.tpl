<?php
namespace app\{name}\model;

use top\library\Model;

class Demo extends Model {

    protected $table = '';
    protected $pk = '';
    protected $map = [];
    
    public function get($id) {
        return $id;
    }
}