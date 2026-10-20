<?php

namespace App\Http\Controllers;

use App\Support\PortfolioPayload;
use Illuminate\Http\JsonResponse;

class PortfolioController extends Controller
{
    /**
     * Every section of the site in one response. The page needs all of it on
     * first paint, so splitting it would only buy four loading states.
     */
    public function __invoke(PortfolioPayload $payload): JsonResponse
    {
        return response()->json($payload->cached());
    }
}
