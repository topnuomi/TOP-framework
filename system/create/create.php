<?php

class Create {

    private $name;

    private $start;

    private $namespace;

    private $base;

    private $dir;

    private $projectPath;

    private $isNewProject;

    public function __construct($start, $namespace, $name, $new) {
        $this->name = $name;
        $this->dir = __DIR__ . '/';
        $this->namespace = $namespace;
        $this->base = $this->dir . '../../';
        $this->start = $this->base . $start . '.php';
        $this->projectPath = $this->base . $this->namespace . '/' . $this->name . '/';
        $this->isNewProject = $new;
        $this->create();
    }

    public function replaceContent($content) {
        return str_replace([
            '{namespace}',
            '{name}'
        ], [
            $this->namespace,
            $this->name
        ], $content);
    }

    public function createStartFile() {
        if (! file_exists($this->start) && $this->isNewProject == 'new') {
            $content = file_get_contents($this->dir . 'tpl/index.tpl');
            $content = $this->replaceContent($content);
            if (file_put_contents($this->start, $content)) {
                return true;
            }
            exit('error -1');
        }
        return true;
    }

    public function createConfig() {
        $configPath = $this->projectPath . 'config/';
        $configFile = $configPath . 'config.php';
        $tagsFile = $configPath . 'tags.php';
        if (! is_dir($configPath)) {
            mkdir($configPath, 0777, true);
        }
        if (! file_exists($configFile)) {
            $content = file_get_contents($this->dir . 'tpl/config/config.tpl');
            $content = $this->replaceContent($content);
            $realConfigFile = $this->base . '/' . $this->namespace . '/' . $this->name . '/config/config.php';
            if (! file_put_contents($configPath . 'config.php', $content)) {
                exit('error -2');
            }
        }
        if (! file_exists($tagsFile)) {
            $content = file_get_contents($this->dir . 'tpl/config/tags.tpl');
            if (! file_put_contents($configPath . 'tags.php', $content)) {
                exit('error -3');
            }
        }
        return true;
    }

    public function createMVC() {
        $dirArray = [
            'controller',
            'model',
            'view'
        ];
        for ($i = 0; $i < count($dirArray); $i ++) {
            if (! is_dir($this->projectPath . $dirArray[$i] . '/')) {
                mkdir($this->projectPath . $dirArray[$i] . '/', 0777, true);
            }
        }
        $controllerFile = $this->projectPath . 'controller/index.php';
        if (! file_exists($controllerFile)) {
            $content = file_get_contents($this->dir . 'tpl/controller/index.tpl');
            $content = $this->replaceContent($content);
            if (! file_put_contents($this->projectPath . 'controller/index.php', $content)) {
                exit('error -4');
            }
        }
        $modelFile = $this->projectPath . 'model/demo.php';
        if (! file_exists($modelFile)) {
            $content = file_get_contents($this->dir . 'tpl/model/demo.tpl');
            $content = $this->replaceContent($content);
            if (! file_put_contents($this->projectPath . 'model/demo.php', $content)) {
                exit('error -5');
            }
        }
        $viewFile = $this->projectPath . 'view/index/index.html';
        if (! file_exists($viewFile)) {
            $content = file_get_contents($this->dir . 'tpl/view/index.tpl');
            if (! is_dir($this->projectPath . 'view/index/')) {
                mkdir($this->projectPath . 'view/index/', 0777, true);
            }
            if (! file_put_contents($this->projectPath . 'view/index/index.html', $content)) {
                exit('error -6');
            }
        }
    }

    public function createFunctions() {
        $file = $this->projectPath . 'functions.php';
        if (! file_exists($file)) {
            if (! file_put_contents($file, '<?php ')) {
                exit('-7');
            }
        }
    }

    public function createRoute() {
        $file = $this->projectPath . '../route.php';
        if (! file_exists($file)) {
            if (! file_put_contents($file, "<?php \r\nreturn [];")) {
                exit('-8');
            }
        }
    }

    public function create() {
        $this->createStartFile();
        $this->createConfig();
        $this->createMVC();
        $this->createFunctions();
        $this->createRoute();
    }
}

// 准备创建项目
$startFile = (isset($argv[1]) && $argv[1]) ? $argv[1] : exit('please type filename~');
$namespace = (isset($argv[2]) && $argv[2]) ? $argv[2] : exit('please type namespace~');
$projectName = (isset($argv[3]) && $argv[3]) ? $argv[3] : exit('please type project name~');
$isNew = (isset($argv[4]) && $argv[4]) ? $argv[4] : 'not new';
new Create($startFile, $namespace, $projectName, $isNew);