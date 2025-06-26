<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Prometheus\CollectorRegistry;
use Symfony\Component\HttpFoundation\Response;

class RecordMetrics
{
    protected $registry;

    public function __construct(CollectorRegistry $registry)
    {
        $this->registry = $registry;
    }

    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        # Increment a counter for total HTTP requests
        $counter = $this->registry->getOrRegisterCounter(
            'gym_system', # Namespace
            'http_requests_total', # Metric name
            'Total HTTP requests', # Help text
            ['method', 'path', 'status'] # Labels
        );

        $response = $next($request);

        $counter->inc([$request->method(), $request->path(), $response->getStatusCode()]);

        return $response;
    }
}
