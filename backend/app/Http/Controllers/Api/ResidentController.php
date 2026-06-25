<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreResidentRequest;
use App\Http\Resources\ResidentResource;
use App\Services\ResidentService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ResidentController extends Controller
{
    public function __construct(
        private ResidentService $residentService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $residents = $this->residentService->getAll($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Daftar penghuni berhasil dimuat',
            'data' => ResidentResource::collection($residents),
            'meta' => [
                'current_page' => $residents->currentPage(),
                'last_page' => $residents->lastPage(),
                'per_page' => $residents->perPage(),
                'total' => $residents->total(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $resident = $this->residentService->find($id);

        return response()->json([
            'success' => true,
            'data' => new ResidentResource($resident),
        ]);
    }

    public function store(StoreResidentRequest $request): JsonResponse
    {
        $resident = $this->residentService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Penghuni berhasil ditambahkan',
            'data' => new ResidentResource($resident->load('currentHouse')),
        ], 201);
    }

    public function update(StoreResidentRequest $request, int $id): JsonResponse
    {
        $resident = $this->residentService->update($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Penghuni berhasil diupdate',
            'data' => new ResidentResource($resident->load('currentHouse')),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->residentService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Penghuni berhasil dihapus',
        ]);
    }
}