<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Laravel\Scout\Searchable;
use Laravel\Scout\Attributes\SearchUsingFullText;
use Laravel\Scout\Attributes\SearchUsingPrefix;

class NewsFeed extends Model
{
    use HasApiTokens, HasFactory, Notifiable, Searchable;

    /**
     * The attributes that are mass assignable.
     *
     * @var array<int, string>
     */
    protected $fillable = [
        'category_id',
        'source_id',
        'author_id',
        'title',
        'summary',
        'content',
        'url',
        'image',
        'published_at'
    ];

    public function searchableAs(): string
    {
        return 'news_feed_index';
    }

    /**
     * Get the indexable data array for the model.
     *
     * @return array<string, mixed>
     */
    #[SearchUsingFullText(['title', 'summary', 'content'])]
    public function toSearchableArray(): array
    {
        return [
            'title' => $this->title,
            'summary' => $this->summary,
            'content' => $this->content,
        ];
    }

    public function authors()
    {
        return $this->belongsTo(Author::class);
    }

    public function categories()
    {
        return $this->belongsTo(Category::class);
    }

    public function sources()
    {
        return $this->belongsTo(Source::class);
    }
}
