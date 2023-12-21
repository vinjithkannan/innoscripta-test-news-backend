<?php

namespace App\Services;

use Illuminate\Http\Client\Response;

interface NewsServiceInterface
{
    const GUARDIAN_SERVICE_URL = 'https://content.guardianapis.com/search?api-key=%s' .
    '&show-fields=trailText,publication,body,thumbnail' .
    '&show-tags=contributor';
    const NYTIMES_SERVICE_URL = 'https://api.nytimes.com/svc/search/v2/articlesearch.json?api-key=%s';
    const NEWS_SERVICE_URL = 'https://newsapi.org/v2/everything?apiKey=%s&q=%s';

    public function fetchData(string $queryString): Response;
    public function prepareDataForStoring(Response $responseObject): ?array;
    public function prepareParentDataForStoring(Response $responseObject): array;
    public function getApiKey(): string;
}
