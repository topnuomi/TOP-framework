<?php
namespace system\library\decorator;

use system\library\decorator\ifs\DefaultDecoratorIfs;
use system\library\Register;

class ViewDecorator implements DefaultDecoratorIfs {

    /**
     *
     * {@inheritdoc}
     *
     * @see \system\library\decorator\ifs\DefaultDecoratorIfs::before()
     */
    public function before() {
        // TODO Auto-generated method stub
        if (DEBUG === false) {
            if (isset($_SERVER['REQUEST_URI'])) {
                $fileIdent = md5($_SERVER['REQUEST_URI']);
            } else {
                $route = Register::get('Route');
                $fileIdent = $route->module . $route->ctrl . $route->action;
            }
            $config = Register::get('Config')->get('view');
            $filePath = $config['cacheDir'] . $fileIdent;
            $cache = Register::get('ViewCache');
            if ($cache->check($filePath, $config['cacheTime'])) {
                exit(file_get_contents($filePath));
            }
        }
    }

    /**
     *
     * {@inheritdoc}
     *
     * @see \system\library\decorator\ifs\DefaultDecoratorIfs::after()
     */
    public function after($data) {
        // TODO Auto-generated method stub
    }
}