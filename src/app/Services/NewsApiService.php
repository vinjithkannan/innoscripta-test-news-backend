<?php

namespace App\Services;

use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use Illuminate\Http\Client\Response;

class NewsApiService implements NewsServiceInterface
{
    public function __construct(private readonly ApiClientService $apiClientService)
    {
    }

    public function fetchData(string $queryString): Response
    {
        $apiKey = $this->getApiKey();
        $fetchDataUrl = sprintf(self::NEWS_SERVICE_URL, $apiKey,  $queryString);

        return $this->apiClientService->get($fetchDataUrl);
    }

    public function prepareDataForStoring(Response $responseObject): ?array
    {

        $responseData = json_decode($responseObject->body(), true);
        if ($responseData['status'] === 'ok' && count($responseData['articles']) > 0) {
            foreach ($responseData['articles'] as $article) {
                $categoryId = Category::where('title', $article['category'] ?? '')->pluck('id')->first();
                $authorId = Author::where('name', $article['author'] ?? '')->pluck('id')->first();
                $sourceId = Source::where('title', $article['source']['name'] ?? '')->pluck('id')->first();
                $newsFeed[] = [
                    'category_id' => $categoryId ?? 1,
                    'author_id' => $authorId ?? 1,
                    'source_id' => $sourceId ?? 1,
                    'title' => $article['title'],
                    'summary' => $article['description'],
                    'content' => $article['content'],
                    'url' => $article['url'],
                    'image' => $article['urlToImage'],
                    'published_at' => date("Y-m-d H:i:s", strtotime($article['publishedAt'])),
                ];
            }
        }

        return $newsFeed;
    }

    public function prepareParentDataForStoring(Response $responseObject): array
    {
        $authors = [];
        $sources = [];
        $responseData = json_decode($responseObject->body(), true);
        if ($responseData['status'] === 'ok' && count($responseData['articles']) > 0) {
            foreach ($responseData['articles'] as $article) {
                $authorId = Author::where('name', $article['author'] ?? '')->pluck('id')->first();
                $sourceId = Source::where('title', $article['source']['name'] ?? '')->pluck('id')->first();
                if (!$authorId) {
                    $authors[] = $article['author'] ?? '';
                }
                if (!$sourceId) {
                    $sources[] = $article['source']['name'] ?? '';
                }
            }

            $authors = array_map(function($item) {
                return ['name' => $item];
            }, array_values(array_filter(array_unique($authors))));

            $sources = array_map(function($item) {
                return ['title' => $item];
            }, array_values(array_filter(array_unique($sources))));
        }

        return [
            'authors' => $authors,
            'sources' => $sources,
            'categories' => []
        ];
    }

    public function getApiKey(): string
    {
        return env('NEWS_API_KEY');
    }
}
