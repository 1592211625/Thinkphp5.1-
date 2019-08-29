<?php

namespace app\http\middleware;

class After
{
    public function handle($request, \Closure $next)
    {
        if(!session('user_name')){
            echo '后';
        }
        return $next($request);
    }
}
