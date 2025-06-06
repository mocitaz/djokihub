<?php

namespace App\Http\Controllers;

use App\Models\MeetingNote;
use App\Models\MeetingAttachment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Carbon\Carbon;
use Illuminate\Support\Str;

class MeetingNoteController extends Controller
{
    public function index()
    {
        $meetingNotes = MeetingNote::with(['creator:id,name', 'participants:id,name,profile_photo_path'])
                            ->orderBy('meeting_datetime', 'desc')
                            ->paginate(10);
        
        $allUsers = User::orderBy('name')->get(['id', 'name', 'email']); 

        return view('meeting-notes.index', compact('meetingNotes', 'allUsers'));
    }

    public function create()
    {
        return redirect()->route('meeting-notes.index')->with('info', 'Please use the "New Meeting Note" button on the list page.');
    }

    public function store(Request $request)
    {
        \Log::info('Store request data:', $request->all());
        \Log::info('Files received:', $request->file('attachments') ?? []);

        $validator = Validator::make($request->all(), [
            'topic' => 'required|string|max:255',
            'meeting_datetime' => 'required|date_format:Y-m-d\TH:i',
            'location' => 'nullable|string|max:255',
            'notes_content' => 'required|string|min:1',
            'status' => 'required|string|in:Upcoming,In Progress,Completed,Cancelled',
            'participants' => 'nullable|array',
            'participants.*' => 'exists:users,id',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar|max:10240',
        ]);

        if ($validator->fails()) {
            \Log::error('Validation failed:', $validator->errors()->toArray());
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Validation failed.', 'errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validatedData = $validator->validated();
        
        try {
            $meetingDateTime = Carbon::parse($validatedData['meeting_datetime'])->toDateTimeString();
        } catch (\Exception $e) {
            \Log::error('Invalid datetime format:', ['error' => $e->getMessage()]);
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Invalid date or time format provided.', 'errors' => ['meeting_datetime' => ['Invalid date or time.']]], 422);
            }
            return redirect()->back()->withErrors(['meeting_datetime' => 'Invalid date or time format.'])->withInput();
        }

        $meetingNote = MeetingNote::create([
            'topic' => $validatedData['topic'],
            'meeting_datetime' => $meetingDateTime,
            'location' => $validatedData['location'],
            'notes_content' => $validatedData['notes_content'],
            'status' => $validatedData['status'],
            'created_by_user_id' => Auth::id(),
        ]);

        if ($request->filled('participants')) {
            $meetingNote->participants()->sync($validatedData['participants']);
        } else {
            $meetingNote->participants()->detach();
        }

        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                $originalName = $file->getClientOriginalName();
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('meeting_attachments/' . $meetingNote->id, $filename, 'public');
                
                $meetingNote->attachments()->create([
                    'file_name' => $originalName,
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Meeting note created successfully!', 'meetingNote' => $meetingNote->fresh()->load(['creator:id,name', 'participants:id,name,profile_photo_path', 'attachments'])], 201);
        }
        return redirect()->route('meeting-notes.index')->with('success', 'Meeting note created successfully!');
    }

    public function show(Request $request, MeetingNote $meetingNote)
    {
        $meetingNote->load([
            'creator:id,name,profile_photo_path',
            'participants:id,name,profile_photo_path',
            'attachments'
        ]);

        if ($request->expectsJson()) {
            return response()->json($meetingNote);
        }
        
        return redirect()->route('meeting-notes.index')->with('info', 'Details are viewed via modal.');
    }

    public function edit(Request $request, MeetingNote $meetingNote)
    {
        if (Auth::id() !== $meetingNote->created_by_user_id && !(Auth::user() && Auth::user()->role === 'admin')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'You are not authorized to edit this meeting note.'], 403);
            }
            abort(403, 'You are not authorized to edit this meeting note.');
        }

        $meetingNote->load('participants:id', 'attachments');

        $dataForJson = $meetingNote->toArray();
        $dataForJson['participant_ids'] = $meetingNote->participants->pluck('id')->toArray();
        $dataForJson['meeting_datetime'] = $meetingNote->meeting_datetime ? Carbon::parse($meetingNote->meeting_datetime)->format('Y-m-d\TH:i') : '';

        if ($request->expectsJson()) {
            return response()->json($dataForJson);
        }
        
        return redirect()->route('meeting-notes.index')->with('info', 'Editing is performed via the modal on the list page.');
    }

    public function update(Request $request, MeetingNote $meetingNote)
    {
        \Log::info('Update request data:', $request->all());
        \Log::info('Files received:', $request->file('attachments') ?? []);

        if (Auth::id() !== $meetingNote->created_by_user_id && !(Auth::user() && Auth::user()->role === 'admin')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'You are not authorized to update this meeting note.'], 403);
            }
            abort(403, 'You are not authorized to update this meeting note.');
        }

        // Validate the request
        $rules = [
            'topic' => 'required|string|max:255',
            'meeting_datetime' => 'required',
            'location' => 'nullable|string|max:255',
            'notes_content' => 'required|string|min:1',
            'status' => 'required|string|in:Upcoming,In Progress,Completed,Cancelled',
            'participants' => 'nullable|array',
            'participants.*' => 'exists:users,id',
            'attachments' => 'nullable|array',
            'attachments.*' => 'nullable|file|mimes:jpg,jpeg,png,gif,pdf,doc,docx,xls,xlsx,ppt,pptx,txt,zip,rar|max:10240',
            'remove_attachments' => 'nullable|array',
            'remove_attachments.*' => 'exists:meeting_attachments,id',
        ];

        $validator = Validator::make($request->all(), $rules);

        if ($validator->fails()) {
            \Log::error('Validation failed:', $validator->errors()->toArray());
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Validation failed.', 'errors' => $validator->errors()], 422);
            }
            return redirect()->back()->withErrors($validator)->withInput();
        }

        $validatedData = $validator->validated();
        
        try {
            // Parse the datetime with more flexible format
            $meetingDateTime = Carbon::parse($validatedData['meeting_datetime']);
            if (!$meetingDateTime) {
                throw new \Exception('Invalid datetime format');
            }
        } catch (\Exception $e) {
            \Log::error('Invalid datetime format:', ['error' => $e->getMessage(), 'datetime' => $validatedData['meeting_datetime']]);
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Invalid date or time format provided.', 'errors' => ['meeting_datetime' => ['Invalid date or time format.']]], 422);
            }
            return redirect()->back()->withErrors(['meeting_datetime' => 'Invalid date or time format.'])->withInput();
        }

        // Update the meeting note
        $meetingNote->update([
            'topic' => $validatedData['topic'],
            'meeting_datetime' => $meetingDateTime,
            'location' => $validatedData['location'],
            'notes_content' => $validatedData['notes_content'],
            'status' => $validatedData['status'],
        ]);

        // Handle participants
        if (isset($validatedData['participants'])) {
            $meetingNote->participants()->sync($validatedData['participants']);
        }

        // Handle attachment removals
        if (isset($validatedData['remove_attachments'])) {
            foreach ($validatedData['remove_attachments'] as $attachmentId) {
                $attachment = MeetingAttachment::where('id', $attachmentId)
                                            ->where('meeting_note_id', $meetingNote->id)
                                            ->first();
                if ($attachment) {
                    if ($attachment->file_path && Storage::disk('public')->exists($attachment->file_path)) {
                        Storage::disk('public')->delete($attachment->file_path);
                    }
                    $attachment->delete();
                }
            }
        }

        // Handle new attachments
        if ($request->hasFile('attachments')) {
            foreach ($request->file('attachments') as $file) {
                if ($file->isValid()) {
                $originalName = $file->getClientOriginalName();
                $filename = Str::uuid() . '.' . $file->getClientOriginalExtension();
                $path = $file->storeAs('meeting_attachments/' . $meetingNote->id, $filename, 'public');
                
                $meetingNote->attachments()->create([
                    'file_name' => $originalName,
                    'file_path' => $path,
                    'file_type' => $file->getClientMimeType(),
                    'file_size' => $file->getSize(),
                ]);
                }
            }
        }

        if ($request->expectsJson()) {
            return response()->json([
                'message' => 'Meeting note updated successfully!', 
                'meetingNote' => $meetingNote->fresh()->load(['creator:id,name', 'participants:id,name,profile_photo_path', 'attachments'])
            ], 200);
        }

        return redirect()->route('meeting-notes.index')->with('success', 'Meeting note updated successfully!');
    }

    public function destroy(Request $request, MeetingNote $meetingNote)
    {
        if (Auth::id() !== $meetingNote->created_by_user_id && !(Auth::user() && Auth::user()->role === 'admin')) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'You are not authorized to delete this meeting note.'], 403);
            }
            abort(403, 'You are not authorized to delete this meeting note.');
        }

        try {
            foreach ($meetingNote->attachments as $attachment) {
                if ($attachment->file_path && Storage::disk('public')->exists($attachment->file_path)) {
                    Storage::disk('public')->delete($attachment->file_path);
                }
                $attachment->delete();
            }

            $meetingNote->participants()->detach();
            $meetingNote->delete();

            if ($request->expectsJson()) {
                return response()->json(['message' => 'Meeting note deleted successfully!'], 200);
            }
            return redirect()->route('meeting-notes.index')->with('success', 'Meeting note deleted successfully!');
        } catch (\Exception $e) {
            \Log::error('Error deleting meeting note:', ['error' => $e->getMessage()]);
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Error deleting meeting note.'], 500);
            }
            return redirect()->route('meeting-notes.index')->with('error', 'Error deleting meeting note.');
        }
    }
}