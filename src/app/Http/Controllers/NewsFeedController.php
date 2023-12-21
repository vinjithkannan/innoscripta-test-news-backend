<?php

namespace App\Http\Controllers;

use App\Services\NewsFeederService;
use Illuminate\Http\Request;

class NewsFeedController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index(Request $request, NewsFeederService $newsFeederService)
    {
        $filters = $newsFeederService->getFilters($request);
        $newsFeedQueryResponse  = $newsFeederService->filteredNewses($filters);

        return response()->json($newsFeedQueryResponse);
    }
}
