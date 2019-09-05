<?php

/**
 * 自动创建模块类
 * Class Create
 */
class Create
{

    /**
     * 模块名
     * @var string
     */
    private $name = '';

    /**
     * 命名空间
     * @var string
     */
    private $namespace = '';

    /**
     * 入口文件名
     * @var string
     */
    private $start = '';

    /**
     * 默认项目根目录
     * @var string
     */
    private $base = '';

    /**
     * 当前目录
     * @var string
     */
    private $dir = '';

    /**
     * 模块目录
     * @var string
     */
    private $projectPath;

    public function __construct($start, $namespace, $name)
    {
        $this->name = $name;
        $this->dir = __DIR__ . '/';
        $this->namespace = $namespace;
        $this->base = $this->dir . '../../';
        if ($start)
            $this->start = $this->base . $start . '.php';
        $this->projectPath = $this->base . $this->namespace . '/' . $this->name . '/';
        $this->create();
    }

    /**
     * 替换内容
     * @param $content
     * @return mixed
     */
    public function replaceContent($content)
    {
        return str_replace([
            '{namespace}',
            '{name}'
        ], [
            $this->namespace,
            $this->name
        ], $content);
    }

    /**
     * 创建入口文件
     * @return bool
     */
    public function createStartFile()
    {
        if ($this->start && !is_file($this->start)) {
            $content = file_get_contents($this->dir . 'tpl/index.tpl');
            $content = $this->replaceContent($content);
            if (file_put_contents($this->start, $content)) {
                return true;
            }
            exit('error -1');
        }
        return true;
    }

    /**
     * 创建配置文件
     */
    public function createConfig()
    {
        $configPath = $this->projectPath . 'config/';
        $configFile = $configPath . 'config.php';
        if (!is_dir($configPath)) {
            mkdir($configPath, 0755, true);
        }
        if (!is_file($configFile)) {
            $content = file_get_contents($this->dir . 'tpl/config/config.tpl');
            $content = $this->replaceContent($content);
            $realConfigFile = $this->base . '/' . $this->namespace . '/' . $this->name . '/config/config.php';
            if (!file_put_contents($configPath . 'config.php', $content)) {
                exit('error -2');
            }
        }
    }

    /**
     * 创建MVC目录及文件
     */
    public function createMVC()
    {
        $dirArray = [
            'controller',
            'model',
            'view'
        ];
        for ($i = 0; $i < count($dirArray); $i++) {
            if (!is_dir($this->projectPath . $dirArray[$i] . '/')) {
                mkdir($this->projectPath . $dirArray[$i] . '/', 0755, true);
            }
        }
        $controllerFile = $this->projectPath . 'controller/index.php';
        if (!is_file($controllerFile)) {
            $content = file_get_contents($this->dir . 'tpl/controller/index.tpl');
            $content = $this->replaceContent($content);
            if (!file_put_contents($this->projectPath . 'controller/Index.php', $content)) {
                exit('error -4');
            }
        }
        $modelFile = $this->projectPath . 'model/demo.php';
        if (!is_file($modelFile)) {
            $content = file_get_contents($this->dir . 'tpl/model/demo.tpl');
            $content = $this->replaceContent($content);
            if (!file_put_contents($this->projectPath . 'model/Demo.php', $content)) {
                exit('error -5');
            }
        }
        $viewFile = $this->projectPath . 'view/index/index.html';
        if (!is_file($viewFile)) {
            $content = file_get_contents($this->dir . 'tpl/view/index.tpl');
            if (!is_dir($this->projectPath . 'view/Index/')) {
                mkdir($this->projectPath . 'view/Index/', 0755, true);
            }
            if (!file_put_contents($this->projectPath . 'view/Index/index.html', $content)) {
                exit('error -6');
            }
        }
    }

    /**
     * 创建函数库文件
     */
    public function createFunctions()
    {
        $file = $this->projectPath . 'functions.php';
        if (!is_file($file)) {
            if (!file_put_contents($file, "<?php\r\n")) {
                exit('-7');
            }
        }
    }

    /**
     * 创建路由文件
     */
    public function createRoute()
    {
        $file = $this->projectPath . '../route.php';
        if (!is_file($file)) {
            if (!file_put_contents($file, file_get_contents($this->dir . 'tpl/route.tpl'))) {
                exit('-8');
            }
        }
    }

    /**
     * 执行创建操作
     */
    public function create()
    {
        $this->createStartFile();
        $this->createConfig();
        $this->createMVC();
        $this->createFunctions();
        $this->createRoute();
    }
}

// 准备创建项目
$namespace = (isset($argv[1]) && $argv[1]) ? $argv[1] : exit('please type namespace~');
$projectName = (isset($argv[2]) && $argv[2]) ? $argv[2] : exit('please type project name~');
$startFile = (isset($argv[3]) && $argv[3]) ? $argv[3] : false;
new Create($startFile, $namespace, $projectName);
