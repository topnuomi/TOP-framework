<?php

namespace top\extend\wechat;

/**
 * 微信API
 * Class WeChatAPI
 * @package top\extend\wechat
 * @author TOP糯米
 */
class WeChatAPI
{
    /**
     * @var null|string 当前页面的URL
     */
    private $url = null;

    /**
     * @var null 错误信息
     */
    private $error = null;

    /**
     * @var array 微信配置
     */
    private $config = [];

    /**
     * @var string 获取access_token的接口
     */
    private $accessTokenAPI = 'https://api.weixin.qq.com/cgi-bin/token?';

    /**
     * @var string 获取OAuth access_token的接口
     */
    private $oauthAccessTokenAPI = 'https://api.weixin.qq.com/sns/oauth2/access_token?';

    /**
     * @var string 获取CODE的接口
     */
    private $codeAPI = 'https://open.weixin.qq.com/connect/oauth2/authorize?';

    /**
     * @var string 拉取用户信息接口
     */
    private $userinfoAPI = 'https://api.weixin.qq.com/sns/userinfo?';

    /**
     * @var string 拉取用户信息接口（UnionID机制）
     */
    private $userinfoUnionIdAPI = 'https://api.weixin.qq.com/cgi-bin/user/info?';

    /**
     * @var string 自定义菜单创建接口
     */
    private $menuAPI = 'https://api.weixin.qq.com/cgi-bin/menu';

    /**
     * @var string 获取自定义菜单配置接口
     */
    private $currentSelfmenuAPI = 'https://api.weixin.qq.com/cgi-bin/get_current_selfmenu_info?';

    /**
     * @var string 模版消息接口
     */
    private $templateAPI = 'https://api.weixin.qq.com/cgi-bin/template/';

    /**
     * @var string 语言
     */
    private $lang = null;

    public function __construct($config = [], $lang = null)
    {
        $this->url = $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['SERVER_NAME'] . $_SERVER['REQUEST_URI'];
        $this->config = $config;
        $this->lang = $lang ? $lang : 'zh_CN';
    }

    /**
     * 获取access_token
     * @return mixed
     * @throws WeChatAPIException
     */
    private function getAccessToken()
    {
        $file = './access_token.json';
        if (file_exists($file)) {
            $content = file_get_contents($file);
            $result = json_decode($content, true);
            $expires = $result['expires_in'] - (time() - filemtime($file));
            if ($expires <= 5) {
                @unlink($file);
                return $this->getAccessToken();
            }
            return $result;
        } else {
            $api = $this->accessTokenAPI . "grant_type=client_credential&appid={$this->config['appid']}";
            $api .= "&secret={$this->config['appsecret']}";
            $result = $this->createHttpRequest($api, null, true);
            file_put_contents($file, $result[0]);
            return $result[1];
        }
    }

    /**
     * 获取CODE
     * @param $scope
     */
    private function getCode($scope)
    {
        $redirect = $this->url;
        $api = $this->codeAPI . "appid={$this->config['appid']}&redirect_uri={$redirect}";
        $api .= "&response_type=code&scope={$scope}&state=0#wechat_redirect";
        exit(header('location:' . $api));
    }

    /**
     * 获取OAuth的access_token
     * @param string $scope
     * @return mixed
     * @throws WeChatAPIException
     */
    private function getOAuthAccessToken($scope = 'snsapi_base')
    {
        if (isset($_SESSION['oauth_access_token']) && !empty($_SESSION['oauth_access_token'])) {
            $session = $_SESSION['oauth_access_token'];
            $content = $session['content'];
            $result = json_decode($content, true);
            $expires = $result['expires_in'] - (time() - $session['time']);
            if ($expires <= 5) {
                unset($_SESSION['oauth_access_token']);
                return $this->getOAuthAccessToken();
            }
            return $result;
        } else {
            $code = isset($_GET['code']) ? $_GET['code'] : null;
            if (!$code) {
                $this->getCode($scope);
            }
            $api = $this->oauthAccessTokenAPI . "appid={$this->config['appid']}";
            $api .= "&secret={$this->config['appsecret']}&code={$code}&grant_type=authorization_code";
            $result = $this->createHttpRequest($api, null, true);
            $_SESSION['oauth_access_token'] = [
                'time' => time(),
                'content' => $result[0]
            ];
            return $result[1];
        }
    }

    /**
     * 拉取用户信息
     * @param null $openid
     * @return bool|mixed
     * @throws WeChatAPIException
     */
    public function getUserInfo($openid = null)
    {
        $postData = [];
        if ($openid) {
            $accessToken = $this->getAccessToken();
            $api = $this->userinfoUnionIdAPI . "access_token={$accessToken['access_token']}";
            if (is_array($openid)) {
                $postData = json_encode([
                    'user_list' => $openid
                ]);
            } else {
                $api .= "&openid={$openid}&lang={$this->lang}";
            }
        } else {
            $accessToken = $this->getOAuthAccessToken('snsapi_userinfo');
            $api = $this->userinfoAPI . "access_token={$accessToken['access_token']}";
            $api .= "&openid={$accessToken['openid']}&lang={$this->lang}";
        }
        $result = $this->createHttpRequest($api, $postData);
        return $result;
    }

    /**
     * 创建公众号菜单
     * $menu数据示例
     * [
     *     [
     *         'type' => 'view',
     *         'name' => 'TOP糯米',
     *         'url' => 'https://www.topnuomi.com/'
     *     ],
     *     [
     *         'name' => '测试多级',
     *         'sub_button' => [
     *             [
     *                 'type' => 'view',
     *                 'name' => '我的主页',
     *                 'url' => 'https://topnuomi.com/'
     *             ],
     *             [
     *                 'type' => 'click',
     *                 'name' => '点击',
     *                 'key' => 'V1001_TODAY_MUSIC'
     *             ]
     *         ]
     *     ]
     * ]
     * @param array $menu
     * @param array $matchrule
     * @return bool
     * @throws WeChatAPIException
     */
    public function createMenu($menu = [], $matchrule = [])
    {
        // 公众号菜单
        $menu = [
            'button' => $menu
        ];
        // 个性化菜单
        $matchrule = (!empty($matchrule)) ? [
            'matchrule' => $matchrule
        ] : [];
        $menu = array_merge($menu, $matchrule);
        $menuJson = json_encode($menu, JSON_UNESCAPED_UNICODE);
        $type = (!empty($matchrule)) ? 'addconditional' : 'create';
        return $this->menuAction($type, $menuJson);
    }

    /**
     * 获取公众号菜单
     * @return mixed
     * @throws WeChatAPIException
     */
    public function getMenu()
    {
        return $this->menuAction('get');
    }

    /**
     * 删除公众号自定义菜单
     * @return mixed
     * @throws WeChatAPIException
     */
    public function deleteMenu($menuid = null)
    {
        $json = null;
        if ($menuid) {
            $json = json_encode([
                'menuid' => $menuid
            ]);
        }
        $type = ($menuid) ? 'delconditional' : 'delete';
        return $this->menuAction($type, $json, $menuid);
    }

    /**
     * 公众号菜单的基础操作
     * @param null $type
     * @param null $postData
     * @return bool|mixed
     * @throws WeChatAPIException
     */
    private function menuAction($type = null, $postData = null, $menuid = null)
    {
        $typePool = ['create', 'get', 'delete', 'addconditional', 'delconditional'];
        if (in_array($type, $typePool)) {
            $accessToken = $this->getAccessToken();
            $api = $this->menuAPI . "/{$type}?access_token={$accessToken['access_token']}";
            $result = $this->createHttpRequest($api, $postData);
            return ($type == 'get' || $type == 'addconditional') ? $result : true;
        } else {
            throw new WeChatAPIException('对公众号菜单的操作不在允许列表');
        }
    }

    /**
     * 获取自定义菜单配置
     * @return mixed
     * @throws WeChatAPIException
     */
    public function getCurrentSelfmenuInfo()
    {
        $accessToken = $this->getAccessToken();
        $api = $this->currentSelfmenuAPI . "access_token={$accessToken['access_token']}";
        $result = $this->createHttpRequest($api);
        return $result;
    }

    /**
     * 设置所属行业
     * post数据示例
     * {
     *     "industry_id1":"1",
     *     "industry_id2":"4"
     * }
     * @param $id1
     * @param $id2
     * @return bool
     * @throws WeChatAPIException
     */
    public function setIndustry($id1, $id2)
    {
        $accessToken = $this->getAccessToken();
        $api = $this->templateAPI . "api_set_industry?access_token={$accessToken['access_token']}";
        $postData = json_encode([
            'industry_id1' => $id1,
            'industry_id2' => $id2
        ]);
        $this->createHttpRequest($api, $postData);
        return true;
    }

    /**
     * 获取设置的行业信息
     * @return mixed
     */
    public function getIndustry()
    {
        $accessToken = $this->getAccessToken();
        $api = $this->templateAPI . "get_industry?access_token={$accessToken['access_token']}";
        $result = $this->createHttpRequest($api);
        return $result;
    }

    /**
     * 获取模板列表
     * @return mixed
     */
    public function getTemplateList()
    {
        $accessToken = $this->getAccessToken();
        $api = $this->templateAPI . "get_all_private_template?access_token={$accessToken['access_token']}";
        $result = $this->createHttpRequest($api);
        return $result;
    }

    /**
     * 创建HTTP请求
     * @param $api
     * @param array $data
     * @param bool $includeJson
     * @return mixed
     */
    private function createHttpRequest($api, $data = [], $includeJson = false)
    {
        $json = create_http_request($api, $data);
        $result = json_decode($json, true);
        if (isset($result['errcode']) && $result['errcode'] != 0) {
            throw new WeChatAPIException("code:{$result['errcode']}, {$result['errmsg']}");
        }
        return ($includeJson) ? [$json, $result] : $result;
    }

    /**
     * 获取错误信息
     * @return null
     */
    public function getError()
    {
        return $this->error;
    }

}
