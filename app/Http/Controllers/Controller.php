<?php

namespace App\Http\Controllers;

use Illuminate\Foundation\Auth\Access\AuthorizesRequests;

/**
 * @OA\Info (
 *     version="1.0.0",
 *     title="Restaurant API",
 * ),
 * @OA\PathItem (
 *     path="/api"
 * ),
 * @OA\Components(
 *     @OA\SecurityScheme(
 *         securityScheme="sanctum",
 *         type="http",
 *         scheme="bearer",
 *     ),
 * ),
 */
abstract class Controller
{
    use AuthorizesRequests;
}
