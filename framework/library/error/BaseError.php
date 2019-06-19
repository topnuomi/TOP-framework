<?php

namespace top\library\error;

use Throwable;

class BaseError extends \Error
{

    public function __construct($message = "", $code = 0, Throwable $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    /**
     * @param $errno
     * @param $errstr
     * @param $errfile
     * @param $errline
     */
    public function handler($errno, $errstr, $errfile, $errline)
    {
        if (DEBUG) {
            $content = $errstr . '<br />' . $errfile . ' 第' . $errline . '行';
        } else {
            $content = $errstr;
        }
        // throw new BaseException($errstr, 0, null, $errfile, $errline);
        echo '<p style="font-size: 12px; font-weight: 100;">' . $content . '</p>';
    }
}
