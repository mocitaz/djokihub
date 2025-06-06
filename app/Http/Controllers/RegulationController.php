<?php

namespace App\Http\Controllers;

use App\Models\Regulation;
use App\Http\Requests\StoreRegulationRequest;
use App\Http\Requests\UpdateRegulationRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Exception;

class RegulationController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            // Pilih kolom yang diperlukan untuk mengurangi payload
            $regulations = Regulation::orderBy('title')->get(['id', 'title', 'description']);
            return response()->json($regulations);
        } catch (Exception $e) {
            Log::error('Error fetching Regulations: ' . $e->getMessage());
            return response()->json(['message' => 'Could not retrieve regulations.'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreRegulationRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreRegulationRequest $request): JsonResponse
    {
        try {
            $regulation = Regulation::create($request->validated());
            return response()->json([
                'message' => 'Regulation created successfully.',
                'regulation' => $regulation
            ], 201);
        } catch (Exception $e) {
            Log::error('Error creating Regulation: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create regulation. Please try again.'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Regulation  $regulation
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(Regulation $regulation): JsonResponse
    {
        // Model sudah dimuat melalui route model binding
        return response()->json($regulation);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateRegulationRequest  $request
     * @param  \App\Models\Regulation  $regulation
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateRegulationRequest $request, Regulation $regulation): JsonResponse
    {
        try {
            $regulation->update($request->validated());
            return response()->json([
                'message' => 'Regulation updated successfully.',
                'regulation' => $regulation->fresh() // Kembalikan model yang sudah diupdate
            ]);
        } catch (Exception $e) {
            Log::error('Error updating Regulation: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update regulation. Please try again.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Regulation  $regulation
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(Regulation $regulation): JsonResponse
    {
        try {
            $regulation->delete();
            return response()->json(['message' => 'Regulation deleted successfully.']);
        } catch (Exception $e) {
            Log::error('Error deleting Regulation: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete regulation. Please try again.'], 500);
        }
    }
}
