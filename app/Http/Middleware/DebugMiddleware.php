<?php

namespace App\Http\Middleware;

use Closure;

class DebugMiddleware
{
    public function handle($request, Closure $next)
    {
        // Засекаем начальное время и потребление памяти
        $startTime = microtime(true);
        $startMemory = memory_get_usage();

        // Выполняем основной запрос
        $response = $next($request);

        // Подсчитываем время выполнения и использование памяти
        $endTime = microtime(true);
        $endMemory = memory_get_usage();

        $executionTime = round(($endTime - $startTime) * 1000, 2); // в миллисекундах
        $memoryUsage = round(($endMemory - $startMemory) / 1024, 2); // в килобайтах

        // Добавляем заголовки к ответу
        $response->headers->set('X-Debug-Time', $executionTime . ' ms');
        $response->headers->set('X-Debug-Memory', $memoryUsage . ' KB');

        return $response;
    }
}
