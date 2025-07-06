<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class LogApiRequests
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $log = [
            'method' => $request->method(),
            'url' => $request->fullUrl(),
            'ip' => $request->ip(),
            'user_id' => optional($request->user())->id,
            'headers' => $request->headers->all(),
            'input' => $request->all(),
            'raw_body' => $request->getContent(),
        ];

        $response = $next($request);

        $log['response_status'] = $response->status();
        $log['response_snippet'] = substr($response->getContent(), 0, 500);

        \Log::info('API Request', $log);

        return $response;
    }
}
