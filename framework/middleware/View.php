<?php

namespace top\middleware;

use top\library\cache\driver\File;
use top\library\Config;
use top\library\http\Response;
use top\middleware\ifs\MiddlewareIfs;

class View implements MiddlewareIfs
{

    public function handler(\Closure $next)
    {
        if (!DEBUG) {
            $ident = viewCacheIdent();
            $config = Config::instance()->get('view');
            (!$config['cacheDir']) && $config['cacheDir'] = './runtime/cache/application/' . request()->module() . '/';
            $cache = File::instance($config['cacheDir']);
            if ($cache->exists($ident)) {
                $content = $cache->get($ident);
                return Response::instance()->dispatch($content);
            }
        }
        return $next();
    }

}
