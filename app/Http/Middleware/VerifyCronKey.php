<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Log;
class VerifyCronKey
{
    public function handle(Request $request, Closure $next): Response
    {
        // لو الكلمة السرية غلط، اطرده
        if ($request->query('key') !== config('cron.key')) {
            return response()->json(['error' => 'Unauthorized - Invalid Key'], 401);
        }

        // لو صح، كمل عادي
        return $next($request);
    }
}