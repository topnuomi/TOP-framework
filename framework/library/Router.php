<?php

namespace top\library;

use top\library\exception\RouteException;
use top\library\http\Request;
use top\middleware\ifs\MiddlewareIfs;
use top\traits\Instance;

/**
 * 路由类
 * @author topnuomi 2018年11月19日
 */
class Router
{

    use Instance;

    /**
     * 路由配置
     * @var array
     */
    private $config = [];

    /**
     * 请求类
     * @var Request
     */
    private $request = null;

    /**
     * 类全限定名
     * @var null
     */
    private $controllerFullName = null;

    /**
     * 类名
     * @var null
     */
    private $controller = null;

    /**
     * 方法
     * @var null
     */
    private $method = null;

    /**
     * 参数
     * @var array
     */
    private $params = [];

    /**
     * 当前加载的路由
     * @var null
     */
    private $loadRuleParameters = null;

    /**
     * Router constructor.
     * @param Request $request
     */
    private function __construct(Request $request)
    {
        $this->request = $request;
    }

    /**
     * 完整控制器名
     * @return mixed
     */
    public function controllerFullName()
    {
        return $this->controllerFullName;
    }

    /**
     * 控制器名
     * @return mixed
     */
    public function controller()
    {
        return $this->controller;
    }

    /**
     * 模块名
     * @return mixed
     */
    public function module()
    {
        return BIND_MODULE;
    }

    /**
     * 方法名
     * @return mixed
     */
    public function method()
    {
        return $this->method;
    }

    /**
     * 请求参数
     * @return mixed
     */
    public function params()
    {
        return $this->params;
    }

    /**
     * 查找最适合的路由匹配
     * @param $rules
     * @param $uri
     * @return mixed
     */
    private function findRule($rules, $uri)
    {
        $result = [];
        if ($uri == '/' && isset($rules['/'])) {
            $result[] = '/';
        } else {
            // 如果不是首页则unset掉首页的规则，避免参与计算导致出错
            unset($rules['/']);
            $keys = array_keys($rules);
            foreach ($keys as $key) {
                $pos = strpos($uri, $key);
                if ($pos !== false) {
                    $endPos = $pos + strlen($key);
                    $result[$endPos] = $key;
                }
            }
        }
        if (!empty($result)) {
            $max = max(array_keys($result));
            $rest = str_replace($result[$max], '', $uri);
            if (($result[$max] == '/' && $uri != '/') || ($rest != '' && substr($rest, 0, 1) != '/')) {
                return false;
            } else {
                $this->loadRuleParameters = $rules[$result[$max]];
                return [
                    'rule' => $result[$max],
                    'parameters' => $this->loadRuleParameters,
                ];
            }
        }
        return false;
    }

    /**
     * 解析路由规则
     * @param $requestMethod
     * @param $uri
     * @return array|bool|mixed
     */
    private function parseRouteRule($requestMethod, $uri)
    {
        // 获取所有路由配置（可能从缓存文件读取）
        $routeConfig = $this->getRouteConfig();

        $rule = [];
        if (isset($routeConfig[$requestMethod])) { // 第一次去当前请求方法中查找
            $rule = $this->findRule($routeConfig[$requestMethod], $uri);
        }
        if (empty($rule) && isset($routeConfig['any'])) { // 全部中查找
            $rule = $this->findRule($routeConfig['any'], $uri);
        }

        return (!empty($rule)) ? $rule : false;
    }

    /**
     * 普通路由处理
     * @param $uri
     * @return array
     */
    private function parseRoute($uri)
    {
        // 普通处理
        $uriArray = explode('/', trim($uri, '/'));
        $uriArray[0] = (isset($uriArray[0]) && $uriArray[0]) ? $uriArray[0] : config('default_controller');
        $uriArray[1] = (isset($uriArray[1]) && $uriArray[1]) ? $uriArray[1] : config('default_method');

        $controller = ucfirst($uriArray[0]);
        $rule['class'] = APP_NS . '\\' . BIND_MODULE . '\\controller\\' . $controller;
        $rule['method'] = $uriArray[1];

        return [
            'rule' => $uriArray[0] . '/' . $uriArray[1],
            'parameters' => $rule,
        ];
    }

    /**
     * 解析请求参数
     * @param $class
     * @param $method
     * @param $prefix
     * @param $uri
     * @return array
     */
    private function parseParameters($class, $method, $prefix, $uri)
    {
        $paramsString = ltrim(substr_replace($uri, '', 0, strlen($prefix)), '/');
        $paramsArray = explode('/', $paramsString);

        $params = [];
        if (config('complete_parameter')) { // 如果开启了完全参数名
            for ($i = 0; $i < count($paramsArray); $i += 2) {
                if (isset($paramsArray[$i + 1])) {
                    $_GET[$paramsArray[$i]] = $params[$paramsArray[$i]] = $paramsArray[$i + 1];
                }
            }
        } else { // 未开启完全参数名，则利用反射得到参数名做映射
            if (!empty($paramsArray)) {
                $reflectionMethod = Application::getReflectionMethod($class, $method);
                $index = 0;
                foreach ($reflectionMethod->getParameters() as $parameter) {
                    $className = $parameter->getClass();
                    if (is_null($className)) {
                        $_GET[$parameter->name] = $params[$parameter->name] = $paramsArray[$index];
                        $index++;
                    }
                }
            }
        }

        return $params;
    }

    /**
     * 执行应用
     * @return mixed
     * @throws RouteException
     */
    public function execute()
    {
        try {
            // 处理路由
            $this->handler($this->request->uri());
        } catch (RouteException $exception) {
            if (!DEBUG) { // 非调试模式直接404
                return \response()->code(404)->send();
            } else throw $exception;
        }

        // 路由中间件处理
        return $this->middleware(function () {
            return Application::callMethod($this->controllerFullName, $this->method, $this->params);
        });
    }

    /**
     * 中间件处理
     * @param \Closure $application
     * @return mixed
     */
    public function middleware(\Closure $application)
    {
        // 加载全局配置文件中配置的中间件
        $middleware = array_reverse(config('middleware'));

        // 配置中不执行的中间件
        $exceptMiddlewareArray = [];
        if (isset($this->loadRuleParameters['except_middleware'])
            && $this->loadRuleParameters['except_middleware'] != ''
        ) {
            $exceptMiddlewareArray = $this->loadRuleParameters['except_middleware'];
        }

        // 配置中新增的中间件
        if (isset($this->loadRuleParameters['accept_middleware'])
            && $this->loadRuleParameters['accept_middleware'] != ''
        ) {
            $acceptMiddlewareArray = $this->loadRuleParameters['accept_middleware'];
            foreach ($acceptMiddlewareArray as $acceptMiddleware) {
                if (!in_array($acceptMiddleware, $middleware)) {
                    $middleware[] = $acceptMiddleware;
                } else continue;
            }
        }
        
        // 应用打包在在洋葱圈最里层
        $next = $application;
        foreach ($middleware as $value) {
            if (!in_array($value, $exceptMiddlewareArray)) {
                $next = function () use ($next, $value) {
                    $middleware = new $value;
                    if ($middleware instanceof MiddlewareIfs) {
                        return $middleware->handler($this->request, $next);
                    } else throw new RouteException('无效的中间件：' . $value);
                };
            }
        }

        return $next();
    }

    /**
     * 处理URI
     * @param $uri
     * @throws RouteException
     */
    public function handler($uri)
    {
        $uri = $uri ? $uri : '/';
        $defaultMethod = config('default_method');
        $requestMethod = strtolower($this->request->requestMethod());
        // 第一次用原始uri去做匹配，第二次带上默认方法去做匹配
        if (false === ($rule = $this->parseRouteRule($requestMethod, $uri))
            && false === ($rule = $this->parseRouteRule($requestMethod, $uri . '/' . $defaultMethod))
        ) {
            // 如果开启强制路由，则抛异常
            if (config('compel_route') === true) {
                throw new RouteException('不支持的路由规则：' . strtoupper($requestMethod) . ' ' . $uri);
            } else {
                // 进行普通处理
                $rule = $this->parseRoute($uri);
            }
        }
        $ruleParameters = $rule['parameters'];

        $this->controllerFullName = $ruleParameters['class'];
        $this->controller = substr($this->controllerFullName, strrpos($ruleParameters['class'], '\\') + 1);
        $this->method = $ruleParameters['method'];
        // 此处还需要检查控制器和方法是否存在
        if (!class_exists($this->controllerFullName)) {
            throw new RouteException('不存在的控制器：' . $this->controllerFullName);
        }
        if (!method_exists($this->controllerFullName, $this->method)) {
            throw new RouteException('不存在的方法：' . $this->method);
        }
        $this->params = $this->parseParameters($ruleParameters['class'], $ruleParameters['method'], $rule['rule'], $uri);
    }


    /**
     * 创建路由配置缓存文件
     * @return array|mixed
     */
    public function getRouteConfig()
    {
        $fileName = './runtime/' . BIND_MODULE . '_route_cache.php';
        if (!DEBUG && is_file($fileName)) {
            return require $fileName;
        } else {
            $result = [];
            $controllerPath = APP_PATH . BIND_MODULE . '/controller/';
            $namespace = APP_NS . '\\' . BIND_MODULE . '\\controller';
            $files = scandir($controllerPath);
            for ($i = 2; $i < count($files); $i++) {
                $className = $namespace . '\\' . pathinfo($files[$i])['filename'];
                $reflectionClass = Application::getReflectionClass($className);
                foreach ($reflectionClass->getMethods() as $method) {
                    if ($method->class == $className && substr($method->name, 0, 1) != '_') {
                        $annotation = Annotation::getMethodAnnotation($className, $method->name);
                        $requestMethod = (isset($annotation['requestMethod'])) ? $annotation['requestMethod'] : 'any';
                        if (isset($annotation['route'])) {
                            $requestUri = $annotation['route'];
                        } else continue;
                        $requestMethod = strtolower($requestMethod);
                        $rule = ($requestUri == '/') ? $requestUri : trim($requestUri, '/');
                        $result[$requestMethod][$rule] = [
                            'class' => $className,
                            'method' => $method->name,
                            'except_middleware' => [],
                            'accept_middleware' => [],
                        ];
                        if (isset($annotation['exceptMiddleware']) && $annotation['exceptMiddleware'] != '') {
                            foreach (explode('|', $annotation['exceptMiddleware']) as $exceptMiddleware) {
                                $result[$requestMethod][$rule]['except_middleware'][] = $exceptMiddleware;
                            }
                        }
                        if (isset($annotation['acceptMiddleware']) && $annotation['acceptMiddleware'] != '') {
                            foreach (explode('|', $annotation['acceptMiddleware']) as $acceptMiddleware) {
                                $result[$requestMethod][$rule]['accept_middleware'][] = $acceptMiddleware;
                            }
                        }
                    }
                }
            }

            // 加载配置文件中的路由配置
            $routeConfigFile = CONFIG_DIR . 'route.php';
            if (is_file($routeConfigFile)) {
                $routeConfig = require $routeConfigFile;
                foreach ($routeConfig as $key => $value) {
                    if (isset($result[$key])) { // 存在当前请求方法的配置就检查含有的路由配置
                        foreach ($value as $uri => $config) {
                            $uri = ($uri == '/') ? $uri : trim($uri, '/');
                            if (isset($result[$key][$uri])) { // 如果已经存在这个路由配置，可能不完全，直接合并覆盖已有项
                                $result[$key][$uri] = array_merge($result[$key][$uri], $config);
                            } else {
                                $result[$key][$uri] = $config;
                            }
                        }
                    } else {
                        $result[$key] = $value;
                    }
                }
            }

            // 写入文件
            ob_start();
            var_export($result);
            $content = ob_get_contents();
            ob_clean();
            file_put_contents($fileName, "<?php\nreturn " . $content . ';');
            return $result;
        }
    }

}
