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
     * 应用目录
     * @var string
     */
    private $path = '';

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

    public function __construct($start, $path, $name)
    {
        $this->name = $name;
        $this->dir = __DIR__ . DIRECTORY_SEPARATOR;
        $this->path = $path;
        $this->base = $this->dir . '..' . DIRECTORY_SEPARATOR . '..' . DIRECTORY_SEPARATOR;
        if ($start)
            $this->start = $this->base . $start;
        $this->projectPath = $this->base . $this->path . DIRECTORY_SEPARATOR . $this->name . DIRECTORY_SEPARATOR;
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
            '{name}'
        ], [
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
            $content = file_get_contents($this->dir . 'tpl' . DIRECTORY_SEPARATOR . 'index.tpl');
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
        $configPath = $this->projectPath . 'config' . DIRECTORY_SEPARATOR;
        $configFile = $configPath . 'config.php';
        if (!is_dir($configPath)) {
            mkdir($configPath, 0755, true);
        }
        if (!is_file($configFile)) {
            $content = file_get_contents($this->dir . 'tpl' . DIRECTORY_SEPARATOR . 'config' . DIRECTORY_SEPARATOR . 'config.tpl');
            $content = $this->replaceContent($content);
            if (!file_put_contents($configPath . 'config.php', $content)) {
                exit('error -2');
            }
        }
    }

    /**
     * 创建MVC目录及文件
     */
    public function createControllerAndView()
    {
        $dirArray = [
            'controller',
            'model',
            'view'
        ];
        for ($i = 0; $i < count($dirArray); $i++) {
            if (!is_dir($this->projectPath . $dirArray[$i] . DIRECTORY_SEPARATOR)) {
                mkdir($this->projectPath . $dirArray[$i] . DIRECTORY_SEPARATOR, 0755, true);
            }
        }
        $controllerFile = $this->projectPath . 'controller' . DIRECTORY_SEPARATOR . 'index.php';
        if (!is_file($controllerFile)) {
            $content = file_get_contents($this->dir . 'tpl' . DIRECTORY_SEPARATOR . 'controller' . DIRECTORY_SEPARATOR . 'index.tpl');
            $content = $this->replaceContent($content);
            if (!file_put_contents($this->projectPath . 'controller' . DIRECTORY_SEPARATOR . 'Index.php', $content)) {
                exit('error -4');
            }
        }
        $viewFile = $this->projectPath . 'view' . DIRECTORY_SEPARATOR . 'index' . DIRECTORY_SEPARATOR . 'index.html';
        if (!is_file($viewFile)) {
            $content = file_get_contents($this->dir . 'tpl' . DIRECTORY_SEPARATOR . 'view' . DIRECTORY_SEPARATOR . 'index.tpl');
            if (!is_dir($this->projectPath . 'view' . DIRECTORY_SEPARATOR . 'Index' . DIRECTORY_SEPARATOR)) {
                mkdir($this->projectPath . 'view' . DIRECTORY_SEPARATOR . 'Index' . DIRECTORY_SEPARATOR, 0755, true);
            }
            if (!file_put_contents($this->projectPath . 'view' . DIRECTORY_SEPARATOR . 'Index' . DIRECTORY_SEPARATOR . 'index.html', $content)) {
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
     * 执行创建操作
     */
    public function create()
    {
        $this->createStartFile();
        $this->createConfig();
        $this->createControllerAndView();
        $this->createFunctions();
    }
}

// 准备创建项目
$path = (isset($argv[1]) && $argv[1]) ? $argv[1] : exit('please type path~');
$projectName = (isset($argv[2]) && $argv[2]) ? $argv[2] : exit('please type project name~');
$startFile = (isset($argv[3]) && $argv[3]) ? $argv[3] : false;
new Create($startFile, $path, $projectName);
