@php
    $currentUser = auth()->user();
    $isAdmin = $currentUser && $currentUser->role === 'admin';
    $isStaff = $currentUser && $currentUser->role === 'staff';
@endphp

@extends('layouts.app')

@section('title', 'Staff Management - DjokiHub')
@section('page_title', 'Staff Management')

@section('navigation_links')
    <nav class="ml-4 md:ml-10 flex items-center space-x-2 md:space-x-4 xl:space-x-6 overflow-x-auto py-2 styled-scrollbar">
        <a href="{{ route('dashboard') }}" class="{{ request()->is('dashboard') ? 'text-primary border-b-2 border-primary bg-primary/5' : 'text-slate-500 hover:text-primary hover:bg-primary/5' }} px-3 py-2 text-sm font-medium transition-all duration-200 whitespace-nowrap rounded-lg">
            <i class="ri-dashboard-3-line mr-1"></i>Dashboard
        </a>
        <a href="{{ route('projects.index') }}" class="{{ request()->is('projects*') || request()->is('taskboard*') ? 'text-primary border-b-2 border-primary bg-primary/5' : 'text-slate-500 hover:text-primary hover:bg-primary/5' }} px-3 py-2 text-sm font-medium transition-all duration-200 whitespace-nowrap rounded-lg">
            <i class="ri-folder-3-line mr-1"></i>Projects
        </a>
        <a href="{{ route('staff.index') }}" class="{{ request()->is('staff*') ? 'text-primary border-b-2 border-primary bg-primary/5' : 'text-slate-500 hover:text-primary hover:bg-primary/5' }} px-3 py-2 text-sm font-medium transition-all duration-200 whitespace-nowrap rounded-lg">
            <i class="ri-team-line mr-1"></i>Staff
        </a>
        <a href="{{ route('work-rules') }}" class="{{ request()->is('work-rules*') ? 'text-primary border-b-2 border-primary bg-primary/5' : 'text-slate-500 hover:text-primary hover:bg-primary/5' }} px-3 py-2 text-sm font-medium transition-all duration-200 whitespace-nowrap rounded-lg">
            <i class="ri-file-list-2-line mr-1"></i>Work Rules
        </a>
        <a href="{{ route('meeting-notes.index') }}" class="{{ request()->is('meeting-notes*') ? 'text-primary border-b-2 border-primary bg-primary/5' : 'text-slate-500 hover:text-primary hover:bg-primary/5' }} px-3 py-2 text-sm font-medium transition-all duration-200 whitespace-nowrap rounded-lg">
            <i class="ri-chat-3-line mr-1"></i>Meeting Notes
        </a>
    </nav>
@endsection

@section('header_actions')
    <div class="flex items-center space-x-3">
        {{-- Enhanced Search --}}
        <div class="relative group">
            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                <i class="ri-search-line text-slate-400 group-focus-within:text-primary transition-colors" aria-hidden="true"></i>
            </div>
            <input type="text" id="staff-search-input"
                   class="w-64 pl-12 pr-4 py-3 text-sm border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-300 focus:border-primary-500 transition-all duration-200 hover:border-gray-300"
                   placeholder="Search staff...">
        </div>

        {{-- Enhanced Filter Dropdown --}}
        <div x-data="{ open: false }" class="relative">
            <button @click="open = !open"
                    class="flex items-center justify-center space-x-2 px-4 py-3 border-2 border-gray-200 rounded-xl text-sm font-medium text-gray-700 hover:border-gray-300 bg-white transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-300 focus:border-primary-500">
                <i class="ri-filter-3-line text-gray-500" aria-hidden="true"></i>
                <span>Filters</span>
                <i class="ri-arrow-down-s-line text-gray-400 transition-transform duration-200" :class="{ 'rotate-180': open }" aria-hidden="true"></i>
            </button>
            <div x-show="open"
                 x-transition:enter="transition ease-out duration-200"
                 x-transition:enter-start="opacity-0 transform scale-95"
                 x-transition:enter-end="opacity-1 transform scale-100"
                 x-transition:leave="transition ease-in duration-150"
                 x-transition:leave-start="opacity-1 transform scale-100"
                 x-transition:leave-end="opacity-0 transform scale-95"
                 @click.away="open = false"
                 class="absolute right-0 mt-2 w-64 bg-white rounded-xl shadow-2xl border-2 border-gray-100 z-40">
                <div class="p-4 space-y-4">
                    <div>
                        <label for="filter_availability" class="block text-xs font-semibold text-gray-600 mb-2">Availability Status</label>
                        <select id="filter_availability" class="w-full px-3 py-2.5 border-2 border-gray-200 rounded-lg text-sm focus:ring-primary-300 focus:border-primary-500 transition-all duration-200">
                            <option value="">All Statuses</option>
                            <option value="Available">Available</option>
                            <option value="On Leave">On Leave</option>
                            <option value="Busy on Project">Busy on Project</option>
                        </select>
                    </div>
                    <div class="flex justify-end space-x-2 pt-2 border-t border-gray-100">
                        <button id="reset-staff-filters" class="text-sm font-medium text-gray-600 hover:text-gray-800 px-4 py-2 rounded-lg hover:bg-gray-100 transition-colors duration-150">Reset</button>
                        <button id="apply-staff-filters" @click="open = false" class="btn-primary text-sm px-4 py-2">Apply</button>
                    </div>
                </div>
            </div>
        </div>

        {{-- Enhanced Add Staff Button --}}
        @if($isAdmin)
        <button id="add-staff-button" class="btn-primary text-sm px-4 py-3 flex items-center justify-center space-x-2 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 transform">
            <i class="ri-user-add-line text-base" aria-hidden="true"></i>
            <span>Add Staff</span>
        </button>
        @endif
    </div>
@endsection

@section('content')
<div class="bg-white min-h-screen">
    {{-- Enhanced Header Section --}}
    <div class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between md:items-center py-8 border-b border-gray-200">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-primary to-primary-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="ri-team-line text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">@yield('page_title', 'Staff Management')</h1>
                        <p class="text-gray-600 text-sm mt-1">Manage your team members and their information</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 mt-4 md:mt-0 w-full sm:w-auto">
                    @yield('header_actions')
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Enhanced Flash Messages --}}
        @if(session('success'))
            <div id="alert-success" class="mb-8 transform transition-all duration-500 ease-out animate-slideInDown">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border-l-4 border-green-400 rounded-xl shadow-lg overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="ri-checkbox-circle-fill text-green-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-lg font-bold text-green-800">Success!</p>
                                <p class="text-sm text-green-700 mt-1">{{ session('success') }}</p>
                            </div>
                            <button type="button" class="ml-4 text-green-400 hover:text-green-600 p-2 rounded-full hover:bg-green-100 transition-all duration-200" onclick="this.closest('#alert-success').style.display='none'">
                                <i class="ri-close-line text-xl"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div id="alert-error" class="mb-8 transform transition-all duration-500 ease-out animate-slideInDown">
                <div class="bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-400 rounded-xl shadow-lg overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                    <i class="ri-error-warning-fill text-red-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <p class="text-lg font-bold text-red-800">Error!</p>
                                <p class="text-sm text-red-700 mt-1">{{ session('error') }}</p>
                            </div>
                            <button type="button" class="ml-4 text-red-400 hover:text-red-600 p-2 rounded-full hover:bg-red-100 transition-all duration-200" onclick="this.closest('#alert-error').style.display='none'">
                                <i class="ri-close-line text-xl"></i>
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if ($errors->any())
            <div class="mb-8 bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-400 rounded-xl shadow-lg p-6">
                <div class="flex items-center mb-3">
                    <i class="ri-error-warning-line text-red-500 text-xl mr-3"></i>
                    <p class="font-bold text-red-800">Validation Errors:</p>
                </div>
                <ul class="list-disc pl-6 text-sm text-red-700 space-y-1">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Enhanced Staff Table --}}
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
            <div class="overflow-x-auto styled-scrollbar">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Staff Member</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Contact Info</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Performance</th>
                            <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status & Location</th>
                            <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-100" id="staff-table-body">
                        @forelse ($staffMembers as $staff)
                        <tr class="staff-row hover:bg-gray-50 transition-all duration-300 ease-in-out group"
                            data-staff-id="{{ $staff->id }}" data-staff-name="{{ $staff->name }}">
                            {{-- Staff Member Info --}}
                            <td class="px-6 py-5 whitespace-nowrap">
                                <div class="flex items-center space-x-4">
                                    <div class="relative">
                                        <img src="{{ $staff->profile_photo_url }}"
                                             class="w-12 h-12 rounded-full object-cover ring-2 ring-white shadow-lg group-hover:ring-primary-300 transition-all duration-300"
                                             alt="{{ $staff->name }}">
                                        <div class="absolute -bottom-1 -right-1 w-4 h-4 bg-green-400 rounded-full border-2 border-white"></div>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-800 group-hover:text-primary transition-colors">{{ $staff->name }}</div>
                                        <div class="text-xs text-gray-500 capitalize flex items-center">
                                            <i class="ri-shield-user-line mr-1"></i>{{ $staff->role }}
                                        </div>
                                    </div>
                                </div>
                            </td>

                            {{-- Contact Info --}}
                            <td class="px-6 py-5">
                                <div class="space-y-1">
                                    <div class="text-sm text-gray-800 flex items-center">
                                        <i class="ri-mail-line mr-2 text-gray-400"></i>
                                        {{ $staff->email }}
                                    </div>
                                    @if($staff->phone_number)
                                    <div class="text-xs text-gray-500 flex items-center">
                                        <i class="ri-phone-line mr-2 text-gray-400"></i>
                                        {{ $staff->phone_number }}
                                    </div>
                                    @endif
                                </div>
                            </td>

                            {{-- Performance Stats --}}
                            <td class="px-6 py-5 whitespace-nowrap text-center">
                                <div class="space-y-2">
                                    <div class="text-2xl font-bold text-primary">{{ $staff->completed_projects_count }}</div>
                                    <div class="text-xs text-gray-500">Projects Done</div>
                                    <div class="w-full bg-gray-200 rounded-full h-1.5">
                                        <div class="bg-gradient-to-r from-primary to-primary-600 h-1.5 rounded-full"
                                             style="width: {{ min(($staff->completed_projects_count / 10) * 100, 100) }}%"></div>
                                    </div>
                                </div>
                            </td>

                            {{-- Status & Location --}}
                            <td class="px-6 py-5">
                                <div class="space-y-3">
                                    @php
                                        $availabilityClasses = [
                                            'Available' => 'bg-green-100 text-green-800 ring-green-200',
                                            'On Leave' => 'bg-yellow-100 text-yellow-800 ring-yellow-200',
                                            'Busy on Project' => 'bg-blue-100 text-blue-800 ring-blue-200',
                                        ];
                                        $statusText = $staff->availability_status ?? 'Unknown';
                                        $statusClass = $availabilityClasses[$statusText] ?? 'bg-gray-100 text-gray-700 ring-gray-200';
                                    @endphp
                                    <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full ring-1 {{ $statusClass }} hover:scale-105 transition-transform duration-200">
                                        {{ $statusText }}
                                    </span>
                                    @if($staff->location)
                                    <div class="text-xs text-gray-500 flex items-center">
                                        <i class="ri-map-pin-line mr-1 text-gray-400"></i>
                                        {{ $staff->location }}
                                    </div>
                                    @endif
                                </div>
                            </td>

                            {{-- Enhanced Actions --}}
                            <td class="px-6 py-5 whitespace-nowrap text-center">
                                <div class="flex justify-center items-center space-x-1">
                                    <button class="text-gray-400 hover:text-blue-600 p-2 rounded-lg hover:bg-blue-100 transition-all duration-200 transform hover:scale-110 view-staff-details-button"
                                            title="View Profile" data-staff-id="{{ $staff->id }}">
                                        <i class="ri-eye-line text-lg" aria-hidden="true"></i>
                                    </button>
                                    @if($isAdmin || ($isStaff && auth()->id() === $staff->id))
                                    <button class="text-gray-400 hover:text-primary p-2 rounded-lg hover:bg-primary-100 transition-all duration-200 transform hover:scale-110 edit-staff-button"
                                            title="Edit Staff" data-staff-id="{{ $staff->id }}">
                                        <i class="ri-edit-box-line text-lg" aria-hidden="true"></i>
                                    </button>
                                    @endif
                                    @if($isAdmin && auth()->id() !== $staff->id)
                                    <button class="text-gray-400 hover:text-red-600 p-2 rounded-lg hover:bg-red-100 transition-all duration-200 transform hover:scale-110 delete-staff-button"
                                            title="Delete Staff" data-staff-id="{{ $staff->id }}" data-staff-name="{{ $staff->name }}">
                                        <i class="ri-delete-bin-5-line text-lg" aria-hidden="true"></i>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="ri-team-line text-3xl text-gray-400" aria-hidden="true"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-gray-600 mb-2">No staff members found</h3>
                                    <p class="text-sm text-gray-400 mb-4">Get started by adding your first team member</p>
                                    @if($isAdmin)
                                    <button type="button" id="add-staff-from-empty" class="btn-primary text-sm px-4 py-2">
                                        <i class="ri-user-add-line mr-2" aria-hidden="true"></i>Add Staff Member
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>

            @if ($staffMembers->hasPages())
            <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 rounded-b-2xl">
                {{ $staffMembers->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Modals (Add/Edit, View, Delete) --}}
{{-- Struktur modal tetap sama seperti pada jawaban sebelumnya, karena sudah cukup baik --}}
{{-- Pastikan ID elemen dan kelas yang dirujuk oleh JavaScript konsisten --}}

<div id="add-edit-staff-modal" class="fixed inset-0 bg-slate-800 hidden items-center justify-center z-[100] p-4 sm:p-6 animate-fadeIn" style="--tw-bg-opacity: 0.75;">
    <div class="bg-white rounded-xl shadow-2xl max-w-lg w-full mx-auto my-8 max-h-[90vh] flex flex-col transform transition-all duration-300 scale-95 opacity-0" id="add-edit-staff-modal-content">
        <div class="flex justify-between items-center p-5 border-b border-slate-200 sticky top-0 bg-white z-10 rounded-t-xl">
            <h3 class="text-lg font-semibold text-slate-800" id="add-edit-staff-modal-title">Add New Staff</h3>
            <button id="close-add-edit-staff-modal-button" class="text-slate-400 hover:text-red-600 p-1 rounded-full hover:bg-red-50 transition-colors duration-150">
                <span class="sr-only">Close modal</span><i class="ri-close-line text-2xl" aria-hidden="true"></i>
            </button>
        </div>
        <form id="add-edit-staff-form" method="POST" action="{{ route('staff.store') }}" enctype="multipart/form-data" class="overflow-y-auto flex-grow styled-scrollbar">
            @csrf
            <input type="hidden" name="_method" id="staff-form-method-input" value="POST">
            <div class="p-5 md:p-6 space-y-5">
                <div>
                    <label for="staff_name_modal_form" class="block text-sm font-medium text-slate-700 mb-1.5">Full Name <span class="text-red-500">*</span></label>
                    <input type="text" id="staff_name_modal_form" name="name" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-300 focus:border-primary-500 shadow-sm placeholder-slate-400 transition-colors" required placeholder="e.g., John Doe">
                </div>
                <div>
                    <label for="staff_email_modal_form" class="block text-sm font-medium text-slate-700 mb-1.5">Email <span class="text-red-500">*</span></label>
                    <input type="email" id="staff_email_modal_form" name="email" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-300 focus:border-primary-500 shadow-sm placeholder-slate-400 transition-colors" required placeholder="e.g., john.doe@example.com">
                </div>
                <div>
                    <label for="staff_password_modal_form" class="block text-sm font-medium text-slate-700 mb-1.5">Password <span id="password-required-text" class="text-red-500">*</span></label>
                    <input type="password" id="staff_password_modal_form" name="password" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-300 focus:border-primary-500 shadow-sm transition-colors">
                    <p class="text-xs text-slate-500 mt-1 hidden" id="password-help-text">Leave blank if not changing password on edit.</p>
                </div>
                <div>
                    <label for="staff_password_confirmation_modal_form" class="block text-sm font-medium text-slate-700 mb-1.5">Confirm Password <span id="password-confirm-required-text" class="text-red-500">*</span></label>
                    <input type="password" id="staff_password_confirmation_modal_form" name="password_confirmation" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-300 focus:border-primary-500 shadow-sm transition-colors">
                </div>
                 <div>
                    <label for="staff_profile_photo_modal_form" class="block text-sm font-medium text-slate-700 mb-1.5">Profile Photo</label>
                    <input type="file" id="staff_profile_photo_modal_form" name="profile_photo" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 cursor-pointer transition-colors">
                    <p class="text-xs text-slate-500 mt-1">Max 2MB (JPG, PNG, GIF).</p>
                    <div id="current-staff-photo-preview" class="mt-2"></div>
                </div>
                <div>
                    <label for="staff_availability_status_modal_form" class="block text-sm font-medium text-slate-700 mb-1.5">Availability Status <span class="text-red-500">*</span></label>
                    <select id="staff_availability_status_modal_form" name="availability_status" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-300 focus:border-primary-500 shadow-sm appearance-none bg-white transition-colors" required>
                        <option value="Available">Available</option>
                        <option value="On Leave">On Leave</option>
                        <option value="Busy on Project">Busy on Project</option>
                    </select>
                </div>
                <div>
                    <label for="staff_phone_number_modal_form" class="block text-sm font-medium text-slate-700 mb-1.5">Phone Number</label>
                    <input type="tel" id="staff_phone_number_modal_form" name="phone_number" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-300 focus:border-primary-500 shadow-sm placeholder-slate-400 transition-colors" placeholder="e.g., 08123456789">
                </div>
                <div>
                    <label for="staff_location_modal_form" class="block text-sm font-medium text-slate-700 mb-1.5">Location</label>
                    <input type="text" id="staff_location_modal_form" name="location" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-300 focus:border-primary-500 shadow-sm placeholder-slate-400 transition-colors" placeholder="e.g., Jakarta, Indonesia">
                </div>
                <div>
                    <label for="staff_linkedin_url_modal_form" class="block text-sm font-medium text-slate-700 mb-1.5">LinkedIn URL</label>
                    <input type="url" id="staff_linkedin_url_modal_form" name="linkedin_url" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-300 focus:border-primary-500 shadow-sm placeholder-slate-400 transition-colors" placeholder="e.g., https://linkedin.com/in/username">
                </div>
                <div>
                    <label for="staff_github_url_modal_form" class="block text-sm font-medium text-slate-700 mb-1.5">GitHub URL</label>
                    <input type="url" id="staff_github_url_modal_form" name="github_url" class="w-full px-4 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-300 focus:border-primary-500 shadow-sm placeholder-slate-400 transition-colors" placeholder="e.g., https://github.com/username">
                </div>
            </div>
            <div class="flex justify-end space-x-3 px-5 md:px-6 py-4 bg-slate-50 rounded-b-xl border-t border-slate-200 sticky bottom-0 z-10">
                <button type="button" id="cancel-add-edit-staff-modal-button" class="px-5 py-2.5 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 focus:ring-2 focus:ring-offset-1 focus:ring-slate-400 transition-colors shadow-sm">Cancel</button>
                <button type="submit" id="save-add-edit-staff-button" class="btn-primary px-5 py-2.5 text-sm font-medium shadow-sm hover:shadow-md">Save Staff</button>
            </div>
        </form>
    </div>
</div>

<div id="view-staff-details-modal" class="fixed inset-0 bg-slate-800 hidden items-center justify-center z-[100] p-4 sm:p-6 animate-fadeIn" style="--tw-bg-opacity: 0.75;">
    <div class="bg-gradient-to-br from-slate-50 to-slate-100 rounded-xl shadow-2xl max-w-3xl w-full mx-auto my-8 max-h-[90vh] flex flex-col transform transition-all duration-300 scale-95 opacity-0" id="view-staff-modal-content">
        <div class="flex justify-between items-center p-5 md:p-6 border-b border-slate-200">
            <div class="flex items-center space-x-4">
                <div class="p-3 bg-primary-100 rounded-xl shadow-sm">
                    <i class="ri-user-search-fill text-primary text-3xl" aria-hidden="true"></i>
                </div>
                <div>
                    <h3 class="text-xl sm:text-2xl font-bold text-slate-800" id="view-staff-modal-title-name-profile">Staff Profile</h3>
                    <p class="text-sm text-slate-500" id="view-staff-modal-role-profile">Role: Staff</p>
                </div>
            </div>
            <button id="close-view-staff-modal-button" class="text-slate-400 hover:text-red-600 p-2 rounded-full hover:bg-red-50 transition-colors duration-150">
                <span class="sr-only">Close</span><i class="ri-close-line text-2xl" aria-hidden="true"></i>
            </button>
        </div>
        <div class="p-5 md:p-8 overflow-y-auto flex-grow space-y-6 styled-scrollbar">
            <div class="flex flex-col sm:flex-row items-center sm:items-start gap-6">
                <img id="view-staff-photo-profile" src="" class="w-28 h-28 sm:w-36 sm:h-36 rounded-full object-cover shadow-lg ring-4 ring-white flex-shrink-0 border border-slate-200" alt="Staff profile photo">
                <div class="flex-1 text-center sm:text-left pt-2">
                    <h4 id="view-staff-name-profile" class="text-2xl sm:text-3xl font-bold text-slate-800">Staff Name</h4>
                    <a href="#" id="view-staff-email-profile-link" class="text-base text-primary-600 hover:underline block mt-1">
                        <span id="view-staff-email-profile">email@example.com</span>
                    </a>
                    <div id="view-staff-availability-profile-badge" class="mt-3 inline-block"></div>
                    <div class="mt-4 flex justify-center sm:justify-start space-x-4">
                        <a href="#" id="view-staff-linkedin-link-profile" target="_blank" class="text-slate-400 hover:text-primary transition-colors duration-150 hidden" title="LinkedIn"><div class="w-9 h-9 flex items-center justify-center hover:bg-slate-200 rounded-full"><i class="ri-linkedin-box-fill text-2xl" aria-hidden="true"></i></div></a>
                        <a href="#" id="view-staff-github-link-profile" target="_blank" class="text-slate-400 hover:text-primary transition-colors duration-150 hidden" title="GitHub"><div class="w-9 h-9 flex items-center justify-center hover:bg-slate-200 rounded-full"><i class="ri-github-fill text-2xl" aria-hidden="true"></i></div></a>
                    </div>
                </div>
            </div>
            <div class="grid grid-cols-1 sm:grid-cols-2 gap-5 pt-5">
                <div class="bg-white p-5 rounded-lg shadow-md border border-slate-100">
                    <h5 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Completed Projects</h5>
                    <p id="view-staff-completed-projects" class="text-3xl sm:text-4xl font-bold text-primary">-</p>
                </div>
                <div class="bg-white p-5 rounded-lg shadow-md border border-slate-100">
                    <h5 class="text-sm font-semibold text-slate-500 uppercase tracking-wider mb-1.5">Active Projects (Handled)</h5>
                    <p id="view-staff-active-projects-count" class="text-3xl sm:text-4xl font-bold text-blue-600">-</p>
                </div>
            </div>
            <div class="border-t border-slate-200 pt-6 mt-6">
                <h5 class="text-base font-semibold text-slate-700 mb-4">Contact & Location</h5>
                <dl class="grid grid-cols-1 sm:grid-cols-2 gap-x-8 gap-y-5">
                    <div>
                        <dt class="text-xs font-medium text-slate-500 flex items-center"><i class="ri-phone-line mr-2 text-slate-400 text-base" aria-hidden="true"></i>Phone</dt>
                        <dd id="view-staff-phone-profile" class="mt-1 text-sm text-slate-800">-</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-500 flex items-center"><i class="ri-map-pin-line mr-2 text-slate-400 text-base" aria-hidden="true"></i>Location</dt>
                        <dd id="view-staff-location-profile" class="mt-1 text-sm text-slate-800">-</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-500 flex items-center"><i class="ri-calendar-check-line mr-2 text-slate-400 text-base" aria-hidden="true"></i>Join Date</dt>
                        <dd id="view-staff-join-date-profile" class="mt-1 text-sm text-slate-800">-</dd>
                    </div>
                    <div>
                        <dt class="text-xs font-medium text-slate-500 flex items-center"><i class="ri-briefcase-4-line mr-2 text-slate-400 text-base" aria-hidden="true"></i>Role</dt>
                        <dd id="view-staff-role-detail-profile" class="mt-1 text-sm text-slate-800 capitalize">-</dd>
                    </div>
                </dl>
            </div>
            <div id="view-staff-projects-section" class="border-t border-slate-200 pt-6 mt-6">
                <h5 class="text-base font-semibold text-slate-700 mb-4">Assigned Projects (<span id="view-staff-projects-count">0</span>)</h5>
                <div id="view-staff-projects-list" class="space-y-3.5 max-h-60 overflow-y-auto styled-scrollbar pr-2">
                    <p class="text-sm text-slate-500 italic py-4 text-center">No projects assigned to this staff.</p>
                </div>
            </div>
        </div>
        <div class="px-5 md:px-6 py-4 bg-slate-100 border-t border-slate-200 rounded-b-xl flex justify-end space-x-3">
            <button type="button" id="secondary-close-view-staff-modal-button" class="px-5 py-2.5 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-200 focus:ring-2 focus:ring-offset-1 focus:ring-slate-400 transition-colors shadow-sm">Close</button>
            <button id="edit-staff-from-profile-modal-button" class="btn-primary px-5 py-2.5 text-sm flex items-center space-x-2" style="display: none;">
                <i class="ri-edit-2-line" aria-hidden="true"></i>
                <span>Edit Profile</span>
            </button>
        </div>
    </div>
</div>

<div id="delete-staff-confirm-modal" class="fixed inset-0 bg-slate-800 hidden items-center justify-center z-[100] p-4 sm:p-6 animate-fadeIn" style="--tw-bg-opacity: 0.75;">
    <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-auto transform transition-all duration-300 scale-95 opacity-0" id="delete-staff-modal-content">
        <div class="p-6 md:p-8 text-center">
            <div class="mx-auto flex items-center justify-center h-16 w-16 rounded-full bg-red-100 mb-5">
                <i class="ri-error-warning-fill text-4xl text-red-500" aria-hidden="true"></i>
            </div>
            <h3 class="text-xl font-semibold text-slate-800">Delete Staff Member</h3>
            <p class="mt-2.5 text-sm text-slate-500">Are you sure you want to delete staff member <strong id="staff-to-delete-name-modal" class="font-medium text-slate-700"></strong>? This action cannot be undone.</p>
        </div>
        <form id="delete-staff-form" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex justify-center space-x-3 px-6 py-5 bg-slate-50 rounded-b-xl border-t border-slate-200">
                <button type="button" id="cancel-delete-staff-button" class="px-5 py-2.5 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 focus:ring-2 focus:ring-offset-1 focus:ring-slate-400 transition-colors shadow-sm">Cancel</button>
                <button type="submit" class="px-5 py-2.5 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 focus:ring-2 focus:ring-offset-1 focus:ring-red-500 transition-colors shadow-sm">Delete Staff</button>
            </div>
        </form>
    </div>
</div>

<style>
    .animate-fade-in { animation: fadeInGlobal 0.3s ease-out forwards; }
    @keyframes fadeInGlobal { from { opacity: 0; } to { opacity: 1; } }
    #add-edit-staff-modal-content.opacity-0,
    #view-staff-modal-content.opacity-0,
    #delete-staff-modal-content.opacity-0 { opacity: 0; transform: scale(0.95); }
    #add-edit-staff-modal-content.opacity-100,
    #view-staff-modal-content.opacity-100,
    #delete-staff-modal-content.opacity-100 { opacity: 1; transform: scale(1); }

    @keyframes fadeInUpSm {
        from { opacity: 0; transform: translateY(-10px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    .animate-fadeInUpSm { animation: fadeInUpSm 0.2s ease-out forwards; }

    .styled-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
    .styled-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .styled-scrollbar::-webkit-scrollbar-thumb { background: #cbd5e1; border-radius: 3px; }
    .styled-scrollbar::-webkit-scrollbar-thumb:hover { background: #9ca3af; }
</style>
@endsection

@push('scripts')
@if(auth()->check())
<script>
    const authUser = {
        id: {{ auth()->user()->id }},
        role: '{{ auth()->user()->role ?? "guest" }}'
    };
</script>
@else
<script>
    const authUser = { id: null, role: 'guest' };
</script>
@endif

<script>
document.addEventListener('DOMContentLoaded', function() {
    // Cache DOM elements
    const staffSearchInput = document.getElementById('staff-search-input');
    const staffFilterButton = document.querySelector('[x-data] button'); // Target Alpine button
    const staffFilterDropdownMenu = document.querySelector('[x-data] div[x-show="open"]'); // Target Alpine dropdown
    const filterAvailabilitySelect = document.getElementById('filter_availability');
    const applyStaffFiltersButton = document.getElementById('apply-staff-filters');
    const resetStaffFiltersButton = document.getElementById('reset-staff-filters');
    const staffTableBody = document.getElementById('staff-table-body');

    const addStaffButton = document.getElementById('add-staff-button');
    const addStaffFromEmptyButton = document.getElementById('add-staff-from-empty');
    const addEditStaffModal = document.getElementById('add-edit-staff-modal');
    const addEditStaffModalContent = document.getElementById('add-edit-staff-modal-content');
    const closeAddEditStaffModalButton = document.getElementById('close-add-edit-staff-modal-button');
    const cancelAddEditStaffModalButton = document.getElementById('cancel-add-edit-staff-modal-button');
    const addEditStaffForm = document.getElementById('add-edit-staff-form');
    const addEditStaffModalTitle = document.getElementById('add-edit-staff-modal-title');
    const staffFormMethodInput = document.getElementById('staff-form-method-input');
    const passwordRequiredText = document.getElementById('password-required-text');
    const passwordConfirmRequiredText = document.getElementById('password-confirm-required-text');
    const passwordHelpText = document.getElementById('password-help-text');
    const currentStaffPhotoPreview = document.getElementById('current-staff-photo-preview');


    const viewStaffDetailsModal = document.getElementById('view-staff-details-modal');
    const viewStaffModalContent = document.getElementById('view-staff-modal-content');
    const closeViewStaffModalButton = document.getElementById('close-view-staff-modal-button');
    const secondaryCloseViewStaffModalButton = document.getElementById('secondary-close-view-staff-modal-button');
    const editStaffFromProfileModalButton = document.getElementById('edit-staff-from-profile-modal-button');
    let currentViewingStaffId = null;

    const deleteStaffConfirmModal = document.getElementById('delete-staff-confirm-modal');
    const deleteStaffModalContent = document.getElementById('delete-staff-modal-content');
    const cancelDeleteStaffButton = document.getElementById('cancel-delete-staff-button');
    const staffToDeleteNameModalEl = document.getElementById('staff-to-delete-name-modal');
    const deleteStaffForm = document.getElementById('delete-staff-form');

    const defaultStaffStoreAction = "{{ route('staff.store') }}";

    // Utility Functions
    function escapeHtml(unsafe) {
        if (unsafe === null || unsafe === undefined) return '';
        return String(unsafe).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    function formatDate(dateString) {
        if (!dateString) return '-';
        try {
            const date = new Date(dateString);
            if (isNaN(date.getTime())) {
                const parts = dateString.split('T')[0].split('-');
                if (parts.length === 3) {
                    const manualDate = new Date(Date.UTC(parts[0], parseInt(parts[1],10)-1, parts[2]));
                    if (isNaN(manualDate.getTime())) return 'Invalid Date';
                    return `${manualDate.getUTCDate()} ${["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Ags","Sep","Okt","Nov","Des"][manualDate.getUTCMonth()]} ${manualDate.getUTCFullYear()}`;
                }
                return 'Invalid Date';
            }
            return `${date.getDate()} ${["Jan","Feb","Mar","Apr","Mei","Jun","Jul","Ags","Sep","Okt","Nov","Des"][date.getMonth()]} ${date.getFullYear()}`;
        } catch(e) { return 'Invalid Date'; }
    }

    function createAvailabilityBadge(statusText, forModal = false) {
        const availabilityClasses = {
            'Available': 'bg-green-100 text-green-700 ring-green-600/20',
            'On Leave': 'bg-yellow-100 text-yellow-700 ring-yellow-600/20',
            'Busy on Project': 'bg-blue-100 text-blue-700 ring-blue-600/20'
        };
        const defaultClass = 'bg-slate-100 text-slate-700 ring-slate-600/20';
        const statusClass = availabilityClasses[statusText] || defaultClass;
        const paddingAndTextSize = forModal ? 'px-3 py-1.5 text-sm' : 'px-2.5 py-1 text-xs';
        return `<span class="${paddingAndTextSize} inline-flex font-semibold rounded-full ${statusClass} ring-1">${escapeHtml(statusText) || 'Unknown'}</span>`;
    }

    function createProjectStatusBadge(text) {
        let baseClasses = 'px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ring-1';
        const projectStatusClasses = {
            'Active': 'bg-green-100 text-green-700 ring-green-200',
            'On-going': 'bg-blue-100 text-blue-700 ring-blue-200',
            'Pending': 'bg-yellow-100 text-yellow-700 ring-yellow-200',
            'Draft': 'bg-slate-100 text-slate-600 ring-slate-200',
            'Completed': 'bg-primary-50 text-primary-600 ring-primary-200',
            'Cancelled': 'bg-red-100 text-red-700 ring-red-200'
        };
        const specificClasses = projectStatusClasses[text] || 'bg-slate-100 text-slate-700 ring-slate-200';
        return `<span class="${baseClasses} ${specificClasses}">${escapeHtml(text) || 'N/A'}</span>`;
    }


    function openModal(modalEl, contentEl) {
        if (!modalEl || !contentEl) return;
        modalEl.classList.remove('hidden');
        modalEl.classList.add('flex');
        setTimeout(() => {
            contentEl.classList.remove('scale-95', 'opacity-0');
            contentEl.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeModal(modalEl, contentEl) {
        if (!modalEl || !contentEl) return;
        contentEl.classList.remove('scale-100', 'opacity-100');
        contentEl.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modalEl.classList.add('hidden');
            modalEl.classList.remove('flex');
        }, 200);
    }

    function safeSetText(id, text, defaultValue = '-') {
        const el = document.getElementById(id);
        if (el) el.textContent = text || defaultValue;
    }
    function safeSetHtml(id, htmlContent) {
        const el = document.getElementById(id);
        if (el) el.innerHTML = htmlContent;
    }
    function safeSetSrc(id, src, defaultSrc) {
        const el = document.getElementById(id);
        if (el) {
            const nameForAvatar = el.alt || 'Staff';
            el.src = src || defaultSrc || `https://ui-avatars.com/api/?name=${encodeURIComponent(nameForAvatar)}&color=FFFFFF&background=4F46E5&size=128&font-size=0.33&bold=true&format=svg`;
            el.onerror = function() {
                this.onerror = null;
                this.src = `https://ui-avatars.com/api/?name=${encodeURIComponent(nameForAvatar)}&color=FFFFFF&background=4F46E5&size=128&font-size=0.33&bold=true&format=svg`;
            };
        }
    }
     function safeSetHref(id, href, defaultHref = '#') {
        const el = document.getElementById(id);
        if (el) {
            if (href && href.trim() !== '' && href.trim() !== '#') {
                el.href = href;
                el.classList.remove('hidden', 'opacity-50', 'pointer-events-none');
            } else {
                el.href = defaultHref;
                el.classList.add('hidden', 'opacity-50', 'pointer-events-none');
            }
        }
    }

    function applyFilters() {
        const availability = filterAvailabilitySelect ? filterAvailabilitySelect.value.toLowerCase() : "";
        const searchTerm = staffSearchInput ? staffSearchInput.value.toLowerCase().trim() : "";

        if (!staffTableBody) return;

        let foundVisible = false;
        staffTableBody.querySelectorAll('tr.staff-row').forEach(row => {
            const name = (row.querySelector('td:nth-child(1) .font-bold')?.textContent || "").toLowerCase(); // Adjusted selector for name
            const email = (row.querySelector('td:nth-child(2) .text-sm.text-gray-800')?.textContent.trim() || "").toLowerCase(); // Adjusted selector for email
            const rowAvailability = (row.querySelector('td:nth-child(4) span.text-xs.font-semibold')?.textContent.trim() || "").toLowerCase(); // Adjusted selector for availability

            const matchesSearch = searchTerm === "" || name.includes(searchTerm) || email.includes(searchTerm);
            const matchesAvailability = availability === "" || rowAvailability === availability;

            if (matchesSearch && matchesAvailability) {
                row.style.display = '';
                foundVisible = true;
            } else {
                row.style.display = 'none';
            }
        });

        const emptyRow = staffTableBody.querySelector('tr td[colspan="5"]');
        if (emptyRow) {
            const emptyRowTr = emptyRow.closest('tr');
            if (foundVisible) {
                emptyRowTr.style.display = 'none';
            } else {
                 emptyRowTr.style.display = '';
                 const emptyMessage = emptyRow.querySelector('.font-medium');
                 if(emptyMessage) emptyMessage.textContent = 'No staff members match your filters.';
                 const emptySubMessage = emptyRow.querySelector('.text-sm.text-gray-400'); // Adjusted for staff.blade.php
                 if(emptySubMessage) emptySubMessage.textContent = 'Try adjusting or clearing your search filters.';
                 const addStaffFromEmptyFiltered = emptyRow.querySelector('#add-staff-from-empty');
                 if(addStaffFromEmptyFiltered) addStaffFromEmptyFiltered.style.display = 'none';
            }
        }
    }

    function resetFilters() {
        if (filterAvailabilitySelect) filterAvailabilitySelect.value = "";
        if (staffSearchInput) staffSearchInput.value = "";
        applyFilters();
        const emptyRow = staffTableBody ? staffTableBody.querySelector('tr td[colspan="5"]') : null;
        if(emptyRow && {{ $staffMembers->isEmpty() ? 'true' : 'false' }}){
             const emptyMessage = emptyRow.querySelector('.font-medium');
             if(emptyMessage) emptyMessage.textContent = 'No staff members found'; // Adjusted message
             const emptySubMessage = emptyRow.querySelector('.text-sm.text-gray-400'); // Adjusted selector
             if(emptySubMessage) emptySubMessage.textContent = 'Get started by adding your first team member'; // Adjusted message
             const addStaffFromEmptyOriginal = emptyRow.querySelector('#add-staff-from-empty');
             if(addStaffFromEmptyOriginal && authUser.role === 'admin') addStaffFromEmptyOriginal.style.display = ''; // Ensure it's visible
        }
    }

    if (staffSearchInput) staffSearchInput.addEventListener('input', () => setTimeout(applyFilters, 300));
    if (applyStaffFiltersButton) applyStaffFiltersButton.addEventListener('click', applyFilters);
    if (resetStaffFiltersButton) resetStaffFiltersButton.addEventListener('click', resetFilters);

    // Add/Edit Modal Logic
    function openAddEditStaffModal(isEdit = false, staffData = null) {
        if (!addEditStaffModal || !addEditStaffForm || !addEditStaffModalContent) {
            console.error("Add/Edit staff modal elements not found.");
            return;
        }
        addEditStaffForm.reset();
        if (currentStaffPhotoPreview) currentStaffPhotoPreview.innerHTML = '';

        const passInput = document.getElementById('staff_password_modal_form');
        const passConfirmInput = document.getElementById('staff_password_confirmation_modal_form');
        if(passInput) passInput.value = '';
        if(passConfirmInput) passConfirmInput.value = '';

        if (isEdit && staffData) {
            if (authUser.role !== 'admin' && authUser.id !== staffData.id) {
                window.showGlobalToast("You are not authorized to edit this staff member.", "error");
                return;
            }
            safeSetText('add-edit-staff-modal-title', `Edit Staff: ${staffData.name || ''}`);
            addEditStaffForm.action = `{{ url('staff') }}/${staffData.id}`;
            if (staffFormMethodInput) staffFormMethodInput.value = 'PUT';

            ['name', 'email', 'availability_status', 'phone_number', 'location', 'linkedin_url', 'github_url'].forEach(fieldKey => {
                const inputEl = document.getElementById(`staff_${fieldKey}_modal_form`);
                if (inputEl) inputEl.value = staffData[fieldKey] || (fieldKey === 'availability_status' ? 'Available' : '');
            });

            if (staffData.profile_photo_url && currentStaffPhotoPreview) {
                currentStaffPhotoPreview.innerHTML = `<img src="${staffData.profile_photo_url}" alt="Current photo" class="h-20 w-20 rounded-lg object-cover mb-2"> <p class="text-xs text-slate-500 mt-1">Current photo. Upload new to replace.</p>`;
            } else if (currentStaffPhotoPreview) {
                 currentStaffPhotoPreview.innerHTML = '<p class="text-xs text-slate-500 italic">No current photo.</p>';
            }


            if (passwordRequiredText) passwordRequiredText.style.display = 'none';
            if (passwordConfirmRequiredText) passwordConfirmRequiredText.style.display = 'none';
            if (passwordHelpText) passwordHelpText.classList.remove('hidden');
            if (passInput) passInput.required = false;
            if (passConfirmInput) passConfirmInput.required = false;

        } else {
            if (authUser.role !== 'admin') {
                 window.showGlobalToast("Only admins can add new staff.", "error");
                 return;
            }
            safeSetText('add-edit-staff-modal-title', 'Add New Staff');
            addEditStaffForm.action = defaultStaffStoreAction;
            if (staffFormMethodInput) staffFormMethodInput.value = 'POST';

            if (passwordRequiredText) passwordRequiredText.style.display = 'inline'; // Make it inline as per the HTML
            if (passwordConfirmRequiredText) passwordConfirmRequiredText.style.display = 'inline'; // Make it inline
            if (passwordHelpText) passwordHelpText.classList.add('hidden');
            if (passInput) passInput.required = true;
            if (passConfirmInput) passConfirmInput.required = true;
            if (currentStaffPhotoPreview) currentStaffPhotoPreview.innerHTML = '';
        }
        openModal(addEditStaffModal, addEditStaffModalContent);
    }

    if (addStaffButton) addStaffButton.addEventListener('click', () => openAddEditStaffModal(false));
    if (addStaffFromEmptyButton) addStaffFromEmptyButton.addEventListener('click', () => openAddEditStaffModal(false));

    if (closeAddEditStaffModalButton) closeAddEditStaffModalButton.addEventListener('click', () => closeModal(addEditStaffModal, addEditStaffModalContent));
    if (cancelAddEditStaffModalButton) cancelAddEditStaffModalButton.addEventListener('click', () => closeModal(addEditStaffModal, addEditStaffModalContent));

    // View Staff Details Modal Logic
    function populateStaffProfileModal(staff) {
        if (!staff || !viewStaffDetailsModal) return false;
        currentViewingStaffId = staff.id;

        safeSetText('view-staff-modal-title-name-profile', `${staff.name || 'Staff'}'s Profile`);
        safeSetText('view-staff-modal-role-profile', `Role: ${staff.role ? staff.role.charAt(0).toUpperCase() + staff.role.slice(1) : 'N/A'}`);
        const photoEl = document.getElementById('view-staff-photo-profile');
        if (photoEl) photoEl.alt = `${staff.name || 'Staff'}'s Profile Photo`;
        safeSetSrc('view-staff-photo-profile', staff.profile_photo_url);
        safeSetText('view-staff-name-profile', staff.name);
        const emailLink = document.getElementById('view-staff-email-profile-link');
        const emailSpan = document.getElementById('view-staff-email-profile');
        if (emailLink && emailSpan) {
            emailSpan.textContent = staff.email || '-';
            emailLink.href = staff.email ? `mailto:${staff.email}` : '#';
        }
        safeSetHtml('view-staff-availability-profile-badge', createAvailabilityBadge(staff.availability_status, true));
        safeSetText('view-staff-completed-projects', staff.completed_projects_count !== undefined ? String(staff.completed_projects_count) : '-');
        const activeProjectsCount = staff.projects ? staff.projects.filter(p => p.status !== 'Completed' && p.status !== 'Cancelled').length : 0;
        safeSetText('view-staff-active-projects-count', String(activeProjectsCount));
        safeSetText('view-staff-phone-profile', staff.phone_number);
        safeSetText('view-staff-location-profile', staff.location);
        safeSetText('view-staff-join-date-profile', formatDate(staff.created_at));
        safeSetText('view-staff-role-detail-profile', staff.role ? staff.role.charAt(0).toUpperCase() + staff.role.slice(1) : 'N/A');
        safeSetHref('view-staff-linkedin-link-profile', staff.linkedin_url);
        safeSetHref('view-staff-github-link-profile', staff.github_url);

        const projectsContainer = document.getElementById('view-staff-projects-list');
        const projectsCountEl = document.getElementById('view-staff-projects-count');
        if (projectsContainer && projectsCountEl) {
            projectsContainer.innerHTML = '';
            if (staff.projects && staff.projects.length > 0) {
                projectsCountEl.textContent = staff.projects.length;
                staff.projects.forEach(proj => {
                    const projectDiv = document.createElement('div');
                     // PERUBAHAN: Menghapus cursor-pointer dan onclick
                    projectDiv.className = 'p-3.5 bg-white border border-slate-200 rounded-lg shadow-sm hover:shadow-md transition-shadow duration-150 group';
                    // projectDiv.onclick = () => { window.location.href = `{{ url('projects') }}/${proj.id}`; }; // DIHAPUS/DIKOMENTARI

                    const deadline = proj.end_date ? formatDate(proj.end_date) : 'N/A';
                     // PERUBAHAN: Menghapus group-hover:underline dari <h6>
                    projectDiv.innerHTML = `
                        <div class="flex justify-between items-start">
                            <h6 class="text-sm font-semibold text-primary-600">${escapeHtml(proj.project_name) || 'Untitled Project'}</h6>
                            ${createProjectStatusBadge(proj.status)}
                        </div>
                        <p class="text-xs text-slate-500 mt-1">Client: ${escapeHtml(proj.client_name) || 'N/A'}</p>
                        <p class="text-xs text-slate-500 mt-0.5">Deadline: ${deadline}</p>
                    `;
                    projectsContainer.appendChild(projectDiv);
                });
            } else {
                projectsCountEl.textContent = '0';
                 // PERUBAHAN: Pesan disesuaikan
                projectsContainer.innerHTML = '<p class="text-sm text-slate-500 italic py-4 text-center">No projects assigned to this staff.</p>';
            }
        }

        if (editStaffFromProfileModalButton) {
            editStaffFromProfileModalButton.style.display = (authUser.role === 'admin' || (authUser.role === 'staff' && authUser.id === staff.id)) ? 'flex' : 'none';
        }
        openModal(viewStaffDetailsModal, viewStaffModalContent);
        return true;
    }

    // Table Row Actions & Modal Triggers
    if (staffTableBody) {
        staffTableBody.addEventListener('click', function(event) {
            const button = event.target.closest('button');
            if (!button) return;
            const row = button.closest('tr.staff-row');
            if (!row) return;
            const staffId = parseInt(row.dataset.staffId, 10);
            const staffName = row.dataset.staffName;

            const fetchStaffData = (id, actionType) => {
                const url = `{{ url('staff') }}/${id}${actionType === 'edit' ? '/edit' : ''}`;
                fetch(url, { headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }})
                    .then(res => {
                        if (!res.ok) {
                            return res.text().then(text => {
                                console.error(`Raw response for ${actionType} staff ${id}:`, text);
                                throw new Error(`Server error: ${res.status}. Response was not valid JSON. Check network tab for details.`);
                            });
                        }
                        return res.json();
                    })
                    .then(data => {
                        if (actionType === 'view') populateStaffProfileModal(data);
                        else if (actionType === 'edit') openAddEditStaffModal(true, data);
                    })
                    .catch(err => {
                        console.error(`Error fetching staff for ${actionType}:`, err);
                        window.showGlobalToast(err.message || `Failed to fetch staff data for ${actionType}.`, 'error');
                    });
            };

            if (button.classList.contains('view-staff-details-button') && staffId) {
                event.stopPropagation();
                fetchStaffData(staffId, 'view');
            } else if (button.classList.contains('edit-staff-button') && staffId) {
                event.stopPropagation();
                 if (authUser.role === 'admin' || (authUser.role === 'staff' && authUser.id === staffId)) {
                    fetchStaffData(staffId, 'edit');
                } else {
                    window.showGlobalToast("You are not authorized to edit this staff member.", "error");
                }
            } else if (button.classList.contains('delete-staff-button') && staffId) {
                event.stopPropagation();
                 if (authUser.role === 'admin') {
                    if (authUser.id === staffId) {
                        window.showGlobalToast("Admins cannot delete their own account from this page.", "error");
                        return;
                    }
                    if (staffToDeleteNameModalEl) staffToDeleteNameModalEl.textContent = staffName;
                    if (deleteStaffForm) deleteStaffForm.action = `{{ url('staff') }}/${staffId}`;
                    if (deleteStaffConfirmModal && deleteStaffModalContent) openModal(deleteStaffConfirmModal, deleteStaffModalContent);
                 } else {
                     window.showGlobalToast("Only admins can delete staff members.", "error");
                 }
            }
        });
    }

    if (closeViewStaffModalButton) closeViewStaffModalButton.addEventListener('click', () => closeModal(viewStaffDetailsModal, viewStaffModalContent));
    if (secondaryCloseViewStaffModalButton) secondaryCloseViewStaffModalButton.addEventListener('click', () => closeModal(viewStaffDetailsModal, viewStaffModalContent));

    if (editStaffFromProfileModalButton) {
        editStaffFromProfileModalButton.addEventListener('click', function() {
            if (currentViewingStaffId) {
                 if (authUser.role === 'admin' || (authUser.role === 'staff' && authUser.id === currentViewingStaffId)) {
                    fetch(`{{ url('staff') }}/${currentViewingStaffId}/edit`, {
                        headers: { 'Accept': 'application/json', 'X-Requested-With': 'XMLHttpRequest' }
                    })
                    .then(res => {
                        if (!res.ok) {
                            return res.text().then(text => {
                                console.error("Raw response for edit from profile modal fetch:", text);
                                throw new Error(`Server error: ${res.status}. Response was not valid JSON. Check network tab.`);
                            });
                        }
                        return res.json();
                    })
                    .then(data => {
                        closeModal(viewStaffDetailsModal, viewStaffModalContent);
                        openAddEditStaffModal(true, data);
                    })
                    .catch(err => {
                        console.error('Error edit from profile modal:', err);
                        window.showGlobalToast(err.message || 'Failed to fetch data for editing profile.', 'error');
                    });
                 } else {
                      window.showGlobalToast("You are not authorized to edit this profile.", "error");
                 }
            }
        });
    }

    if (cancelDeleteStaffButton && deleteStaffConfirmModal && deleteStaffModalContent) { // pastikan deleteStaffModalContent ada
        cancelDeleteStaffButton.addEventListener('click', () => closeModal(deleteStaffConfirmModal, deleteStaffModalContent));
    }

    // Auto-dismiss alert messages
    ['alert-success', 'alert-error'].forEach(alertId => {
        const alertElement = document.getElementById(alertId);
        if (alertElement) {
            setTimeout(() => {
                if (alertElement) {
                    alertElement.style.transition = 'opacity 0.5s ease, transform 0.5s ease, margin-bottom 0.5s ease';
                    alertElement.style.opacity = '0';
                    alertElement.style.transform = 'scale(0.95)';
                    alertElement.style.marginBottom = '0';
                    setTimeout(() => alertElement.remove(), 500);
                }
            }, 7000); // 7 seconds
        }
    });

    // Initialize filters if they exist
    if (typeof applyFilters === 'function') {
        applyFilters();
    }
    console.log("Staff Management JS Initialized. Auth User:", authUser);
});
</script>
@endpush