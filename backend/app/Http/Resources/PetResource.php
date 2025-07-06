<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class PetResource extends JsonResource
{
    /**
     * Transform the resource into an array.
     *
     * @return array<string, mixed>
     */
    public function toArray(Request $request): array
    {
        return [
            'id' => $this['id'] ?? null,
            'name' => $this['name'] ?? '',
            'status' => $this['status'] ?? 'available',
            'category' => [
                'id' => $this['category']['id'] ?? null,
                'name' => $this['category']['name'] ?? 'Unknown'
            ],
            'photoUrls' => $this['photoUrls'] ?? [],
            'tags' => collect($this['tags'] ?? [])->map(function ($tag) {
                return [
                    'id' => $tag['id'] ?? null,
                    'name' => $tag['name'] ?? 'Unknown'
                ];
            })->toArray(),
            'created_at' => $this['created_at'] ?? null,
            'updated_at' => $this['updated_at'] ?? null,
            'main_photo' => !empty($this['photoUrls']) ? $this['photoUrls'][0] : null,
            'tag_names' => collect($this['tags'] ?? [])->pluck('name')->toArray(),
            'status_label' => $this->getStatusLabel($this['status'] ?? 'available'),
            'status_color' => $this->getStatusColor($this['status'] ?? 'available'),
        ];
    }

    private function getStatusLabel(string $status): string
    {
        return match ($status) {
            'available' => 'Available',
            'pending' => 'Pending',
            'sold' => 'Sold',
            default => 'Unknown'
        };
    }

    private function getStatusColor(string $status): string
    {
        return match ($status) {
            'available' => '#28a745',
            'pending' => '#ffc107',
            'sold' => '#dc3545',
            default => '#6c757d'
        };
    }
}
