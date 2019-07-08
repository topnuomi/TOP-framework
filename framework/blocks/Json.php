<?php

namespace top\blocks;

trait Json {

    public function returnJson($msg, $code = 0, $data = [])
    {
        if (is_array($msg)) {
            return json_encode($msg);
        } else {
            return json_encode([
                'msg' => $msg,
                'code' => $code,
                'data' => $data
            ]);
        }
    }
}