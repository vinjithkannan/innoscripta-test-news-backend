<?php

namespace App\Services;

use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use Illuminate\Http\Client\Response;

class NewyorkTimesApiService implements NewsServiceInterface
{
    public function __construct(private readonly ApiClientService $apiClientService)
    {
    }

    public function fetchData(string $queryString): Response
    {
        $apiKey = $this->getApiKey();
        $fetchDataUrl = sprintf(self::NYTIMES_SERVICE_URL, $apiKey,  $queryString);

        return $this->apiClientService->get($fetchDataUrl);
    }

    public function prepareDataForStoring(Response $responseObject): ?array
    {
        $responseData = json_decode($responseObject->body(), true);
        if ($responseData['status'] === 'OK' && count($responseData['response']['docs']) > 0) {
            foreach ($responseData['response']['docs'] as $doc) {
                $author = '';
                if (isset($doc['byline']['person'][0])) {
                    $author = sprintf(
                        '%s %s',
                        $doc['byline']['person'][0]['firstname'], $doc['byline']['person'][0]['lastname']
                    );
                }

                $categoryId = Category::where('title', $doc['section_name'] ?? '')->pluck('id')->first();
                $authorId = Author::where('name', $author ?? '')->pluck('id')->first();
                $sourceId = Source::where('title', $doc['source'] ?? '')->pluck('id')->first();
                $newsFeed[] = [
                    'category_id' => $categoryId ?? 1,
                    'author_id' => $authorId ?? 1,
                    'source_id' => $sourceId ?? 1,
                    'title' => $doc['abstract'],
                    'summary' => $doc['snippet'],
                    'content' => $doc['lead_paragraph'],
                    'url' => $doc['web_url'],
                    'image' => $doc['multimedia']['url'] ?? '',
                    'published_at' => date("Y-m-d H:i:s", strtotime($doc['pub_date'])),
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
        if ($responseData['status'] === 'OK' && count($responseData['response']['docs']) > 0) {
            foreach ($responseData['response']['docs'] as $doc) {
                $author = '';
                if (isset($doc['byline']['person'][0])) {
                    $author = sprintf(
                        '%s %s',
                        $doc['byline']['person'][0]['firstname'], $doc['byline']['person'][0]['lastname']
                    );
                }
                $categoryId = Category::where('title', $doc['section_name'] ?? '')->pluck('id')->first();
                $authorId = Author::where('name', $author ?? '')->pluck('id')->first();
                $sourceId = Source::where('title', $doc['source'] ?? '')->pluck('id')->first();

                if (!$categoryId) {
                    $categories[] = $doc['section_name'] ?? '';
                }
                if (!$authorId) {
                    $authors[] = $author ?? '';
                }
                if (!$sourceId) {
                    $sources[] = $doc['source'] ?? '';
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
        return env('NY_TIMES_API_KEY');
    }
}
