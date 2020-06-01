<?php

namespace top\library\template\driver\engine;

use Exception;
use top\traits\Instance;

/**
 * 模板标签解析
 * Class Template
 * @package lib
 */
class Engine
{

    use Instance;

    /**
     * @var string 左定界符
     */
    private $left = '<';

    /**
     * @var string 右定界符
     */
    private $right = '>';

    /**
     * @var array 标签定义
     */
    protected $tags = [];

    /**
     * @var array 模板配置
     */
    protected $config = [];

    /**
     * @var array 标签库
     */
    private $libs = [];

    /**
     * @var array 标签库类实例
     */
    private $libInstance = [];

    /**
     * 构造方法
     * Engine constructor.
     * @param array $config
     */
    private function __construct($config = [])
    {
        $this->config = $config;
        if (isset($this->config['left']) && $this->config['left']) {
            $this->left = $this->config['left'];
        }
        if (isset($this->config['right']) && $this->config['right']) {
            $this->right = $this->config['right'];
        }
    }

    /**
     * 处理模板继承
     * @param $template
     * @return mixed
     */
    private function parseExtend($template)
    {
        $pattern = '/' . $this->left . 'extend\s+file[\s\S]*?=[\s\S]*?[\'"](.*?)[\'"][\s\S]*?\/' . $this->right . '/is';
        preg_match($pattern, $template, $matches);
        if (!empty($matches)) {
            $blockPattern = '/' . $this->left . 'block\s+name[\s\S]*?=[\s\S]*?[\'"](.*?)[\'"][\s\S]*?' . $this->right;
            $blockPattern .= '([\s\S]*?)' . $this->left . '\/block' . $this->right . '/is';
            // 获得被继承的模板内容
            $file = $this->config['dir'] . $matches[1] . '.' . ltrim($this->config['ext'], '.');
            $extendFileContent = null;
            if (file_exists($file)) {
                $extendFileContent = file_get_contents($file);
            }
            // 处理继承中的include标签
            $tempContent = $this->parseInclude($extendFileContent);
            $extendFileContent = $tempContent !== false ? $tempContent : $extendFileContent;
            // 被继承模板中的块
            preg_match_all($blockPattern, $extendFileContent, $extendResult);
            // 继承模板中的块
            preg_match_all($blockPattern, $template, $templateResult);
            // 组合搜索的块数组
            $search = [];
            $defaultContent = [];
            for ($i = 0; $i < count($extendResult[0]); $i++) {
                $search[$extendResult[1][$i]] = $extendResult[0][$i];
                $defaultContent[$extendResult[1][$i]] = $extendResult[2][$i];
            }
            // 组合替换的块数组
            $replace = [];
            for ($j = 0; $j < count($templateResult[0]); $j++) {
                $replace[$templateResult[1][$j]] = $templateResult[2][$j];
            }
            // 块是否在继承模板中存在
            $searchArray = [];
            $replaceArray = [];
            foreach ($search as $key => $value) {
                $searchArray[] = $value;
                if (isset($replace[$key])) {
                    $replaceArray[] = $replace[$key];
                } else {
                    $replaceArray[] = $defaultContent[$key];
                }
            }
            $template = str_replace($searchArray, $replaceArray, $extendFileContent);
        }
        return $template;
    }

    /**
     * 处理include标签
     * @param $template
     * @return null|string|string[]
     */
    private function parseInclude($template)
    {
        $pattern = '/' . $this->left . 'include\s+file[\s\S]*?=[\s\S]*?[\'"](.*?)[\'"][\s\S]*?\/' . $this->right . '/is';
        $template = preg_replace_callback($pattern, function ($result) {
            $string = null;
            $file = $this->config['dir'] . $result[1] . '.' . ltrim($this->config['ext'], '.');
            if (file_exists($file)) {
                $string = file_get_contents($file);
            }
            return $string;
        }, $template);
        // 处理多层include
        if ($this->hasInclude($template)) {
            $template = $this->parseInclude($template);
        }
        return $template;
    }

    /**
     * 检测是否含有include
     * @param $template
     * @return bool
     */
    private function hasInclude($template)
    {
        $pattern = '/' . $this->left . 'include\s+file[\s\S]*?=[\s\S]*?[\'"](.*?)[\'"][\s\S]*?\/' . $this->right . '/is';
        preg_match($pattern, $template, $matches);
        return !empty($matches);
    }

    /**
     * 分析参数以及函数输出
     * @param $template
     * @return mixed
     */
    private function parseVars($template)
    {
        preg_match_all('/{(.*?)}/', $template, $matches);
        $search = [];
        $replace = [];
        for ($i = 0; $i < count($matches[0]); $i++) {
            $start = substr($matches[1][$i], 0, 1);
            $search[] = $matches[0][$i];
            if ($start == ':') { // 调用函数
                $replace[] = '<?php echo (' . ltrim($matches[1][$i], ':') . '); ?>';
            } elseif ($start == '@') { // 输出常量
                $replace[] = '<?php echo (' . ltrim($matches[1][$i], '@') . '); ?>';
            } else { // 输出变量，自动拼接$
                if (strstr($matches[1][$i], '.')) {
                    $content = str_replace('.', '[\'', $matches[1][$i]) . '\']';
                } else {
                    $content = $matches[1][$i];
                }
                $replace[] = '<?php echo ($' . $content . '); ?>';
            }
        }
        $template = str_replace($search, $replace, $template);
        return $template;
    }

    /**
     * 外部加载扩展标签
     * @param $prefix
     * @param $className
     */
    public function loadTaglib($prefix, $className)
    {
        if ($prefix == 'default') {
            throw new Exception('扩展标签库前缀不能为default');
        }
        $this->libs[$prefix] = $className;
    }

    /**
     * 获取所有标签
     * @return null|string|string[]
     */
    private function getTags()
    {
        $tags = [];
        // 加入默认标签库
        $this->libs = array_merge(['default' => Tags::class,], $this->libs);
        foreach ($this->libs as $prefix => $lib) {
            $this->libInstance[$prefix] = $object = new $lib;
            foreach ($object->tags as $name => $tag) {
                if (!isset($tags[$name])) { // 如果不存在则加入到标签库
                    $tags[($prefix == 'default' ? '' : $prefix . ':') . $name] = $tag;
                }
            }
        }
        return $tags;
    }

    /**
     * 获取标签处理结果
     * @param $name
     * @param $attr
     * @param string $content
     * @return mixed
     */
    private function getTagParseResult($name, $attr, $content = '')
    {
        // 如果是扩展标签则找到扩展类进行处理
        if (strstr($name, ':')) {
            $tagInfo = explode(':', $name);
            if (method_exists($this->libInstance[$tagInfo[0]], '_' . $tagInfo[1])) {
                return $this->libInstance[$tagInfo[0]]->{'_' . $tagInfo[1]}($attr, $content);
            }
        }
        // 否则尝试默认标签处理
        else if (method_exists($this->libInstance['default'], '_' . $name)) {
            return $this->libInstance['default']->{'_' . $name}($attr, $content);
        }
    }

    /**
     * 进行标签处理
     * @param $template
     * @param $tags
     * @return null|string|string[]
     */
    private function parseTags($template)
    {
        foreach ($this->getTags() as $name => $item) {
            $pattern = '/' . $this->left . '(?:(' . $name . ')\b(?>[^' . $this->right . ']*)|\/(' . $name . '))';
            $pattern .= $this->right . '/is';
            if ($item['close']) {
                preg_match_all($pattern, $template, $matches, PREG_SET_ORDER | PREG_OFFSET_CAPTURE);
                $nodes = [];
                if (!empty($matches)) {
                    // 将匹配结果组合为成对数组
                    $start = [];
                    foreach ($matches as $match) {
                        // 为空则为结束标签
                        if ($match[1][0] == '') {
                            $tag = array_pop($start);
                            $nodes[$match[0][1]] = [
                                'name' => $name,
                                'start' => $tag[1],
                                'end' => $match[0][1],
                                'start_str' => $tag[0],
                                'end_str' => $match[0][0],
                            ];
                        } else {
                            $start[] = $match[0];
                        }
                    }
                    unset($matches, $start);
                    krsort($nodes);

                    if (!empty($nodes)) {
                        $nodes = array_merge($nodes, []);
                        $cut = '<!--CONTENT-->';
                        $startArray = [];
                        foreach ($nodes as $pos => $node) {
                            $attr = $item['attr'] ? $this->getAttr($node['start_str'], explode(',', $item['attr'])) : [];
                            // 得到准备替换的值
                            $replace = explode($cut, $this->getTagParseResult($name, $attr, $cut));
                            $replace = [
                                (isset($replace[0])) ? $replace[0] : [],
                                (isset($replace[1])) ? $replace[1] : [],
                            ];
                            while ($startArray) {
                                $begin = end($startArray);
                                // 如果当前结束位置大于最后一个开始标签的位置，则跳过，直接去替换这个结束标签
                                if ($node['end'] > $begin['start']) {
                                    break;
                                } else {
                                    // 否则先替换掉这个标签后面的所有开始标签
                                    $begin = array_pop($startArray);
                                    $template = substr_replace($template, $begin['string'], $begin['start'], $begin['length']);
                                }
                            }
                            $template = substr_replace($template, $replace[1], $node['end'], strlen($node['end_str']));
                            $startArray[] = [
                                'start' => $node['start'],
                                'length' => strlen($node['start_str']),
                                'string' => $replace[0]
                            ];
                        }
                        // 替换没有结束标签穿插的开始标签
                        while ($startArray) {
                            $begin = array_pop($startArray);
                            $template = substr_replace($template, $begin['string'], $begin['start'], $begin['length']);
                        }
                    }
                }
            } else { // 自闭合标签处理
                $template = preg_replace_callback($pattern, function ($matches) use ($name, $item) {
                    if (!isset($matches[2])) {
                        $attr = $item['attr'] ? $this->getAttr($matches[0], explode(',', $item['attr'])) : [];
                        return $this->getTagParseResult($name, $attr);
                    }
                    return $matches[0];
                }, $template);
            }
        }
        return preg_replace('/\?>([\r|\n|\s]*?)<\?php/is', '', $template);
    }

    /**
     * 获取属性
     * @param $string
     * @param array $tags
     * @return array
     */
    private function getAttr($string, $tags = [])
    {
        $attr = [];
        $attrPattern = '/\s+(.*?)=[\s\S]*?([\'"])(.*?)\\2/is';
        preg_match_all($attrPattern, $string, $result);
        if (isset($result[0]) && !empty($result[0])) {
            foreach ($result[1] as $key => $value) {
                $name = str_replace([' ', "\t", PHP_EOL], '', $value);
                if (in_array($name, $tags)) {
                    $attr[$name] = $result[3][$key];
                }
            }
        }
        return $attr;
    }

    /**
     * 处理original标签
     * @param $template
     * @return null|string|string[]
     */
    private function parseOriginal($template)
    {
        $pattern = '/' . $this->left . 'original' . $this->right . '([\s\S]*?)';
        $pattern .= $this->left . '\/original' . $this->right . '/is';
        return preg_replace_callback($pattern, function ($matches) {
            return str_replace([
                $this->left, $this->right,
                '{', '}'
            ], [
                '<!ORIGINAL--', '--ORIGINAL>',
                '<!PARAM--', '--PARAM>'
            ], $matches[1]);
        }, $template);
    }

    /**
     * 还原original内容
     * @param $template
     * @return mixed
     */
    public function returnOriginal($template)
    {
        return str_replace([
            '<!ORIGINAL--', '--ORIGINAL>',
            '<!PARAM--', '--PARAM>'
        ], [
            $this->left, $this->right,
            '{', '}'
        ], $template);
    }

    /**
     * 获取编译后的内容
     * @param $template
     * @return mixed|null|string|string[]
     */
    public function compile($template)
    {
        // 处理original标签
        $template = $this->parseOriginal($template);
        // 处理模板继承标签
        $template = $this->parseExtend($template);
        // 处理include标签
        $template = $this->parseInclude($template);
        // 处理变量以及函数
        $template = $this->parseVars($template);
        // 处理定义的标签
        $template = $this->parseTags($template);
        // 还原original内容
        $template = $this->returnOriginal($template);

        return '<?php if (!defined(\'APP_PATH\')) exit; ?>' . $template;
    }

}
