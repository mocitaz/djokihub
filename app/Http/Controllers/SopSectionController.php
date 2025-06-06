<?php

namespace App\Http\Controllers;

use App\Models\SopSection;
use App\Http\Requests\StoreSopSectionRequest;
use App\Http\Requests\UpdateSopSectionRequest;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\Log;
use Exception;

class SopSectionController extends Controller
{
    /**
     * Display a listing of the resource.
     * Used to populate dropdowns or lists in the modal.
     *
     * @return \Illuminate\Http\JsonResponse
     */
    public function index(): JsonResponse
    {
        try {
            $sopSections = SopSection::orderBy('title')->get(['id', 'title']); // Only select necessary columns
            return response()->json($sopSections);
        } catch (Exception $e) {
            Log::error('Error fetching SOP Sections: ' . $e->getMessage());
            return response()->json(['message' => 'Could not retrieve SOP sections.'], 500);
        }
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \App\Http\Requests\StoreSopSectionRequest  $request
     * @return \Illuminate\Http\JsonResponse
     */
    public function store(StoreSopSectionRequest $request): JsonResponse
    {
        try {
            $sopSection = SopSection::create($request->validated());
            return response()->json([
                'message' => 'SOP Section created successfully.',
                'sopSection' => $sopSection
            ], 201);
        } catch (Exception $e) {
            Log::error('Error creating SOP Section: ' . $e->getMessage());
            // Consider more specific error messages if appropriate
            return response()->json(['message' => 'Failed to create SOP section. Please try again.'], 500);
        }
    }

    /**
     * Display the specified resource.
     * Used to populate the edit form in the modal.
     *
     * @param  \App\Models\SopSection  $sopSection
     * @return \Illuminate\Http\JsonResponse
     */
    public function show(SopSection $sopSection): JsonResponse
    {
        // The model is already loaded through route model binding
        return response()->json($sopSection);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \App\Http\Requests\UpdateSopSectionRequest  $request
     * @param  \App\Models\SopSection  $sopSection
     * @return \Illuminate\Http\JsonResponse
     */
    public function update(UpdateSopSectionRequest $request, SopSection $sopSection): JsonResponse
    {
        try {
            $sopSection->update($request->validated());
            return response()->json([
                'message' => 'SOP Section updated successfully.',
                'sopSection' => $sopSection->fresh() // Return the updated model
            ]);
        } catch (Exception $e) {
            Log::error('Error updating SOP Section: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to update SOP section. Please try again.'], 500);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SopSection  $sopSection
     * @return \Illuminate\Http\JsonResponse
     */
    public function destroy(SopSection $sopSection): JsonResponse
    {
        try {
            // Consider what happens to SopItems if a section is deleted.
            // The current migration uses onDelete('cascade'), so items will be deleted.
            // If that's not desired, you'd need to handle it here (e.g., prevent deletion if items exist, or reassign items).
            $sopSection->delete();
            return response()->json(['message' => 'SOP Section deleted successfully.']);
        } catch (Exception $e) {
            Log::error('Error deleting SOP Section: ' . $e->getMessage());
            return response()->json(['message' => 'Failed to delete SOP section. It might be in use or an error occurred.'], 500);
        }
    }
}
