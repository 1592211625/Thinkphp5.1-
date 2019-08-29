<?php

namespace app\http\middleware;

class Check
{
    public function handle($request, \Closure $next)
    {
        if(!session('user_name')){
            echo '这是一个中间件';
        }
        return $next($request);
    }
}
