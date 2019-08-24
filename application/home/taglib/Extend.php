<?php

namespace app\home\taglib;

class Extend
{
    public $tags = [
        'say' => ['attr' => 'what', 'close' => 0],
        'lists' => ['attr' => 'name', 'close' => 1]
    ];

    public function _say($tag)
    {
        return '<?php echo \'' . $tag['what'] . '\'; ?>';
    }

    public function _lists($tag, $content)
    {
        $parse = "<?php echo '{$tag['name']} start'; ?>";
        $parse .= $content;
        $parse .= "<?php echo '{$tag['name']} end'; ?>";
        return $parse;
    }

}
