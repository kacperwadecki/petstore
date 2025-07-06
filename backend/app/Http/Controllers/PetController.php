<?php

namespace App\Http\Controllers;

use App\Http\Requests\PetRequest;
use App\Services\PetService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Http\Resources\PetResource;

class PetController extends Controller
{
    public function __construct(
        private PetService $petService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $status = $request->query('status', 'available');
        $pets = $this->petService->getPetsByStatus($status);

        return $this->successResponse($pets, 'Pets retrieved successfully');
    }

    public function show(string $id): JsonResponse
    {
        $pet = $this->petService->getPetById($id);

        return $this->successResponse(new PetResource($pet), 'Pet retrieved successfully');
    }

    public function store(PetRequest $request): JsonResponse
    {
        $validated = $request->validated();
        $pet = $this->petService->createPet($validated);

        return $this->successResponse($pet, 'Pet created successfully', 201);
    }

    public function update(PetRequest $request, string $id): JsonResponse
    {
        $validated = $request->validated();
        $pet = $this->petService->updatePet($id, $validated);

        return $this->successResponse($pet, 'Pet updated successfully');
    }

    public function destroy(string $id): JsonResponse
    {
        $this->petService->deletePet($id);

        return $this->successResponse(null, 'Pet deleted successfully', 204);
    }
}
