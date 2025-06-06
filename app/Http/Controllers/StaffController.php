<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Arr;
use Illuminate\Validation\Rules;
use Illuminate\Validation\Rule as ValidationRule;

class StaffController extends Controller
{
    /**
     * Display a listing of the staff members.
     */
    public function index()
    {
        $staffMembers = User::where('role', 'staff')
                            ->orderBy('name')
                            ->paginate(10);
        
        return view('staff', compact('staffMembers'));
    }

    /**
     * Show the form for creating a new staff member.
     */
    public function create()
    {
        if (Auth::user()->role !== 'admin') {
            abort(403, 'Unauthorized action.');
        }
        return redirect()->route('staff.index')->with('info', 'Staff creation is handled via the modal on the staff list page.');
    }

    /**
     * Store a newly created staff member in storage.
     */
    public function store(Request $request)
    {
        if (Auth::user()->role !== 'admin') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized action.'], 403);
            }
            abort(403, 'Unauthorized action.');
        }

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'confirmed', Rules\Password::defaults()],
            'availability_status' => ['required', 'string', ValidationRule::in(['Available', 'On Leave', 'Busy on Project'])],
            'phone_number' => ['nullable', 'string', 'max:20', 'regex:/^[0-9\-\+$$  $$ ]*$/'],
            'location' => ['nullable', 'string', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
        ]);

        $userData = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'password' => Hash::make($validatedData['password']),
            'role' => 'staff',
            'availability_status' => $validatedData['availability_status'],
            'phone_number' => $validatedData['phone_number'] ?? null,
            'location' => $validatedData['location'] ?? null,
            'linkedin_url' => $validatedData['linkedin_url'] ?? null,
            'github_url' => $validatedData['github_url'] ?? null,
        ];
        
        $user = User::create(Arr::except($userData, ['profile_photo_path']));

        if ($request->hasFile('profile_photo')) {
            if (!Storage::disk('public')->exists('profile-photos')) {
                Storage::disk('public')->makeDirectory('profile-photos', 0775, true);
                \Log::info("Directory profile-photos created for new user {$user->id}");
            }
            $newPhotoPath = $request->file('profile_photo')->store('profile-photos', 'public');
            if ($newPhotoPath) {
                $user->profile_photo_path = $newPhotoPath;
                $user->save();
                \Log::info("New photo stored for user {$user->id}: {$newPhotoPath}");
            } else {
                \Log::error("Failed to store new photo for user {$user->id}. Check disk permissions or space.");
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Failed to store profile photo.'], 500);
                }
                return redirect()->back()->with('error', 'Failed to store profile photo. Check disk permissions or space.');
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Staff successfully added!', 'staff' => $user->fresh()], 201);
        }

        return redirect()->route('staff.index')->with('success', 'Staff successfully added!');
    }

    /**
     * Display the specified staff member's details.
     */
    public function show(Request $request, User $user)
    {
        if ($user->role !== 'staff') {
            if ($request->expectsJson()) {
                return response()->json(['error' => 'Staff member not found.'], 404);
            }
            abort(404, 'Staff member not found.');
        }

        $user->load(['projects' => function ($query) {
            $query->orderByRaw("FIELD(status, 'On-going', 'Active', 'Pending', 'Draft', 'Completed', 'Cancelled')")
                  ->orderBy('end_date', 'asc');
        }]);

        $responseData = $user->toArray();
        $responseData['completed_projects_count'] = $user->completed_projects_count ?? 0;
        
        return response()->json($responseData);
    }

    /**
     * Show the form for editing the specified staff member or return JSON data.
     */
    public function edit(Request $request, $id)
    {
        try {
            $user = User::findOrFail($id);
            if ($user->role !== 'staff') {
                return $request->expectsJson()
                    ? response()->json(['error' => 'The selected user is not a staff member.'], 404)
                    : (Auth::user() && Auth::user()->role === 'admin'
                        ? redirect()->route('staff.index')->with('error', 'The selected user is not a staff member.')
                        : abort(404, 'Staff profile not found.'));
            }

            if (Auth::user()->role !== 'admin' && Auth::id() !== $user->id) {
                return $request->expectsJson()
                    ? response()->json(['error' => 'You are not authorized to edit this profile.'], 403)
                    : abort(403, 'You are not authorized to edit this profile.');
            }

            if ($request->expectsJson()) {
                $responseData = $user->toArray();
                return response()->json($responseData);
            }

            return view('staff.edit', ['staffMember' => $user]);
        } catch (\Illuminate\Database\Eloquent\ModelNotFoundException $e) {
            \Log::error("Staff member not found for ID: {$id}");
            return $request->expectsJson()
                ? response()->json(['error' => 'Staff member not found.'], 404)
                : abort(404, 'Staff member not found.');
        } catch (\Exception $e) {
            \Log::error("Error in staff edit: {$e->getMessage()}");
            return $request->expectsJson()
                ? response()->json(['error' => 'Server error occurred.'], 500)
                : abort(500, 'Server error occurred.');
        }
    }

    /**
     * Update the specified staff member in storage.
     */
    public function update(Request $request, User $user)
    {
        if ($user->role !== 'staff') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Staff not found or user is not a staff member.'], 404);
            }
            return redirect()->route('staff.index')->with('error', 'Staff not found or user is not a staff member.');
        }

        if (Auth::user()->role !== 'admin' && Auth::id() !== $user->id) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'You are not authorized to update this profile.'], 403);
            }
            abort(403, 'You are not authorized to update this profile.');
        }

        $validatedData = $request->validate([
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', ValidationRule::unique('users')->ignore($user->id)],
            'password' => ['nullable', 'confirmed', Rules\Password::defaults()],
            'availability_status' => ['required', 'string', ValidationRule::in(['Available', 'On Leave', 'Busy on Project'])],
            'phone_number' => ['nullable', 'string', 'max:20', 'regex:/^[0-9\-\+$$  $$ ]*$/'],
            'location' => ['nullable', 'string', 'max:255'],
            'linkedin_url' => ['nullable', 'url', 'max:255'],
            'github_url' => ['nullable', 'url', 'max:255'],
            'profile_photo' => ['nullable', 'image', 'mimes:jpg,jpeg,png,gif', 'max:2048'],
        ]);

        $updateData = [
            'name' => $validatedData['name'],
            'email' => $validatedData['email'],
            'availability_status' => $validatedData['availability_status'],
            'phone_number' => $validatedData['phone_number'] ?? null,
            'location' => $validatedData['location'] ?? null,
            'linkedin_url' => $validatedData['linkedin_url'] ?? null,
            'github_url' => $validatedData['github_url'] ?? null,
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($validatedData['password']);
        }
        
        $user->update(Arr::except($updateData, ['profile_photo_path']));

        if ($request->hasFile('profile_photo')) {
            if (!Storage::disk('public')->exists('profile-photos')) {
                Storage::disk('public')->makeDirectory('profile-photos', 0775, true);
                \Log::info("Directory profile-photos created for user {$user->id}");
            }
            if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
                Storage::disk('public')->delete($user->profile_photo_path);
                \Log::info("Old photo deleted for user {$user->id}: {$user->profile_photo_path}");
            }
            $newPhotoPath = $request->file('profile_photo')->store('profile-photos', 'public');
            if ($newPhotoPath) {
                $user->profile_photo_path = $newPhotoPath;
                $user->save();
                \Log::info("New photo stored for user {$user->id}: {$newPhotoPath}");
            } else {
                \Log::error("Failed to store new photo for user {$user->id}. Check disk permissions or space.");
                if ($request->expectsJson()) {
                    return response()->json(['message' => 'Failed to store profile photo.'], 500);
                }
                return redirect()->back()->with('error', 'Failed to store profile photo. Check disk permissions or space.');
            }
        }

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Staff data successfully updated!', 'staff' => $user->fresh()]);
        }
        
        $successMessage = 'Staff data successfully updated!';
        if (Auth::id() === $user->id) {
            $successMessage = 'Your profile was successfully updated!';
            return redirect()->route('staff.edit', ['user' => $user->id])->with('success', $successMessage);
        }
        return redirect()->route('staff.index')->with('success', $successMessage);
    }

    /**
     * Remove the specified staff member from storage.
     */
    public function destroy(Request $request, User $user)
    {
        if (Auth::user()->role !== 'admin') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized action.'], 403);
            }
            abort(403, 'Unauthorized action.');
        }

        if ($user->role !== 'staff') {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Staff not found or user is not a staff member.'], 404);
            }
            return redirect()->route('staff.index')->with('error', 'Staff not found or user is not a staff member.');
        }
        
        if (Auth::id() === $user->id) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'You cannot delete your own account through this action.'], 403);
            }
            return redirect()->route('staff.index')->with('error', 'You cannot delete your own account through this page.');
        }

        if ($user->profile_photo_path && Storage::disk('public')->exists($user->profile_photo_path)) {
            Storage::disk('public')->delete($user->profile_photo_path);
        }

        $user->delete();

        if ($request->expectsJson()) {
            return response()->json(['message' => 'Staff successfully deleted.']);
        }
        return redirect()->route('staff.index')->with('success', 'Staff successfully deleted.');
    }
}