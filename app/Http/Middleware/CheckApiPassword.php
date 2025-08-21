<?php
// app/Http/Middleware/CheckApiPassword.php
namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class CheckApiPassword

{
    public function handle(Request $request, Closure $next)
    {
        $apiPassword = $request->header('X-API-PASSWORD'); // your header key

        if (!$apiPassword || $apiPassword !== 'MySecretPassword123') {
            return response()->json(['message' => 'Unauthorized'], 401);
        }

        return $next($request);
    }

}
