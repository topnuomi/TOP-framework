<?php
namespace system\top;

use system\library\Register;

/**
 * 基础控制器
 *
 * @author topnuomi 2018年11月23日
 */
abstract class Controller {

    public function __construct() {}

    public function json($msg, $code = 1, $status = '', $ext = []) {
        if (is_bool($code)) {
            if ($code !== false) {
                $code = 1;
            } else {
                $code = - 1;
            }
        }
        return json_encode([
            'message' => $msg,
            'code' => $code,
            'status' => $status,
            'ext' => $ext
        ]);
    }

    /**
     * @param $name
     * @param $value
     * @throws \system\library\exception\BaseException
     */
    public function param($name, $value) {
        Register::get('View')->param($name, $value);
    }

    /**
     * @param bool $status
     * @throws \system\library\exception\BaseException
     */
    public function cache($status = true) {
        Register::get('View')->cache($status);
    }

    /**
     * @param string $file
     * @param array $param
     * @param bool $cache
     * @return mixed
     * @throws \system\library\exception\BaseException
     */
    public function fetch($file = '', $param = [], $cache = false) {
        return Register::get('View')->fetch($file, $param, $cache);
    }

    /**
     * 跳转（非ajax）
     * @param $url
     */
    public function redirect($url) {
        return redirect($url);
    }

    /**
     * @param $message
     * @param string $url
     * @param int $sec
     * @return mixed|string
     * @throws \system\library\exception\BaseException
     */
    public function tips($message, $url = '', $sec = 3) {
        if (request()->isAjax()) {
            return $this->json($message, '', 'tips', ['url' => $url, 'sec' => $sec]);
        } else {
            return $this->fetch('tips', [
                'message' => $message,
                'url' => $url,
                'sec' => $sec
            ]);
        }
    }
}