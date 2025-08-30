<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class RoleMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next, string $roles): Response
    {
        //разделение входной строки
        $roles = explode('|', $roles);

        //проверка доступа
        foreach ($roles as $role) {
            if($request->user()->tokenCan($role)) {
                return $next($request);
            }
        }

        return response()->json(['Error' => true, 'Message' => 'Доступ запрещен!'], 403);
    }
}
