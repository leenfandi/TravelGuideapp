<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Auth\Middleware\Authenticate as Middleware;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;

class Admin extends Middleware
{
    protected function authenticate($request, array $guards)
    {
        try{


            if ($this->auth->guard('admin-api')->check()) {
                return $this->auth->shouldUse('admin-api');
            }


        $this->unauthenticated($request, ['admin-api']);
        }
        catch (TokenExpiredException $e){
            return  response()->json(['msg'=>'Unauthenticated user']);
        }catch (JWTException $e)
        {
            return  response()->json(['msg'=>'token_invaled',$e ->getMessage()]);
        }
    }
    /**
     * Get the path the user should be redirected to when they are not authenticated.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return string|null
     */
    protected function redirectTo($request)
    {
        if (! $request->expectsJson()) {

            return route('login');
        }
    }
}
