<?php

namespace App\Services;

use App\Models\Author;
use App\Models\Category;
use App\Models\Source;
use App\Models\NewsFeed;
use Doctrine\DBAL\Exception\DatabaseObjectExistsException;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Database\RecordsNotFoundException;
use Illuminate\Http\Request;

class NewsFeederService
{
    private const ALLOWED_FILTERS = [
        'authors' => 'author_id',
        'categories' => 'category_id',
        'sources' => 'source_id'
    ];
    public function __construct(
        private readonly NewsServiceInterface $newsService,
    ){
    }

    public function feedNewses(): bool|array
    {
        try {
            $newsFromNewsApi = $this->newsService->fetchData('*');
            $newsInputParentData = $this->newsService->prepareParentDataForStoring($newsFromNewsApi);
            if ($newsInputParentData['categories']) {
                $this->storeCategories($newsInputParentData['categories']);
            }
            if ($newsInputParentData['authors']) {
                $this->storeAuthors($newsInputParentData['authors']);
            }
            if ($newsInputParentData['sources']) {
                $this->storeSources($newsInputParentData['sources']);
            }

            $newsInputData = $this->newsService->prepareDataForStoring($newsFromNewsApi);

            return $this->storeNewsFeed($newsInputData);
        } catch (DatabaseObjectExistsException $exception) {
            return [
                'code' => $exception->getCode(),
                'message' => $exception->getMessage()
            ];
        }
    }

    public function filteredNewses(array $filters): array | Collection
    {
        try {
            $newsFilterQuery = NewsFeed::with(['authors', 'categories', 'sources']);
            $filters = array_filter($filters, function($eachFilter) {
                return count($eachFilter) > 0;
            });

            $firstKey = array_key_first($filters);
            $filtersFirst = array_shift($filters);
            if ($filtersFirst) {
                $newsFilterQuery->whereIn(self::ALLOWED_FILTERS[$firstKey], $filtersFirst);
            }

            if ($filters) {
                foreach ($filters as $keyFilter => $filter) {
                    $newsFilterQuery->orWhereIn(self::ALLOWED_FILTERS[$keyFilter], $filter);
                }
            }

            return $newsFilterQuery->orderBy('published_at','asc')->get();
        } catch (RecordsNotFoundException $recordsNotFoundException) {
            return [
              'code' => $recordsNotFoundException->getCode(),
              'message' => $recordsNotFoundException->getMessage()
            ];
        }
    }

    public function getFilters(Request $request): array
    {
        $requestContent = json_decode($request->getContent(), true);
        $filters = $requestContent['filters'] ?? [];

        if ($request->user() && !$filters) {
            $userPreference = $request->user()->userPreference();
            $filters = [
                'authors' => json_decode($userPreference->first()->authors) ?? [],
                'categories' => json_decode($userPreference->first()->categories) ?? [],
                'sources' => json_decode($userPreference->first()->source) ?? [],
            ];
        }

        return $filters;
    }

    private function storeCategories(array $categories): void
    {
        Category::insert($categories);
    }

    private function storeAuthors(array $authors): void
    {
        Author::insert($authors);
    }

    private function storeSources(array $sources): void
    {
        Source::insert($sources);
    }

    private function storeNewsFeed(array $newFeedData): bool|array
    {
        return NewsFeed::insert($newFeedData);
    }
}
