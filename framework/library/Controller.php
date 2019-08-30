<?php

namespace top\library;

/**
 * 基础控制器
 * @author topnuomi 2018年11月23日
 */
abstract class Controller
{

    public function __construct()
    {
    }

    /**
     * 输出JSON数据
     * @param $msg
     * @param int $code
     * @param array $data
     * @param array $ext
     * @return false|string
     */
    protected function json($msg, $code = 1, $data = [], $ext = [])
    {
        $array = [
            'msg' => $msg,
            'code' => $code,
            'data' => $data,
            'ext' => $ext
        ];
        return json_encode($array);
    }

    /**
     * 缓存页面（具体视图驱动完成此功能）
     * @param bool $param
     * @return $this
     */
    protected function cache($param = true)
    {
        View::instance()->cache($param);
        return $this;
    }

    /**
     * 赋值到视图
     * @param $name
     * @param $value
     */
    protected function param($name, $value)
    {
        View::instance()->param($name, $value);
    }

    /**
     * 渲染视图
     * @param string $file
     * @param array $param
     * @param bool $cache
     * @return mixed
     */
    protected function view($file = '', $param = [], $cache = false)
    {
        return View::instance()->fetch($file, $param, $cache);
    }

    /**
     * 跳转（非ajax）
     * @param $url
     */
    protected function redirect($url)
    {
        return redirect($url);
    }

    /**
     * 显示提示页面
     * @param $message
     * @param string $url
     * @param int $sec
     * @return false|mixed|string
     */
    protected function tips($message, $url = '', $sec = 3)
    {
        if (request()->isAjax()) {
            return $this->json($message, '', 'tips', ['url' => $url, 'sec' => $sec]);
        } else {
            $viewConfig = Config::instance()->get('view');
            $tipsTemplate = $viewConfig['dir'] . 'tips.' . $viewConfig['ext'];
            (!file_exists($tipsTemplate)) && file_put_contents($tipsTemplate, '');
            return $this->view('tips', [
                'message' => $message,
                'url' => $url,
                'sec' => $sec
            ]);
        }
    }
}
