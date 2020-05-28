<?php

namespace top\traits;

/**
 * Trait Json
 * @package top\traits
 */
trait Json {

    /**
     * 格式化数据为json
     * @param $msg
     * @param int $code
     * @param array $data
     * @return false|string
     */
    public function returnJson($msg, $code = 0, $data = [])
    {
        if (is_array($msg)) {
            return json_encode($msg);
        } else {
            return json_encode([
                'msg' => $msg,
                'code' => $code,
                'data' => $data,
            ]);
        }
    }
}