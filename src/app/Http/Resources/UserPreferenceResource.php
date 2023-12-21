<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class UserPreferenceResource extends JsonResource {
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array {
        return [
            'user_id' => $this->user_id,
            'author' => json_decode($this->author),
            'category' => json_decode($this->category),
            'source' => json_decode($this->source),
            'created_at' => $this->created_at,
            'updated_at' => $this->updated_at,
        ];
    }
}
