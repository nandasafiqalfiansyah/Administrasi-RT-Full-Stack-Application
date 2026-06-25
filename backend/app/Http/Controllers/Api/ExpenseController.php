<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Http\Requests\StoreExpenseRequest;
use App\Http\Resources\ExpenseResource;
use App\Services\ExpenseService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class ExpenseController extends Controller
{
    public function __construct(
        private ExpenseService $expenseService
    ) {}

    public function index(Request $request): JsonResponse
    {
        $expenses = $this->expenseService->getAll($request->all());

        return response()->json([
            'success' => true,
            'message' => 'Daftar pengeluaran berhasil dimuat',
            'data' => ExpenseResource::collection($expenses),
            'meta' => [
                'current_page' => $expenses->currentPage(),
                'last_page' => $expenses->lastPage(),
                'per_page' => $expenses->perPage(),
                'total' => $expenses->total(),
            ],
        ]);
    }

    public function show(int $id): JsonResponse
    {
        $expense = $this->expenseService->find($id);

        return response()->json([
            'success' => true,
            'data' => new ExpenseResource($expense),
        ]);
    }

    public function store(StoreExpenseRequest $request): JsonResponse
    {
        $expense = $this->expenseService->create($request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Pengeluaran berhasil dicatat',
            'data' => new ExpenseResource($expense->load(['category', 'createdBy'])),
        ], 201);
    }

    public function update(StoreExpenseRequest $request, int $id): JsonResponse
    {
        $expense = $this->expenseService->update($id, $request->validated());

        return response()->json([
            'success' => true,
            'message' => 'Pengeluaran berhasil diupdate',
            'data' => new ExpenseResource($expense->load(['category', 'createdBy'])),
        ]);
    }

    public function destroy(int $id): JsonResponse
    {
        $this->expenseService->delete($id);

        return response()->json([
            'success' => true,
            'message' => 'Pengeluaran berhasil dihapus',
        ]);
    }
}