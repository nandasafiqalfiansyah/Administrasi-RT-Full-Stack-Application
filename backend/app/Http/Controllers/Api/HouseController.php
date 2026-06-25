<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreHouseRequest;
use App\Http\Resources\HouseResource;
use App\Http\Resources\ResidentHouseHistoryResource;
use App\Services\HouseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class HouseController extends Controller
{
    public function __construct(
        private HouseService $houseService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $houses = $this->houseService->getAll($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Daftar rumah berhasil dimuat',
            'data' => HouseResource::collection($houses),
            'meta' => [
                'current_page' => $houses->currentPage(),
                'last_page' => $houses->lastPage(),
                'per_page' => $houses->perPage(),
                'total' => $houses->total(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $house = $this->houseService->find($id);

        return response()->json([
            'success' => true,
            'data' => new HouseResource($house),
        ]);
    }

    public function store(StoreHouseRequest $request): JsonResponse
    {
        $house = $this->houseService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Rumah berhasil ditambahkan',
            'data' => new HouseResource($house->load(['currentResident', 'houseHistories.resident'])),
        ], 201);
    }

    public function update(StoreHouseRequest $request, int $id): JsonResponse
    {
        $house = $this->houseService->update($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Rumah berhasil diupdate',
            'data' => new HouseResource($house->load(['currentResident', 'houseHistories.resident'])),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->houseService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Rumah berhasil dihapus',
        ]);
    }

    public function history(int $id): JsonResponse
    {
        $histories = $this->houseService->getHistory($id);

        return response()->json([
            'success' => true,
            'data' => ResidentHouseHistoryResource::collection($histories),
        ]);
    }
}