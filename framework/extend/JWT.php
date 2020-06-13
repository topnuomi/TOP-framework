<?php

namespace top\extend;

use top\library\Config;

class JWT
{

    /**
     * JWT配置
     * @var array
     */
    private $config = [];

    /**
     * 加密key
     * @var null
     */
    private $key = null;

    /**
     * Token有效期
     * @var int
     */
    private $exp = 30;

    /**
     * 附加数据
     * @var array
     */
    private $data = [];

    public function __construct()
    {
        $this->config = \config('jwt');
        if (isset($this->config['key'])) {
            $this->key = $this->config['key'];
        } else {
            throw new \Exception('加密key参数必须');
        }
    }

    /**
     * 设置附加数据
     * @param $key
     * @param $value
     * @return $this
     */
    public function setData($key, $value)
    {
        $this->data[$key] = $value;
        return $this;
    }

    /**
     * 设置Token有效期
     * @param $sec
     * @return $this
     */
    public function exp($sec)
    {
        $this->exp = $sec;
        return $this;
    }

    /**
     * 获取Token
     * @return string
     */
    public function token()
    {
        $time = time();
        $data = array_merge([
            'iss' => $this->config['iss'],
            'aud' => $this->config['aud'],
            'iat' => $time,
            'nbf' => $time,
            'exp' => $time + $this->exp,
        ], $this->data);
        return $token = \Firebase\JWT\JWT::encode($data, $this->key, 'HS256');
    }

    /**
     * 解析Token
     * @param $jwt
     * @return object
     */
    public function decode($jwt)
    {
        return $result = \Firebase\JWT\JWT::decode($jwt, $this->key, ['HS256']);
    }
}
