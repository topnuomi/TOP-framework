<?php

namespace top\library\template\driver\tags;

use top\library\Register;

/**
 * 模板标签处理类
 * @author topnuomi 2018年11月21日
 */
class Tags
{

    public static $instance;

    private $processing;

    private $tags;

    public $left;

    public $right;

    private $selfTags = [
        // 注释
        '//(.*?)' => '/*\\1*/',
        '/\*(.*?)\*/' => '/*\\1*/',
        // 原生php代码
        'php' => '<?php ',
        '/php' => ' ?>',
        // 变量直接输出
        '\$(.*?)' => 'echo \$\\1;',
        ':(.*?)' => 'echo \\1;',
        // 模板中变量赋值
        'assign:name,value' => '$name = value;',
        // if
        'empty:name' => 'if (empty(name)):',
        'notempty:name' => 'if (!empty(name)):',
        'if (.*?)' => 'if (\\1):',
        'elseif (.*?) /' => 'elseif (\\1):',
        'else /' => 'else:',
        '/(if|empty|notempty)' => 'endif;',
        // foreach
        'loop (.*?)' => '$i = 0; foreach (\\1): $i++;',
        '/loop' => 'endforeach;',
        // for
        'for (.*?)' => 'for (\\1):',
        '/for' => 'endfor;',
        // switch
        'switch:name' => 'switch (\\1):',
        'case:value' => 'case \\1:',
        '/case' => 'break;',
        'default /' => 'default:',
        '/switch' => 'endswitch;'
    ];

    /**
     * 当前类实例
     * @return Tags
     * @throws \Exception
     */
    public static function instance()
    {
        if (!self::$instance) {
            self::$instance = new self();
        }
        return self::$instance;
    }

    /**
     * Tags constructor.
     * @throws \Exception
     */
    private function __construct()
    {
        $config = Register::get('Config')->get('view');
        $this->left = (isset($config['left']) && $config['left']) ? $config['left'] : '{';
        $this->right = (isset($config['right']) && $config['right']) ? $config['right'] : '}';
        $this->compileDir = (isset($config['compileDir']) && $config['compileDir']) ? $config['compileDir'] : './runtime/';
    }

    /**
     * 设置模板标签
     * @param array $array
     */
    private function setTags($array)
    {
        foreach ($array as $key => $value) {
            $tagsInfo = explode(':', $key);
            $tag = $tagsInfo[0];
            // 第一个值不是为空（不是{:xxx}语法）
            if ($tagsInfo[0]) {
                // 存在除标签外的其他属性
                if (isset($tagsInfo[1])) {
                    $attrArr = explode(',', $tagsInfo[1]);
                    // 拼接正则表达式
                    $processingArr = [];
                    for ($i = 0; $i < count($attrArr); $i++) {
                        $processingArr[$attrArr[$i]] = '\\' . ($i + 1);
                        $tag .= '\s' . $attrArr[$i] . '="(.*?)"';
                    }
                    $keys = array_keys($processingArr);
                    $vals = array_values($processingArr);
                    $value = str_replace($keys, $vals, $value);
                }
            } else {
                // {:xxx}语法则保持原样
                $tag = $key;
            }
            // 正则界定符使用#号，避免过多的转义字符
            $this->tags[] = '#' . $this->left . $tag . $this->right . '#i';
            // 不拼接原生脚本开始结束标记符
            $this->processing[] = ($value != '<?php ' && $value != ' ?>') ? '<?php ' . $value . ' ?>' : $value;
        }
    }

    /**
     * 预处理引入视图标签（为了保证require进来的文件中的模板标签可用，必须先进行预处理）
     * @param string $filename
     * @return string
     */
    private function processingViewTag($filename)
    {
        $tags = [
            'view:name' => '$__view__config = \\framework\\library\\Register::get(\'Config\')->get(\'view\'); require BASEDIR . \'/\' . $__view__config[\'dir\'] . \'name\' . \'.\' . $__view__config[\'ext\'];'
        ];
        $this->setTags($tags);
        $content = file_get_contents($filename);
        $result = preg_replace($this->tags, $this->processing, $content);
        $tempFileName = $this->compileDir . md5($filename) . '_temp.php';
        if (!is_dir($this->compileDir)) {
            mkdir($this->compileDir, 0777, true);
        }
        // 创建临时文件
        file_put_contents($tempFileName, $result);
        ob_start();
        require $tempFileName;
        // 拿到临时创建的文件内容
        $content = ob_get_contents();
        ob_clean();
        // 删除临时文件
        @unlink($tempFileName);
        return $content;
    }

    /**
     * 处理模板文件中的标签（插件模板不解析view标签）
     * @param $filename
     * @return string
     * @throws \Exception
     */
    public function processing($filename)
    {
        $content = $this->processingViewTag($filename);
        // 加载预设模板标签
        $this->setTags($this->selfTags);
        // 加载自定义模板标签
        // 文件位置固定
        $tagsFile = BASEDIR . '/' . Register::get('Router')->module . '/config/tags.php';
        if (file_exists($tagsFile)) {
            $tags = require $tagsFile;
            $this->setTags($tags);
        }
        $result = preg_replace($this->tags, $this->processing, $content);
        if (!is_dir($this->compileDir)) {
            mkdir($this->compileDir, 0777, true);
        }
        // 最终过滤内容中?\>与<?php中间的内容
        $result = preg_replace('#\?>([\r|\n|\s]*?)<\?php#', '', $result);
        $filename = $this->compileDir . md5($filename) . '.php';
        file_put_contents($filename, "<?php /* topnuomi */ (!defined('BASEDIR')) && exit(0); ?>" . $result);
        return $filename;
    }
}
