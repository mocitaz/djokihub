<?php

namespace App\Http\Controllers;

use App\Models\SopItem;
use App\Http\Requests\StoreSopItemRequest;
use App\Http\Requests\UpdateSopItemRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request; // Diperlukan untuk mengambil query parameter
use Illuminate\Support\Facades\Log;
use Exception;

class SopItemController extends Controller
{
    /**
     * Display a listing of the resource.
     * Dapat difilter berdasarkan sop_section_id.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(Request $request): JsonResponse
    {
        try {
            $query = SopItem::query()->orderBy('title');

            if ($request->has('sop_section_id')) {
                $query->where('sop_section_id', $request->input('sop_section_id'));
            }

            // Pilih kolom yang diperlukan untuk mengurangi payload
            $sopItems = $query->get(['id', 'title', 'sop_section_id', 'description']);
            return response()->json($sopItems);
        } catch (Exception $e) {
            Log::error('Error fetching SOP Items: ' . $e->getMessage());
            return response()->json(['message' => 'Could not retrieve SOP items.'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSopItemRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreSopItemRequest $request): JsonResponse
    {
        try {
            $sopItem = SopItem::create($request->validated());
            return response()->json([
                'message' => 'SOP Item created successfully.',
                'sopItem' => $sopItem
            ], 201);
        } catch (Exception $e) {
            Log::error('Error creating SOP Item: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to create SOP item. Please try again.'], 500);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SopItem  $sopItem
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(SopItem $sopItem): JsonResponse
    {
        // Model sudah dimuat melalui route model binding
        // Anda mungkin ingin memuat relasi section jika diperlukan di form edit
        // $sopItem->load('sopSection:id,title');
        return response()->json($sopItem);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSopItemRequest  $request
     * @param  \App\Models\SopItem  $sopItem
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateSopItemRequest $request, SopItem $sopItem): JsonResponse
    {
        try {
            $sopItem->update($request->validated());
            return response()->json([
                'message' => 'SOP Item updated successfully.',
                'sopItem' => $sopItem->fresh() // Kembalikan model yang sudah diupdate
            ]);
        } catch (Exception $e) {
            Log::error('Error updating SOP Item: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update SOP item. Please try again.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SopItem  $sopItem
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(SopItem $sopItem): JsonResponse
    {
        try {
            $sopItem->delete();
            return response()->json(['message' => 'SOP Item deleted successfully.']);
        } catch (Exception $e) {
            Log::error('Error deleting SOP Item: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete SOP item. Please try again.'], 500);
        }
    }
}
