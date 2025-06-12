<?php

namespace App\Http\Controllers;

use App\Models\Project;
use App\Models\User;
use App\Models\ProjectRequirement; // Pastikan model ini ada dan benar
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\App; // Untuk DomPDF
use Carbon\Carbon; // Untuk tanggal

class ProjectController extends Controller
{
    public function index(Request $request)
    {
        Log::info('ProjectController@index - Request Query:', $request->query());

        $query = Project::with(['staff']); // Eager load staff

        // Search filter
        if ($request->filled('search')) {
            $searchTerm = $request->input('search');
            Log::info('Applying search filter:', ['term' => $searchTerm]);
            $query->where(function ($q) use ($searchTerm) {
                $q->where('project_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('client_name', 'like', '%' . $searchTerm . '%')
                  ->orWhere('order_id', 'like', '%' . $searchTerm . '%');
            });
        }

        // Status filter (multiple statuses)
        if ($request->filled('statuses') && is_array($request->input('statuses'))) {
            $statuses = $request->input('statuses');
            Log::info('Applying status filter:', ['statuses' => $statuses]);
            if (count($statuses) > 0) { // Ensure array is not empty
                $query->whereIn('status', $statuses);
            }
        }

        // Staff filter
        if ($request->filled('staff_id')) {
            $staffId = $request->input('staff_id');
            Log::info('Applying staff filter:', ['staff_id' => $staffId]);
            $query->whereHas('staff', function ($q) use ($staffId) {
                $q->where('users.id', $staffId); // Assumes pivot table links users.id
            });
        }

        // Date range filter for deadline (end_date)
        if ($request->filled('date_start')) {
            $dateStart = $request->input('date_start');
            Log::info('Applying date_start filter:', ['date_start' => $dateStart]);
            $query->whereDate('end_date', '>=', $dateStart);
        }
        if ($request->filled('date_end')) {
            $dateEnd = $request->input('date_end');
            Log::info('Applying date_end filter:', ['date_end' => $dateEnd]);
            $query->whereDate('end_date', '<=', $dateEnd);
        }

        // Calculate total budget for completed projects based on current filters
        $queryForTotal = clone $query; // Clone query before pagination for accurate sum
        $totalAllCompletedBudget = $queryForTotal->where('status', 'Completed')->sum(DB::raw('CAST(budget AS DECIMAL(15,2))'));
        Log::info('Total All Completed Budget (from filtered query):', ['total' => $totalAllCompletedBudget]);

        // Paginate results
        $projects = $query->latest()->paginate(10)->appends($request->query());
        
        // Get staff members for filter dropdown
        $staffMembers = User::where('role', 'Staff')->orderBy('name')->get();

        return view('projects.index', compact('projects', 'staffMembers', 'totalAllCompletedBudget'));
    }

    private function handleFileUpload(Request $request, $fileInputName, $projectInstance = null, $existingDbColumnValue = null)
    {
        if ($request->hasFile($fileInputName)) {
            Log::info("Handling file upload for: {$fileInputName}");
            // If updating and an old file exists (and it's not a URL), delete it
            if ($projectInstance && $existingDbColumnValue) {
                if (!filter_var($existingDbColumnValue, FILTER_VALIDATE_URL)) { // Check if it's not a URL
                    if (Storage::disk('public')->exists($existingDbColumnValue)) {
                        Log::info("Deleting old file: {$existingDbColumnValue} for project ID: {$projectInstance->id}");
                        Storage::disk('public')->delete($existingDbColumnValue);
                    } else {
                        Log::warning("Old file not found for deletion: {$existingDbColumnValue}");
                    }
                }
            }
            $file = $request->file($fileInputName);
            $originalName = pathinfo($file->getClientOriginalName(), PATHINFO_FILENAME);
            $extension = $file->getClientOriginalExtension();
            // Create a more unique filename
            $filename = Str::slug($originalName) . '-' . time() . '.' . $extension;
            
            // Store the file
            $path = $file->storeAs("projects/{$fileInputName}", $filename, 'public'); // Store in a subfolder related to the input name
            Log::info("File stored at: {$path}");
            return $path;
        }
        Log::info("No file uploaded for: {$fileInputName}");
        return null;
    }
    
    private function parseBudget(?string $budgetString): ?int
    {
        if (is_null($budgetString) || trim($budgetString) === '') {
            return null;
        }
        // Remove all non-numeric characters (e.g., "Rp", ".", ",")
        $numericValue = (int) preg_replace('/[^0-9]/', '', $budgetString);
        Log::info("Parsing budget string: '{$budgetString}' -> Parsed Int: {$numericValue}");
        // Handle case where input was "0" vs non-numeric string that becomes 0
        return ($numericValue === 0 && $budgetString !== '0' && !empty(trim($budgetString))) ? null : $numericValue;
    }


    public function store(Request $request)
    {
        Log::info('ProjectController@store - Request Data:', $request->all());
        $validatedData = $request->validate([
            'project_name' => 'required|string|max:255',
            'client_name' => 'required|string|max:255',
            'order_id' => 'nullable|string|max:255|unique:projects,order_id', // Changed from order_id_modal
            'description' => 'required|string|max:65535',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|string|max:30', 
            'status' => 'required|string|in:Draft,Pending,Active,On-going,Completed,Cancelled',
            'payment_status' => 'nullable|string|max:50',
            'assignees' => 'nullable|array',
            'assignees.*' => 'exists:users,id',
            'file_poc_upload' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip,rar|max:10240', // Max 10MB
            'file_poc_link' => 'nullable|string|max:1000|url:http,https',
            'file_bast_upload' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip,rar|max:10240', // Max 10MB
            'file_bast_link' => 'nullable|string|max:1000|url:http,https',
            'notes' => 'nullable|string|max:65535',
            'requirements' => 'required|array|min:1',
            'requirements.*.description' => 'required|string|max:500',
            'requirements.*.id' => 'nullable|integer|exists:project_requirements,id' // For potential future use, though store usually doesn't have existing req IDs
        ]);

        $parsedBudget = $this->parseBudget($request->input('budget'));
        if ($request->filled('budget') && is_null($parsedBudget) && $request->input('budget') !== '0' && trim($request->input('budget')) !== '') {
             return back()->withErrors(['budget' => 'Format harga proyek tidak valid. Harap masukkan angka atau format ribuan (misal: 50.000).'])->withInput();
        }

        DB::beginTransaction();
        try {
            $orderIdInput = $request->input('order_id'); // Changed from order_id_modal
            $finalOrderId = $orderIdInput; 
            if (empty($orderIdInput)) {
                // Generate Order ID if empty
                $prefix = "DC-0"; // Or your desired prefix
                $startNumber = 331; // Starting number if no projects exist or if reset
                $latestProject = Project::where('order_id', 'like', $prefix . '%')
                                    ->orderByRaw('CAST(SUBSTRING_INDEX(order_id, "-", -1) AS UNSIGNED) DESC, order_id DESC')
                                    ->first();
                $nextNumber = $startNumber;
                if ($latestProject) {
                    $parts = explode('-', $latestProject->order_id);
                    $lastNumber = end($parts);
                    if (is_numeric($lastNumber)) {
                        $numericPart = (int)$lastNumber;
                        $nextNumber = $numericPart + 1;
                        // Ensure nextNumber is not less than startNumber if DB was manually altered
                        if ($nextNumber < $startNumber) { 
                            $nextNumber = $startNumber;
                        }
                    }
                }
                $finalOrderId = $prefix . str_pad($nextNumber, ($nextNumber >= 1000 ? 4 : 3), '0', STR_PAD_LEFT);
            }

            $projectData = [
                'project_name' => $validatedData['project_name'],
                'client_name' => $validatedData['client_name'], 
                'order_id' => $finalOrderId,
                'description' => $validatedData['description'],
                'start_date' => $validatedData['start_date'] ?? null,
                'end_date' => $validatedData['end_date'] ?? null,
                'budget' => $parsedBudget,
                'status' => $validatedData['status'],
                'payment_status' => $validatedData['payment_status'] ?? null,
                'created_by_user_id' => Auth::id(),
                'notes' => $validatedData['notes'] ?? null,
            ];

            // Handle PoC file
            if ($request->hasFile('file_poc_upload')) {
                $projectData['poc'] = $this->handleFileUpload($request, 'file_poc_upload');
            } elseif ($request->filled('file_poc_link')) {
                $projectData['poc'] = $request->input('file_poc_link');
            }

            // Handle BAST file
            if ($request->hasFile('file_bast_upload')) {
                $projectData['bast'] = $this->handleFileUpload($request, 'file_bast_upload');
            } elseif ($request->filled('file_bast_link')) {
                $projectData['bast'] = $request->input('file_bast_link');
            }

            $project = Project::create($projectData);
            Log::info('Project created:', $project->toArray());

            // Save project requirements
            if ($request->has('requirements') && is_array($request->requirements)) {
                foreach ($request->requirements as $index => $reqData) {
                    if (!empty($reqData['description'])) { // Only save if description is not empty
                        $project->requirements()->create([
                            'description' => $reqData['description'],
                            // Checkbox value handling: 'on' if checked, not present if unchecked
                            'is_completed' => isset($reqData['is_completed_temp']) && $reqData['is_completed_temp'] === 'on', 
                            'order' => $index + 1, // Maintain order
                        ]);
                    }
                }
            }

            // Sync assigned staff
            if (!empty($validatedData['assignees'])) {
                $project->staff()->sync($validatedData['assignees']);
            }

            DB::commit();
            return redirect()->route('projects.index')->with('success', 'Proyek berhasil ditambahkan! Order ID: ' . $finalOrderId);
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error storing project: ', $e->errors());
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error storing project: ' . $e->getMessage() . ' Stack: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Gagal menambahkan proyek. ' . $e->getMessage())->withInput();
        }
    }

    public function update(Request $request, Project $project)
    {
        Log::info("ProjectController@update - Updating project ID: {$project->id}, Request Data:", $request->all());
         $validatedData = $request->validate([
            'project_name' => 'required|string|max:255',
            'client_name' => 'required|string|max:255', 
            'order_id' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('projects', 'order_id')->ignore($project->id),
            ],
            'description' => 'required|string|max:65535',
            'start_date' => 'nullable|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'budget' => 'nullable|string|max:30',
            'status' => 'required|string|in:Draft,Pending,Active,On-going,Completed,Cancelled',
            'payment_status' => 'nullable|string|max:50',
            'assignees' => 'nullable|array',
            'assignees.*' => 'exists:users,id',
            'file_poc_upload' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip,rar|max:10240',
            'file_poc_link' => 'nullable|string|max:1000|url:http,https',
            'remove_file_poc' => 'nullable|boolean',
            'file_bast_upload' => 'nullable|file|mimes:pdf,doc,docx,xls,xlsx,jpg,jpeg,png,zip,rar|max:10240',
            'file_bast_link' => 'nullable|string|max:1000|url:http,https',
            'remove_file_bast' => 'nullable|boolean',
            'notes' => 'nullable|string|max:65535',
            'requirements' => 'required|array|min:1',
            'requirements.*.id' => 'nullable|integer|exists:project_requirements,id',
            'requirements.*.description' => 'required|string|max:500',
            'requirements.*.is_completed_temp' => 'nullable|string|in:on',
        ]);

        $parsedBudget = $this->parseBudget($request->input('budget'));
        if ($request->filled('budget') && is_null($parsedBudget) && $request->input('budget') !== '0' && trim($request->input('budget')) !== '') {
             return back()->withErrors(['budget' => 'Format harga proyek tidak valid.'])->withInput();
        }

        DB::beginTransaction();
        try {
            $updateData = [
                'project_name' => $validatedData['project_name'],
                'client_name' => $validatedData['client_name'],
                'description' => $validatedData['description'],
                'start_date' => $validatedData['start_date'] ?? null,
                'end_date' => $validatedData['end_date'] ?? null,
                'budget' => $parsedBudget, 
                'status' => $validatedData['status'],
                'payment_status' => $validatedData['payment_status'] ?? null,
                'notes' => $validatedData['notes'] ?? $project->notes, // Keep old notes if new one is empty
            ];
            
            // Update order_id only if provided and different
            if ($request->filled('order_id') && $request->input('order_id') !== $project->order_id) { // Changed from order_id_modal
                $updateData['order_id'] = $request->input('order_id'); // Changed from order_id_modal
            }

            // Handle PoC file update/removal
            if ($request->input('remove_file_poc') == '1') { // Checkbox value '1' if checked
                if ($project->poc && !filter_var($project->poc, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($project->poc)) {
                    Storage::disk('public')->delete($project->poc);
                }
                $updateData['poc'] = null;
            } elseif ($request->hasFile('file_poc_upload')) {
                $updateData['poc'] = $this->handleFileUpload($request, 'file_poc_upload', $project, $project->poc);
            } elseif ($request->filled('file_poc_link')) {
                 if ($project->poc !== $request->input('file_poc_link')) { // Only update if link changed
                    // If old PoC was a file, delete it before saving new link
                    if ($project->poc && !filter_var($project->poc, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($project->poc)) {
                         Storage::disk('public')->delete($project->poc); 
                    }
                    $updateData['poc'] = $request->input('file_poc_link');
                }
            }

            // Handle BAST file update/removal
            if ($request->input('remove_file_bast') == '1') {
                if ($project->bast && !filter_var($project->bast, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($project->bast)) {
                    Storage::disk('public')->delete($project->bast);
                }
                $updateData['bast'] = null;
            } elseif ($request->hasFile('file_bast_upload')) {
                $updateData['bast'] = $this->handleFileUpload($request, 'file_bast_upload', $project, $project->bast);
            } elseif ($request->filled('file_bast_link')) {
                 if ($project->bast !== $request->input('file_bast_link')) { // Only update if link changed
                    if ($project->bast && !filter_var($project->bast, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($project->bast)) {
                        Storage::disk('public')->delete($project->bast); 
                    }
                    $updateData['bast'] = $request->input('file_bast_link');
                 }
            }

            $project->update($updateData);
            Log::info('Project updated:', $project->fresh()->toArray());

            // Sync Project Requirements
            $existingRequirementIds = $project->requirements->pluck('id')->toArray();
            $submittedRequirementIds = [];

            if ($request->has('requirements') && is_array($request->requirements)) {
                foreach ($request->requirements as $index => $reqData) {
                    if (empty($reqData['description'])) {
                        continue; 
                    }

                    $isCompleted = isset($reqData['is_completed_temp']) && $reqData['is_completed_temp'] === 'on';
                    Log::info("Processing requirement update/create:", ['data' => $reqData, 'is_completed_parsed' => $isCompleted]);

                    if (!empty($reqData['id'])) {
                        $requirement = ProjectRequirement::find($reqData['id']);
                        if ($requirement && $requirement->project_id == $project->id) {
                            $requirement->update([
                                'description' => $reqData['description'],
                                'is_completed' => $isCompleted,
                                'order' => $index + 1,
                            ]);
                            $submittedRequirementIds[] = $requirement->id;
                        }
                    } else {
                        $newReq = $project->requirements()->create([
                            'description' => $reqData['description'],
                            'is_completed' => $isCompleted,
                            'order' => $index + 1,
                        ]);
                        $submittedRequirementIds[] = $newReq->id;
                    }
                }
            }
            
            // Delete requirements that were removed from the form
            $idsToDelete = array_diff($existingRequirementIds, $submittedRequirementIds);
            if (!empty($idsToDelete)) {
                Log::info("Deleting project requirements with IDs:", $idsToDelete);
                ProjectRequirement::destroy($idsToDelete);
            }

            // Sync assigned staff
            if ($request->has('assignees')) {
                $project->staff()->sync((array) $request->input('assignees'));
            } else {
                $project->staff()->detach(); // Remove all staff if 'assignees' is not present or empty
            }

            DB::commit();
            return redirect()->route('projects.index')->with('success', 'Proyek berhasil diperbarui!');
        } catch (\Illuminate\Validation\ValidationException $e) {
            DB::rollBack();
            Log::error('Validation error updating project: ', $e->errors());
            return redirect()->back()->withErrors($e->errors())->withInput();
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error('Error updating project: ' . $e->getMessage() . ' Stack: ' . $e->getTraceAsString());
            return redirect()->back()->with('error', 'Gagal memperbarui proyek. ' . $e->getMessage())->withInput();
        }
    }
    
    public function show(Project $project)
    {
        Log::info("ProjectController@show - Fetching project ID: {$project->id}, Budget from DB: {$project->budget}");
        $project->load(['staff', 'requirements' => function ($query) {
            $query->orderBy('order', 'asc'); // Ensure requirements are ordered
        }]); 
        // Ensure accessors are called to include URLs in JSON response if they exist
        $project->file_poc_url = $project->getFilePocUrlAttribute();
        $project->file_bast_url = $project->getFileBastUrlAttribute();
        return response()->json($project);
    }

    public function edit(Project $project)
    {
        Log::info("ProjectController@edit - Editing project ID: {$project->id}, Budget from DB: {$project->budget}");
        $project->load(['staff', 'requirements' => function ($query) {
            $query->orderBy('order', 'asc');
        }]); 
        $project->file_poc_url = $project->getFilePocUrlAttribute();
        $project->file_bast_url = $project->getFileBastUrlAttribute();
        return response()->json([
            'project' => $project,
            'assignedStaffIds' => $project->staff->pluck('id')->toArray()
        ]);
    }

    // --- NEW METHODS FOR LISTING PROJECTS FOR PoC/BAST ---
    /**
     * Display a list of projects for PoC generation.
     */
    public function listProjectsForPoc(Request $request)
    {
        // You might want to filter projects that are eligible for PoC, e.g., not yet completed.
        $query = Project::query()->whereNotIn('status', ['Completed', 'Cancelled']);
        $projects = $query->orderBy('created_at', 'desc')->paginate(15);
        return view('projects.list-for-poc', compact('projects'));
    }

    /**
     * Display a list of projects for BAST generation.
     */
    public function listProjectsForBast(Request $request)
    {
        // Typically BAST is for completed or nearly completed projects
        $query = Project::query()->whereIn('status', ['Completed', 'Active', 'On-going']);
        $projects = $query->orderBy('updated_at', 'desc')->paginate(15);
        return view('projects.list-for-bast', compact('projects'));
    }
    // --- END OF NEW METHODS ---


    public function generatePocPdf(Project $project)
    {
        $project->load(['requirements' => function ($query) {
            $query->orderBy('order', 'asc');
        }]); 
        Log::info("Generating PoC PDF for Project ID: {$project->id}", ['project_data' => $project->toArray()]);

        if (empty($project->client_name)) {
            return redirect()->back()->with('error', 'Nama Klien tidak lengkap untuk membuat PoC.');
        }
        
        $companyDetails = [
            'name' => config('app.company_name', 'Djoki Coding'),
            'phone' => config('app.company_phone', '+62 851-7442-4245'),
            'email' => config('app.company_email', 'djokicoding@gmail.com'),
            'instagram' => config('app.company_instagram', '@djokicoding'),
            'website' => config('app.company_website', 'www.djokicoding.com'),
            'address' => config('app.company_address', 'Bandung, Indonesia'),
            'representative_name' => config('app.company_representative_name', 'Djoki Coding Legal Team'),
            'representative_title' => config('app.company_representative_title', 'Project Manager'),
        'logo_path' => file_exists(public_path('storage/logo_djokihub.png')) 
                        ? 'storage/logo_djokihub.png'  // Correct path after symlink
                        : null,  // Fallback option if not found
                ];

        $data = [
            'project' => $project,
            'requirements' => $project->requirements, // Already ordered by load
            'company' => $companyDetails,
            'documentTitle' => 'Proof of Concept (PoC)',
            'priceLabel' => 'Estimasi Harga',
        ];

        try {
            $pdf = App::make('dompdf.wrapper');
            // Ensure DOMPDF can access remote images/CSS if any, and handle HTML5 better
            $pdf->setOptions(['isRemoteEnabled' => true, 'isHtml5ParserEnabled' => true, 'defaultFont' => 'DejaVu Sans']);
            $pdf->loadView('documents.poc_template', $data); // Ensure this view path is correct
            $pdfFileName = 'PoC-' . Str::slug($project->project_name) . '-' . ($project->order_id ?? $project->id) . '.pdf';
            return $pdf->download($pdfFileName);
        } catch (\Exception $e) {
            Log::error("Error generating PoC PDF for Project ID {$project->id}: " . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'Gagal membuat PDF PoC: ' . $e->getMessage());
        }
    }

    public function generateBastPdf(Project $project)
    {
        $project->load(['requirements' => function ($query) {
            $query->orderBy('order', 'asc');
        }]); 
        Log::info("Generating BAST PDF for Project ID: {$project->id}", ['project_data' => $project->toArray()]);

        if (empty($project->client_name)) {
            return redirect()->back()->with('error', 'Nama Klien tidak lengkap untuk membuat BAST.');
        }

        $companyDetails = [
            'name' => config('app.company_name', 'Djoki Coding'),
            'phone' => config('app.company_phone', '+62 851-7442-4245'),
            'email' => config('app.company_email', 'djokicoding@gmail.com'),
            'instagram' => config('app.company_instagram', '@djokicoding'),
            'website' => config('app.company_website', 'www.djokicoding.com'),
            'address' => config('app.company_address', 'Bandung, Indonesia'),
            'company_representative_name' => env('COMPANY_REPRESENTATIVE_NAME', 'Djoki Coding Legal Team'),
            'representative_title' => config('app.company_representative_title', 'Project Manager'),
            'logo_path' => file_exists(public_path('images/logo_djokicoding.png')) ? 'images/logo_djokicoding.png' : (file_exists(public_path('images/logo_djokihub.png')) ? 'images/logo_djokihub.png' : null),
        ];
        
        // For BAST, typically only completed requirements are listed or all are assumed completed.
        // Adjust this logic based on your specific BAST requirements.
        $requirementsForBast = $project->requirements()->where('is_completed', true)->orderBy('order')->get();
        if ($requirementsForBast->isEmpty() && $project->requirements->isNotEmpty()) {
            // If no requirements are marked completed, but there are requirements, maybe list all?
            // Or show a message? For now, let's assume BAST needs completed items.
            // You might want to redirect with an error if no completed items for BAST.
            Log::warning("BAST generation for Project ID {$project->id}: No requirements marked as completed.");
            // $requirementsForBast = $project->requirements; // Uncomment to list all if none are completed
        }


        $data = [
            'project' => $project,
            'requirements' => $requirementsForBast, // Use filtered requirements
            'company' => $companyDetails,
            'documentTitle' => 'BAST (Berita Acara Serah Terima)', // Standard Indonesian term
            'priceLabel' => 'Harga Proyek (Final)', // Or appropriate label
        ];

        try {
            $pdf = App::make('dompdf.wrapper');
            $pdf->setOptions(['isRemoteEnabled' => true, 'isHtml5ParserEnabled' => true, 'defaultFont' => 'DejaVu Sans']);
            $pdf->loadView('documents.bast_template', $data); // Ensure this view path is correct
            $pdfFileName = 'BAST-' . Str::slug($project->project_name) . '-' . ($project->order_id ?? $project->id) . '.pdf';
            return $pdf->download($pdfFileName);
        } catch (\Exception $e) {
            Log::error("Error generating BAST PDF for Project ID {$project->id}: " . $e->getMessage(), ['exception' => $e]);
            return redirect()->back()->with('error', 'Gagal membuat PDF BAST: ' . $e->getMessage());
        }
    }
    
    public function destroy(Project $project)
    {
        DB::beginTransaction();
        try {
            // Delete associated files from storage if they are not URLs
            if ($project->poc && !filter_var($project->poc, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($project->poc)) {
                Storage::disk('public')->delete($project->poc);
            }
            if ($project->bast && !filter_var($project->bast, FILTER_VALIDATE_URL) && Storage::disk('public')->exists($project->bast)) {
                Storage::disk('public')->delete($project->bast);
            }

            $project->staff()->detach(); // Detach staff members from pivot table
            $project->requirements()->delete(); // Delete associated requirements
            $project->delete(); // Delete the project itself

            DB::commit();
            return redirect()->route('projects.index')->with('success', 'Proyek berhasil dihapus.');
        } catch (\Exception $e) {
            DB::rollBack();
            Log::error("Error deleting project {$project->id}: " . $e->getMessage() . ' Stack: ' . $e->getTraceAsString());
            return redirect()->route('projects.index')->with('error', 'Gagal menghapus proyek.');
        }
    }
}

