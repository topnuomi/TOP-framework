<?php

namespace top\middleware;

use top\library\cache\driver\File;
use top\library\Config;
use top\library\http\Request;
use top\library\http\Response;
use top\middleware\ifs\MiddlewareIfs;

/**
 * 检查是否存在静态缓存
 * Class View
 * @package top\middleware
 */
class View implements MiddlewareIfs
{

    public function handler(Request $request, \Closure $next)
    {
        // 非调试模式则直接返回静态缓存
        if (!DEBUG) {
            $ident = view_cache_ident();
            $config = Config::instance()->get('view');
            (!$config['cacheDir']) && $config['cacheDir'] = './runtime/cache/application/' . request()->module() . '/';
            $cache = File::instance($config['cacheDir']);
            if ($cache->exists($ident)) return Response::instance()->send($cache->get($ident));
        }

        return $next();
    }

}
