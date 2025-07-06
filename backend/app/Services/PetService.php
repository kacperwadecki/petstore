<?php

namespace App\Services;

use Illuminate\Support\Facades\Http;

class PetService
{
    private string $baseUrl;

    public function __construct()
    {
        $this->baseUrl = config('services.petstore.base_url');
    }

    public function getPetsByStatus(string $status = 'available'): array
    {
        $response = Http::get("{$this->baseUrl}/pet/findByStatus", [
            'status' => $status
        ]);

        if (!$response->successful()) {
            throw new \Exception('Failed to retrieve pets: ' . $response->body());
        }

        return $response->json();
    }

    public function getPetById(string $id): array
    {
        $response = Http::get("{$this->baseUrl}/pet/{$id}");

        if (!$response->successful()) {
            throw new \Exception('Failed to retrieve pet: ' . $response->body(), $response->status());
        }

        return $response->json();
    }

    public function createPet(array $petData): array
    {
        $response = Http::post("{$this->baseUrl}/pet", $petData);

        if (!$response->successful()) {
            throw new \Exception('Failed to create pet: ' . $response->body());
        }

        return $response->json();
    }

    public function updatePet(string $id, array $petData): array
    {
        $petData['id'] = $id;
        $response = Http::put("{$this->baseUrl}/pet", $petData);

        if (!$response->successful()) {
            throw new \Exception('Failed to update pet: ' . $response->body(), $response->status());
        }

        return $response->json();
    }

    public function deletePet(string $id): bool
    {
        $response = Http::delete("{$this->baseUrl}/pet/{$id}");

        if (!$response->successful()) {
            throw new \Exception('Failed to delete pet: ' . $response->body(), $response->status());
        }

        return true;
    }
}
