<?php

namespace App\Services;

use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;

class ApiClientService
{
    public function __construct()
    {
    }

    public function get(string $url): Response
    {
        return Http::get($url);
    }
}
