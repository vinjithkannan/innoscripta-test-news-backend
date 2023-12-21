<?php

namespace App\Services;

use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use Illuminate\Http\Client\Response;

class GuardianApiService implements NewsServiceInterface
{
    public function __construct(private readonly ApiClientService $apiClientService)
    {
    }

    public function fetchData(string $queryString): Response
    {
        $apiKey = $this->getApiKey();
        $fetchDataUrl = sprintf(self::GUARDIAN_SERVICE_URL, $apiKey,  $queryString);

        return $this->apiClientService->get($fetchDataUrl);
    }

    public function prepareDataForStoring(Response $responseObject): ?array
    {

        $responseData = json_decode($responseObject->body(), true);
        $responseData = $responseData['response'] ?? [];
        if ($responseData['status'] === 'ok' && count($responseData['results']) > 0) {
            foreach ($responseData['results'] as $result) {
                $authors = array_filter($result['tags'] ?? [], function($tag) {
                    $tag['type'] === 'contributor';
                });

                $categoryId = Category::where('title', $result['sectionName'] ?? '')->pluck('id')->first();
                $authorId = Author::where('name', $authors[0]['webTitle'] ?? '')->pluck('id')->first();
                $sourceId = Source::where('title', $result['fields']['publication'] ?? '')->pluck('id')->first();
                $newsFeed[] = [
                    'category_id' => $categoryId ?? 1,
                    'author_id' => $authorId ?? 1,
                    'source_id' => $sourceId ?? 1,
                    'title' => $result['webTitle'],
                    'summary' => $result['fields']['trailText'],
                    'content' => $result['fields']['body'],
                    'url' => $result['webUrl'],
                    'image' => $result['fields']['thumbnail'],
                    'published_at' => date("Y-m-d H:i:s", strtotime($result['webPublicationDate'])),
                ];
            }
        }

        return $newsFeed;
    }

    public function prepareParentDataForStoring(Response $responseObject): array
    {
        $authors = [];
        $sources = [];
        $categories = [];
        $responseData = json_decode($responseObject->body(), true);
        $responseData = $responseData['response'] ?? [];
        if ($responseData['status'] === 'ok' && count($responseData['results']) > 0) {
            foreach ($responseData['results'] as $result) {
                $authors = array_filter($result['tags'] ?? [], function($tag) {
                    $tag['type'] === 'contributor';
                });

                $categoryId = Category::where('title', $result['sectionName'] ?? '')->pluck('id')->first();
                $authorId = Author::where('name', $authors[0]['webTitle'] ?? '')->pluck('id')->first();
                $sourceId = Source::where('title', $result['fields']['publication'] ?? '')->pluck('id')->first();

                if (!$categoryId) {
                    $categories[] = $result['sectionName'] ?? '';
                }
                if (!$authorId) {
                    $authors[] = $authors[0]['webTitle'] ?? '';
                }
                if (!$sourceId) {
                    $sources[] = $result['publication'] ?? '';
                }
            }

            $categories = array_map(function($item) {
                return ['title' => $item];
            }, array_values(array_filter(array_unique($categories))));

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
            'categories' => $categories
        ];
    }

    public function getApiKey(): string
    {
        return env('THE_GUARDIAN_API_KEY');
    }
}
