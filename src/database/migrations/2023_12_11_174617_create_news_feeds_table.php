<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Run the migrations.
     */
    public function up(): void
    {
        Schema::create('news_feeds', function (Blueprint $table) {
            $table->id();
            $table->bigInteger('category_id')->unsigned();
            $table->bigInteger('source_id')->unsigned();
            $table->bigInteger('author_id')->unsigned();
            $table->string('title');
            $table->mediumText('summary')->nullable();
            $table->longText('content')->nullable();
            $table->text('url')->nullable();
            $table->text('image')->nullable();
            $table->dateTime('published_at');
            $table->timestamps();

            $table->foreign('category_id')->references('id')->on('categories')
                ->onDelete('cascade');
            $table->foreign('source_id')->references('id')->on('sources')
                ->onDelete('cascade');
            $table->foreign('author_id')->references('id')->on('authors')
                ->onDelete('cascade');
        });
    }

    /**
     * Reverse the migrations.
     */
    public function down(): void
    {
        Schema::dropIfExists('news_feeds');
    }
};
