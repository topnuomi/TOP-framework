<?php

namespace app\home\taglib;

use top\library\template\driver\tags\Engine;

class Extend extends Engine
{
    protected $tags = [
        'say' => ['attr' => 'what', 'close' => 0]
    ];

    protected function _say_start($tag)
    {
        return 'echo \'' . $tag['what'] . '\';';
    }

    protected function _say_end($tag, $content)
    {
        return "echo '{$content}123';";
    }

}
