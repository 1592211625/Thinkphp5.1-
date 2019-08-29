<?php

namespace app\http\middleware;

class Before
{
    public function handle($request, \Closure $next)
    {
        if(!session('user_name')){
            echo '前';
        }
        return $next($request);
    }
}
