@extends('layouts.app')

@section('title', 'Project Management - DjokiHub')
@section('page_title', 'Project Management')

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
        {{-- View Toggle --}}
        <div class="hidden sm:flex bg-slate-100 rounded-lg p-1">
            <button id="table-view-btn" class="view-toggle-btn active px-3 py-2 text-xs font-medium rounded-md transition-all duration-200" data-view="table">
                <i class="ri-table-line mr-1"></i>Table
            </button>
            <button id="cards-view-btn" class="view-toggle-btn px-3 py-2 text-xs font-medium rounded-md transition-all duration-200" data-view="cards">
                <i class="ri-grid-line mr-1"></i>Cards
            </button>
        </div>
        
        {{-- Add Project Button --}}
        <button id="add-project-button" class="btn-primary text-sm px-4 py-2.5 flex items-center justify-center space-x-2 shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 transform">
        <i class="ri-add-line text-base" aria-hidden="true"></i>
        <span>New Project</span>
    </button>
    </div>
@endsection

@section('content')
<div class="bg-white min-h-screen">
    {{-- Simple White Header --}}
    <div class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between md:items-center py-8 border-b border-gray-200">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-primary rounded-xl flex items-center justify-center shadow-lg">
                        <i class="ri-briefcase-4-line text-white text-xl"></i>
                    </div>
                    <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">@yield('page_title', 'Page Title')</h1>
                        <p class="text-gray-600 text-sm mt-1">Manage and track your projects efficiently</p>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-3 mt-4 md:mt-0 w-full sm:w-auto">
                    @yield('header_actions')
                </div>
            </div>
        </div>
    </div>

    {{-- Enhanced Filter Bar --}}
    <div class="bg-white border-b border-gray-200 filter-section shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <form id="filter-form" method="GET" action="{{ route('projects.index') }}" class="py-6">
                <div class="grid grid-cols-1 sm:grid-cols-2 lg:grid-cols-3 xl:grid-cols-5 gap-4 items-end">
                    {{-- Enhanced Search --}}
                    <div class="xl:col-span-2">
                        <label for="project-search-input" class="block text-sm font-semibold text-gray-700 mb-2">Search Projects</label>
                        <div class="relative group">
                            <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                                <i class="ri-search-line text-gray-400 group-focus-within:text-primary transition-colors" aria-hidden="true"></i>
                            </div>
                            <input type="text" id="project-search-input" name="search" value="{{ request('search') }}" 
                                   class="w-full pl-12 pr-4 py-3 text-sm border-2 border-gray-200 rounded-xl focus:outline-none focus:ring-2 focus:ring-primary-300 focus:border-primary-500 transition-all duration-200 hover:border-gray-300" 
                                   placeholder="Search by name, client, or ID...">
                        </div>
                    </div>

                    {{-- Enhanced Status Filter --}}
                    <div class="relative">
                        <label for="status-filter-button" class="block text-sm font-semibold text-gray-700 mb-2">Project Status</label>
                        <button type="button" id="status-filter-button" 
                                class="w-full flex items-center justify-between px-4 py-3 border-2 border-gray-200 rounded-xl text-sm hover:border-gray-300 bg-white text-left transition-all duration-200 focus:outline-none focus:ring-2 focus:ring-primary-300 focus:border-primary-500">
                            <span id="status-filter-text" class="truncate font-medium">Any Status</span>
                            <i class="ri-arrow-down-s-line ml-2 text-gray-400 transition-transform duration-200" aria-hidden="true"></i>
                        </button>
                        <div id="status-dropdown-menu" class="hidden absolute z-40 mt-2 w-full bg-white rounded-xl shadow-2xl border-2 border-gray-100 max-h-64 overflow-y-auto styled-scrollbar animate-fadeInUpSm">
                            <div class="p-3 space-y-2">
                                @php $projectStatuses = ['Active', 'On-going', 'Pending', 'Completed', 'Draft', 'Cancelled']; @endphp
                                @foreach($projectStatuses as $statusValue)
                                <label class="flex items-center p-3 hover:bg-primary-50 rounded-lg cursor-pointer transition-all duration-150 group">
                                    <input type="checkbox" name="statuses[]" value="{{ $statusValue }}" 
                                           class="form-checkbox h-4 w-4 text-primary rounded border-gray-300 focus:ring-primary-300 filter-status-checkbox transition-colors"
                                           @if(is_array(request('statuses')) && in_array($statusValue, request('statuses'))) checked @endif>
                                    <span class="ml-3 text-sm font-medium text-gray-700 group-hover:text-primary-700">{{ $statusValue }}</span>
                                </label>
                                @endforeach
                            </div>
                        </div>
                    </div>

                    {{-- Enhanced Staff Filter --}}
                    <div>
                        <label for="filter_staff_id" class="block text-sm font-semibold text-gray-700 mb-2">Assigned Staff</label>
                        <select id="filter_staff_id" name="staff_id" 
                                class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-primary-300 focus:border-primary-500 transition-all duration-200 hover:border-gray-300">
                            <option value="">All Staff Members</option>
                            @if(isset($staffMembers))
                                @foreach($staffMembers as $staff)
                                    <option value="{{ $staff->id }}" @if(request('staff_id') == $staff->id) selected @endif>{{ $staff->name }}</option>
                                @endforeach
                            @endif
                        </select>
                    </div>

                    {{-- Enhanced Date Filters --}}
                    <div>
                        <label for="filter_date_start" class="block text-sm font-semibold text-gray-700 mb-2">Deadline From</label>
                        <input type="date" id="filter_date_start" name="date_start" value="{{ request('date_start') }}" 
                               class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-primary-300 focus:border-primary-500 transition-all duration-200 hover:border-gray-300">
                    </div>
                    </div>

                {{-- Enhanced Action Buttons --}}
                <div class="mt-6 flex flex-wrap gap-3 items-center">
                    <button type="submit" class="btn-primary text-sm px-6 py-3 flex items-center shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 transform">
                        <i class="ri-filter-3-line mr-2" aria-hidden="true"></i>Apply Filters
                    </button>
                    <a href="{{ route('projects.index') }}" 
                       class="px-6 py-3 border-2 border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-400 flex items-center transition-all duration-200 bg-white">
                        <i class="ri-refresh-line mr-2" aria-hidden="true"></i>Reset
                    </a>
                </div>

                {{-- Enhanced Active Filters Display --}}
                <div id="active-filters-display" class="text-sm text-gray-600 mt-4 empty:hidden">
                    {{-- Active filters will be displayed here by JS --}}
                </div>
            </form>
        </div>
    </div>

    {{-- Main Content Area --}}
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

        {{-- Table View --}}
        <div id="table-view" class="view-content">
            <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
            <div class="overflow-x-auto styled-scrollbar">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">ID Project</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Project Details</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Staff & Status</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Timeline & Budget</th>
                                <th scope="col" class="px-6 py-4 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Documents</th>
                                <th scope="col" class="px-6 py-4 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                    </tr>
                </thead>
                        <tbody class="bg-white divide-y divide-gray-100">
                    @forelse ($projects as $project)
                            <tr class="hover:bg-gray-50 transition-all duration-300 ease-in-out project-row group" 
                                data-project-id="{{ $project->id }}" id="project-{{$project->id}}">
                                <td class="px-6 py-5 whitespace-nowrap">
                                    <div class="flex items-center">
                                        <div class="w-10 h-10 bg-gradient-to-br from-primary to-primary-600 rounded-lg flex items-center justify-center mr-3">
                                            <i class="ri-hashtag text-white text-sm"></i>
                                        </div>
                                        <div>
                                            <div class="text-sm font-bold text-gray-800">{{ $project->order_id ?: ('#FALLBACK-' . $project->id) }}</div>
                                            <div class="text-xs text-gray-500">Project ID</div>
                                        </div>
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    <div>
                                        <div class="text-sm font-bold text-gray-800 mb-1">{{ Str::limit($project->project_name, 30) }}</div>
                                        <div class="text-sm text-gray-600 flex items-center">
                                            <i class="ri-user-3-line mr-1 text-gray-400"></i>
                                            {{ $project->client_name ?? 'N/A' }}
                                        </div>
                                        @if($project->notes)
                                        <div class="text-xs text-gray-500 mt-1 italic">{{ Str::limit($project->notes, 40) }}</div>
                                        @endif
                                    </div>
                                </td>
                                <td class="px-6 py-5">
                                    {{-- Staff Assignment --}}
                                    <div class="mb-3">
                            @if($project->staff && $project->staff->isNotEmpty())
                                            <div class="flex -space-x-2 overflow-hidden mb-2">
                                    @foreach($project->staff->take(3) as $staffMember)
                                                    <img class="inline-block h-8 w-8 rounded-full ring-2 ring-white object-cover hover:z-10 hover:scale-110 transition-transform duration-200"
                                             src="{{ $staffMember->profile_photo_path ? Storage::url($staffMember->profile_photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($staffMember->name).'&color=fff&background=6366F1&size=32&font-size=0.45&bold=true' }}"
                                             alt="{{ $staffMember->name }}" title="{{ $staffMember->name }}">
                                    @endforeach
                                    @if($project->staff->count() > 3)
                                                    <span class="inline-flex items-center justify-center h-8 w-8 rounded-full ring-2 ring-white bg-gray-200 text-xs font-medium text-gray-700 hover:bg-gray-300 cursor-pointer transition-colors" title="{{ $project->staff->count() - 3 }} more staff">+{{ $project->staff->count() - 3 }}</span>
                                    @endif
                                </div>
                            @else
                                            <span class="text-xs text-gray-400 italic">Not assigned</span>
                            @endif
                                    </div>
                                    
                                    {{-- Status Badges --}}
                                    <div class="space-y-2">
                                        @php
                                            $statusClasses = [
                                                'Active' => 'bg-green-100 text-green-800 ring-green-200',
                                                'On-going' => 'bg-blue-100 text-blue-800 ring-blue-200',
                                                'Pending' => 'bg-yellow-100 text-yellow-800 ring-yellow-200',
                                                'Draft' => 'bg-gray-100 text-gray-700 ring-gray-200',
                                                'Completed' => 'bg-emerald-100 text-emerald-800 ring-emerald-200',
                                                'Cancelled' => 'bg-red-100 text-red-800 ring-red-200'
                                            ];
                                            $statusClass = $statusClasses[$project->status] ?? 'bg-gray-100 text-gray-700 ring-gray-200';
                                            
                                            $paymentStatusClasses = [
                                                'Fully Paid' => 'bg-green-100 text-green-800 ring-green-200',
                                                'Paid' => 'bg-green-100 text-green-800 ring-green-200',
                                                'DP Paid' => 'bg-sky-100 text-sky-800 ring-sky-200',
                                                'DP' => 'bg-sky-100 text-sky-800 ring-sky-200',
                                                'Pending' => 'bg-yellow-100 text-yellow-800 ring-yellow-200',
                                                'Unpaid' => 'bg-amber-100 text-amber-800 ring-amber-200',
                                                'Overdue' => 'bg-red-100 text-red-800 ring-red-200',
                                                'Refunded' => 'bg-purple-100 text-purple-800 ring-purple-200'
                                            ];
                                            $paymentStatusClass = $paymentStatusClasses[$project->payment_status] ?? 'bg-gray-100 text-gray-700 ring-gray-200';
                            @endphp
                                        <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full ring-1 {{ $statusClass }} hover:scale-105 transition-transform duration-200 cursor-default" title="Project Status">
                                            {{ $project->status }}
                                        </span>
                                        <span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full ring-1 {{ $paymentStatusClass }} hover:scale-105 transition-transform duration-200 cursor-default" title="Payment Status">
                                            {{ $project->payment_status ?? 'N/A' }}
                                        </span>
                                    </div>
                        </td>
                                <td class="px-6 py-5">
                                    <div class="space-y-2">
                                        <div class="text-xs text-gray-500">Deadline</div>
                                        <div class="text-sm font-medium text-gray-700 flex items-center">
                                            <i class="ri-calendar-event-line mr-1 text-gray-400"></i>
                                            {{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('d M Y') : 'N/A' }}
                                        </div>
                                        <div class="text-xs text-gray-500 mt-2">Budget</div>
                                        <div class="text-sm font-bold text-gray-800 budget-cell">
                            @if(!is_null($project->budget) && is_numeric($project->budget))
                                                <span class="text-primary-600">Rp {{ number_format(floatval($project->budget), 0, ',', '.') }}</span>
                                            @else 
                                                <span class="text-gray-400">N/A</span>
                                            @endif
                                        </div>
                                    </div>
                        </td>
                                <td class="px-6 py-5">
                                    <div class="space-y-2">
                                        {{-- POC Document --}}
                                        <div>
                            @if($project->poc)
                                @php $pocUrl = $project->getFilePocUrlAttribute(); @endphp
                                                <a href="{{ $pocUrl ?: '#' }}" {{ $pocUrl ? 'target="_blank"' : '' }} 
                                                   class="text-primary-600 hover:text-primary-700 hover:underline flex items-center space-x-1.5 text-xs {{ !$pocUrl ? 'cursor-not-allowed opacity-70' : '' }}" 
                                                   title="{{ $project->poc }}">
                                                    <i class="ri-{{ $pocUrl ? 'file-text-line' : 'file-forbid-line' }} text-sm text-blue-500" aria-hidden="true"></i>
                                                    <span class="truncate max-w-[80px]">POC</span>
                                                </a>
                                            @else 
                                                <span class="text-xs text-gray-400 italic">No POC</span>
                                            @endif
                                        </div>
                                        
                                        {{-- BAST Document --}}
                                        <div>
                            @if($project->bast)
                                @php $bastUrl = $project->getFileBastUrlAttribute(); @endphp
                                                <a href="{{ $bastUrl ?: '#' }}" {{ $bastUrl ? 'target="_blank"' : '' }} 
                                                   class="text-primary-600 hover:text-primary-700 hover:underline flex items-center space-x-1.5 text-xs {{ !$bastUrl ? 'cursor-not-allowed opacity-70' : '' }}" 
                                                   title="{{ $project->bast }}">
                                                    <i class="ri-{{ $bastUrl ? 'shield-check-line' : 'file-forbid-line' }} text-sm text-green-500" aria-hidden="true"></i>
                                                    <span class="truncate max-w-[80px]">BAST</span>
                                                </a>
                                            @else 
                                                <span class="text-xs text-gray-400 italic">No BAST</span>
                                            @endif
                                        </div>
                                    </div>
                        </td>
                                <td class="px-6 py-5 whitespace-nowrap text-center">
                            <div class="flex justify-center items-center space-x-1">
                                        <button class="text-gray-400 hover:text-blue-600 p-2 rounded-lg hover:bg-blue-100 transition-all duration-200 transform hover:scale-110 view-project-details-button" 
                                                title="View Details" data-project-id="{{ $project->id }}">
                                            <i class="ri-eye-line text-lg" aria-hidden="true"></i>
                                        </button>
                                        <button class="text-gray-400 hover:text-primary p-2 rounded-lg hover:bg-primary-100 transition-all duration-200 transform hover:scale-110 edit-project-button" 
                                                title="Edit Project" data-project-id="{{ $project->id }}">
                                            <i class="ri-edit-box-line text-lg" aria-hidden="true"></i>
                                        </button>
                                        <button class="text-gray-400 hover:text-red-600 p-2 rounded-lg hover:bg-red-100 transition-all duration-200 transform hover:scale-110 delete-project-button" 
                                                title="Delete Project" data-project-id="{{ $project->id }}" data-project-name="{{ $project->project_name }}">
                                            <i class="ri-delete-bin-5-line text-lg" aria-hidden="true"></i>
                                        </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                                <td colspan="6" class="px-6 py-16 text-center">
                            <div class="flex flex-col items-center">
                                        <div class="w-20 h-20 bg-gray-100 rounded-full flex items-center justify-center mb-4">
                                            <i class="ri-folder-open-line text-3xl text-gray-400" aria-hidden="true"></i>
                                        </div>
                                        <h3 class="text-lg font-medium text-gray-600 mb-2">No projects found</h3>
                                        <p class="text-sm text-gray-400 mb-4">Try adjusting your filters or add a new project</p>
                                        <button type="button" id="add-project-from-empty" class="btn-primary text-sm px-4 py-2">
                                            <i class="ri-add-line mr-2" aria-hidden="true"></i>Create New Project
                                </button>
                            </div>
                        </td>
                    </tr>
                    @endforelse
                </tbody>
                </table>
            </div>
                
            @if ($projects->hasPages())
                <div class="bg-gray-50 px-6 py-4 border-t border-gray-200 rounded-b-2xl">
                {{ $projects->appends(request()->query())->links() }}
            </div>
            @endif
            </div>
        </div>

        {{-- Cards View --}}
        <div id="cards-view" class="view-content hidden">
            <div class="grid grid-cols-1 md:grid-cols-2 xl:grid-cols-3 gap-6">
                @forelse ($projects as $project)
                <div class="bg-white rounded-2xl shadow-lg hover:shadow-xl transition-all duration-300 transform hover:-translate-y-1 border border-slate-200 overflow-hidden group">
                    {{-- Card Header --}}
                    <div class="bg-gradient-to-r from-slate-800 to-slate-700 p-6 text-white">
                        <div class="flex items-start justify-between">
                            <div>
                                <h3 class="text-lg font-bold mb-1">{{ Str::limit($project->project_name, 30) }}</h3>
                                <p class="text-slate-300 text-sm">{{ $project->order_id ?: ('#FALLBACK-' . $project->id) }}</p>
                            </div>
                            <div class="flex space-x-1">
                                <button class="text-slate-300 hover:text-white p-1 rounded transition-colors view-project-details-button" data-project-id="{{ $project->id }}">
                                    <i class="ri-eye-line"></i>
                                </button>
                                <button class="text-slate-300 hover:text-white p-1 rounded transition-colors edit-project-button" data-project-id="{{ $project->id }}">
                                    <i class="ri-edit-box-line"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    {{-- Card Body --}}
                    <div class="p-6">
                        <div class="space-y-4">
                            {{-- Client Info --}}
                            <div class="flex items-center">
                                <i class="ri-user-3-line text-slate-400 mr-2"></i>
                                <span class="text-sm text-slate-600">{{ $project->client_name ?? 'N/A' }}</span>
                            </div>

                            {{-- Status Badges --}}
                            <div class="flex flex-wrap gap-2">
                                @php
                                    $statusClasses = [
                                        'Active' => 'bg-green-100 text-green-800 ring-green-200',
                                        'On-going' => 'bg-blue-100 text-blue-800 ring-blue-200',
                                        'Pending' => 'bg-yellow-100 text-yellow-800 ring-yellow-200',
                                        'Draft' => 'bg-slate-100 text-slate-700 ring-slate-200',
                                        'Completed' => 'bg-emerald-100 text-emerald-800 ring-emerald-200',
                                        'Cancelled' => 'bg-red-100 text-red-800 ring-red-200'
                                    ];
                                    $statusClass = $statusClasses[$project->status] ?? 'bg-slate-100 text-slate-700 ring-slate-200';
                                    
                                    $paymentStatusClasses = [
                                        'Fully Paid' => 'bg-green-100 text-green-800 ring-green-200',
                                        'Paid' => 'bg-green-100 text-green-800 ring-green-200',
                                        'DP Paid' => 'bg-sky-100 text-sky-800 ring-sky-200',
                                        'DP' => 'bg-sky-100 text-sky-800 ring-sky-200',
                                        'Pending' => 'bg-yellow-100 text-yellow-800 ring-yellow-200',
                                        'Unpaid' => 'bg-amber-100 text-amber-800 ring-amber-200',
                                        'Overdue' => 'bg-red-100 text-red-800 ring-red-200',
                                        'Refunded' => 'bg-purple-100 text-purple-800 ring-purple-200'
                                    ];
                                    $paymentStatusClass = $paymentStatusClasses[$project->payment_status] ?? 'bg-slate-100 text-slate-700 ring-slate-200';
                                @endphp
                                <span class="px-2 py-1 text-xs font-semibold rounded-full ring-1 {{ $statusClass }}">{{ $project->status }}</span>
                                @if($project->payment_status)
                                <span class="px-2 py-1 text-xs font-semibold rounded-full ring-1 {{ $paymentStatusClass }}">{{ $project->payment_status }}</span>
                                @endif
                            </div>

                            {{-- Staff Assignment --}}
                            @if($project->staff && $project->staff->isNotEmpty())
                            <div class="flex items-center">
                                <i class="ri-team-line text-slate-400 mr-2"></i>
                                <div class="flex -space-x-1">
                                    @foreach($project->staff->take(3) as $staffMember)
                                        <img class="inline-block h-6 w-6 rounded-full ring-2 ring-white object-cover"
                                             src="{{ $staffMember->profile_photo_path ? Storage::url($staffMember->profile_photo_path) : 'https://ui-avatars.com/api/?name='.urlencode($staffMember->name).'&color=fff&background=6366F1&size=24&font-size=0.45&bold=true' }}"
                                             alt="{{ $staffMember->name }}" title="{{ $staffMember->name }}">
                                    @endforeach
                                    @if($project->staff->count() > 3)
                                        <span class="inline-flex items-center justify-center h-6 w-6 rounded-full ring-2 ring-white bg-slate-200 text-xs font-medium text-slate-600">+{{ $project->staff->count() - 3 }}</span>
                                    @endif
                                </div>
                            </div>
                            @endif

                            {{-- Timeline & Budget --}}
                            <div class="grid grid-cols-2 gap-4 pt-4 border-t border-slate-100">
                                <div>
                                    <p class="text-xs text-slate-500 mb-1">Deadline</p>
                                    <p class="text-sm font-medium text-slate-700">{{ $project->end_date ? \Carbon\Carbon::parse($project->end_date)->format('d M Y') : 'N/A' }}</p>
                                </div>
                                <div>
                                    <p class="text-xs text-slate-500 mb-1">Budget</p>
                                    <p class="text-sm font-bold text-primary-600">
                                        @if(!is_null($project->budget) && is_numeric($project->budget))
                                            Rp {{ number_format(floatval($project->budget), 0, ',', '.') }}
                                        @else 
                                            N/A
                                        @endif
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    {{-- Card Footer --}}
                    <div class="bg-slate-50 px-6 py-4 flex justify-between items-center">
                        <div class="flex space-x-2">
                            @if($project->poc)
                                <span class="text-xs bg-blue-100 text-blue-700 px-2 py-1 rounded-full">POC</span>
                            @endif
                            @if($project->bast)
                                <span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">BAST</span>
                            @endif
                        </div>
                        <button class="text-red-400 hover:text-red-600 p-1 rounded transition-colors delete-project-button" 
                                data-project-id="{{ $project->id }}" data-project-name="{{ $project->project_name }}">
                            <i class="ri-delete-bin-5-line"></i>
                        </button>
                    </div>
                </div>
                @empty
                <div class="col-span-full">
                    <div class="text-center py-16">
                        <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mx-auto mb-4">
                            <i class="ri-folder-open-line text-3xl text-slate-400"></i>
                        </div>
                        <h3 class="text-lg font-medium text-slate-600 mb-2">No projects found</h3>
                        <p class="text-sm text-slate-400 mb-4">Try adjusting your filters or add a new project</p>
                        <button type="button" id="add-project-from-empty-cards" class="btn-primary text-sm px-4 py-2">
                            <i class="ri-add-line mr-2"></i>Create New Project
                        </button>
                    </div>
                </div>
                @endforelse
            </div>

            @if ($projects->hasPages())
            <div class="mt-8 flex justify-center">
                {{ $projects->appends(request()->query())->links() }}
            </div>
            @endif
        </div>

        {{-- Enhanced Summary Section --}}
        <div class="mt-12 bg-white rounded-2xl shadow-lg border border-gray-200 p-8">
            <div class="flex items-center justify-between">
                <div>
                    <h2 class="text-2xl font-bold mb-2 text-gray-900">Project Summary</h2>
                    <p class="text-gray-600">
                        Total revenue from completed projects: 
                        <span class="font-bold text-green-600 text-xl ml-2">
                            Rp {{ isset($totalAllCompletedBudget) ? number_format($totalAllCompletedBudget, 0, ',', '.') : '0' }}
                        </span>
                    </p>
                    <p class="text-sm text-gray-500 mt-2 italic">
                @if(collect(request()->query())->except('page')->isNotEmpty())
                            Based on current filter criteria
                @else
                            Based on all projects
                @endif
            </p>
                </div>
                <div class="hidden lg:block">
                    <div class="w-16 h-16 bg-green-100 rounded-full flex items-center justify-center">
                        <i class="ri-money-dollar-circle-line text-2xl text-green-600"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    {{-- Modal Add/Edit Project --}}
    <div id="project-form-modal" class="fixed inset-0 bg-gray-800 hidden items-center justify-center z-50 p-4 animate-fadeIn" style="--tw-bg-opacity: 0.65;">
        <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full mx-auto my-8 max-h-[90vh] flex flex-col transform transition-all duration-300 ease-out scale-95 opacity-0" id="project-modal-inner-content">
            <div class="flex justify-between items-center p-5 border-b border-slate-200 sticky top-0 bg-white z-10 rounded-t-xl">
                <h3 class="text-lg font-semibold text-slate-800" id="project-modal-title">Add New Project</h3>
            </div>
            <form id="project-modal-form" method="POST" action="{{ route('projects.store') }}" class="overflow-y-auto flex-grow styled-scrollbar" enctype="multipart/form-data">
                @csrf
                <input type="hidden" name="_method" id="form-method-input" value="POST">
                <div class="p-5 md:p-6 space-y-5">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-5 gap-y-4">
                        <div class="sm:col-span-2">
                            <label for="project_name_modal" class="block text-sm font-medium text-slate-700 mb-1">Project Name <span class="text-red-500">*</span></label>
                            <input type="text" id="project_name_modal" name="project_name" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-300 focus:border-primary-500 shadow-sm" placeholder="Enter project name" required>
                        </div>
                        <div>
                            <label for="order_id_modal" class="block text-sm font-medium text-slate-700 mb-1">Order ID <span class="text-xs text-slate-500">(Auto if empty)</span></label>
                            <input type="text" id="order_id_modal" name="order_id" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-sm bg-slate-50 focus:ring-2 focus:ring-primary-300 focus:border-primary-500 shadow-sm" placeholder="e.g., DC-001">
                        </div>
                        <div>
                            <label for="client_name_modal" class="block text-sm font-medium text-slate-700 mb-1">Client Name <span class="text-red-500">*</span></label>
                            <input type="text" id="client_name_modal" name="client_name" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-300 focus:border-primary-500 shadow-sm" placeholder="Enter client name" required>
                        </div>
                        <div>
                            <label for="start_date_modal" class="block text-sm font-medium text-slate-700 mb-1">Start Date</label>
                            <input type="date" id="start_date_modal" name="start_date" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-300 focus:border-primary-500 shadow-sm">
                        </div>
                        <div>
                            <label for="end_date_modal" class="block text-sm font-medium text-slate-700 mb-1">Deadline</label>
                            <input type="date" id="end_date_modal" name="end_date" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-300 focus:border-primary-500 shadow-sm">
                        </div>
                        <div>
                            <label for="budget_display_modal" class="block text-sm font-medium text-slate-700 mb-1">Project Price</label>
                            <input type="text" id="budget_display_modal" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-300 focus:border-primary-500 shadow-sm" placeholder="e.g., 70.000">
                            <input type="hidden" id="budget_modal" name="budget">
                        </div>
                         <div>
                            <label for="status_modal" class="block text-sm font-medium text-slate-700 mb-1">Project Status <span class="text-red-500">*</span></label>
                            <select id="status_modal" name="status" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-sm pr-8 focus:ring-2 focus:ring-primary-300 focus:border-primary-500 shadow-sm" required>
                                <option value="Draft">Draft</option>
                                <option value="Pending">Pending</option>
                                <option value="Active">Active</option>
                                <option value="On-going">On-going</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="payment_status_modal" class="block text-sm font-medium text-slate-700 mb-1">Payment Status</label>
                            <select id="payment_status_modal" name="payment_status" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-sm pr-8 focus:ring-2 focus:ring-primary-300 focus:border-primary-500 shadow-sm">
                                <option value="">Select Payment Status</option>
                                <option value="Unpaid">Unpaid</option>
                                <option value="DP">DP (Waiting Payment)</option>
                                <option value="DP Paid">DP Paid</option>
                                <option value="Fully Paid">Fully Paid</option>
                                <option value="Refunded">Refunded</option>
                                <option value="Overdue">Overdue</option>
                            </select>
                        </div>
                         <div class="sm:col-span-2">
                            <label for="assignees_modal" class="block text-sm font-medium text-slate-700 mb-1">Assign Staff (Optional)</label>
                            <select id="assignees_modal" name="assignees[]" multiple class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-300 focus:border-primary-500 shadow-sm min-h-[120px]">
                                @if(isset($staffMembers))
                                    @foreach($staffMembers as $staff)
                                        <option value="{{ $staff->id }}">{{ $staff->name }} ({{ $staff->email }})</option>
                                    @endforeach
                                @endif
                            </select>
                            <p class="text-xs text-slate-500 mt-1.5">Hold Ctrl (or Cmd on Mac) to select multiple staff.</p>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="description_modal" class="block text-sm font-medium text-slate-700 mb-1">Project Description <span class="text-red-500">*</span></label>
                            <textarea id="description_modal" name="description" rows="4" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-300 focus:border-primary-500 shadow-sm" placeholder="Detailed project description..." required></textarea>
                        </div>

                        {{-- Project Requirements Section --}}
                        <div class="sm:col-span-2 pt-5 border-t border-slate-200 mt-3" id="project-requirements-section-container">
                            <div class="flex justify-between items-center mb-2.5">
                                <label class="block text-sm font-medium text-slate-700">Project Requirements/Deliverables <span class="text-red-500">*</span></label>
                                <button type="button" id="add-requirement-btn" class="text-xs bg-primary-50 text-primary-700 hover:bg-primary-100 px-3 py-1.5 rounded-md flex items-center font-medium transition-colors">
                                    <i class="ri-add-circle-line mr-1.5 text-sm" aria-hidden="true"></i> Add Item
                                </button>
                            </div>
                            <div id="project-requirements-container" class="space-y-2.5">
                                {{-- Requirement items will be added here by JS --}}
                            </div>
                            <p class="text-xs text-slate-500 mt-2">Define specific tasks or deliverables for this project. At least one is required.</p>
                        </div>

                        {{-- File Upload Sections --}}
                        <div class="sm:col-span-2 pt-5 border-t border-slate-200 mt-3 space-y-4">
                            <div>
                                <label for="file_poc_upload_modal" class="block text-sm font-medium text-slate-700 mb-1">POC Document (Proof of Concept)</label>
                                <input type="file" id="file_poc_upload_modal" name="file_poc_upload" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 transition-colors cursor-pointer">
                                <div id="current_file_poc_info" class="mt-1.5 text-xs text-slate-500"></div>
                                <label for="file_poc_link_modal" class="mt-2.5 block text-sm font-medium text-slate-700 mb-1">or POC Link</label>
                                <input type="text" id="file_poc_link_modal" name="file_poc_link" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-300 focus:border-primary-500 shadow-sm" placeholder="https://example.com/poc-document">
                                <div class="mt-1.5" id="remove_file_poc_container" style="display:none;">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" id="remove_file_poc_modal" name="remove_file_poc" value="1" class="form-checkbox h-4 w-4 text-primary rounded border-slate-300 focus:ring-primary-300">
                                        <span class="ml-2 text-sm text-slate-600">Remove current POC file/link</span>
                                    </label>
                                </div>
                            </div>
                            <div>
                                <label for="file_bast_upload_modal" class="block text-sm font-medium text-slate-700 mb-1">BAST Document (Berita Acara Serah Terima)</label>
                                <input type="file" id="file_bast_upload_modal" name="file_bast_upload" class="w-full text-sm text-slate-500 file:mr-4 file:py-2 file:px-4 file:rounded-lg file:border-0 file:text-sm file:font-semibold file:bg-primary-50 file:text-primary-700 hover:file:bg-primary-100 transition-colors cursor-pointer">
                                <div id="current_file_bast_info" class="mt-1.5 text-xs text-slate-500"></div>
                                <label for="file_bast_link_modal" class="mt-2.5 block text-sm font-medium text-slate-700 mb-1">or BAST Link</label>
                                <input type="text" id="file_bast_link_modal" name="file_bast_link" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-300 focus:border-primary-500 shadow-sm" placeholder="https://example.com/bast-document">
                                 <div class="mt-1.5" id="remove_file_bast_container" style="display:none;">
                                    <label class="inline-flex items-center">
                                        <input type="checkbox" id="remove_file_bast_modal" name="remove_file_bast" value="1" class="form-checkbox h-4 w-4 text-primary rounded border-slate-300 focus:ring-primary-300">
                                        <span class="ml-2 text-sm text-slate-600">Remove current BAST file/link</span>
                                    </label>
                                </div>
                            </div>
                        </div>
                        <div class="sm:col-span-2">
                            <label for="notes_modal" class="block text-sm font-medium text-slate-700 mb-1">Additional Notes</label>
                            <textarea id="notes_modal" name="notes" rows="3" class="w-full px-3.5 py-2.5 border border-slate-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-300 focus:border-primary-500 shadow-sm" placeholder="Any extra notes, internal memos, or client requests..."></textarea>
                        </div>
                    </div>
                </div>
                <div class="flex justify-end space-x-3 px-5 md:px-6 py-4 bg-slate-50 rounded-b-xl sticky bottom-0 z-10 border-t border-slate-200">
                    <button type="submit" id="save-project-button" class="btn-primary px-4 py-2.5 text-sm font-medium shadow-sm hover:shadow-md">Save Project</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal Delete Confirmation --}}
    <div id="delete-confirm-modal" class="fixed inset-0 bg-gray-800 hidden items-center justify-center z-50 p-4 animate-fadeIn" style="--tw-bg-opacity: 0.65;">
        <div class="bg-white rounded-xl shadow-2xl max-w-md w-full mx-auto transform transition-all duration-300 ease-out scale-95 opacity-0" id="delete-modal-inner-content">
            <div class="p-6 md:p-8 text-center">
                <div class="flex items-center justify-center w-16 h-16 mx-auto bg-red-100 rounded-full mb-5">
                    <i class="ri-error-warning-fill text-4xl text-red-500" aria-hidden="true"></i>
                </div>
                <h3 class="text-xl font-semibold text-slate-800">Delete Project</h3>
                <p class="mt-2.5 text-sm text-slate-500">Are you sure you want to delete project <strong id="project-to-delete-name-modal" class="font-medium text-slate-700"></strong>? This action cannot be undone and all associated data will be lost.</p>
            </div>
            <form id="delete-project-form" method="POST" action="">
                @csrf
                @method('DELETE')
                <div class="flex justify-center space-x-3 px-6 py-5 bg-slate-50 rounded-b-xl border-t border-slate-200">
                    <button type="button" id="cancel-delete-button" class="px-5 py-2.5 border border-slate-300 rounded-lg text-sm font-medium text-slate-700 hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-slate-400 transition-colors shadow-sm">Cancel</button>
                    <button type="submit" class="px-5 py-2.5 bg-red-600 text-white rounded-lg text-sm font-medium hover:bg-red-700 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-red-500 transition-colors shadow-sm">Delete Project</button>
                </div>
            </form>
        </div>
    </div>

    {{-- Modal View Project Details --}}
    <div id="view-project-details-modal" class="fixed inset-0 bg-gray-800 hidden items-center justify-center z-50 p-4 animate-fadeIn" style="--tw-bg-opacity: 0.75;">
        <div class="bg-gradient-to-br from-slate-50 to-gray-100 rounded-xl shadow-2xl max-w-2xl w-full mx-auto my-8 max-h-[90vh] flex flex-col transform transition-all duration-300 ease-out scale-95 opacity-0"
             id="view-project-modal-content">
            <div class="flex justify-between items-center p-5 md:p-6 border-b border-gray-300">
                <div class="flex items-center space-x-3.5">
                    <div class="p-2.5 bg-primary/10 rounded-lg">
                        <i class="ri-briefcase-4-line text-primary text-2xl" aria-hidden="true"></i>
                    </div>
                    <div>
                        <h3 class="text-xl font-semibold text-gray-800" id="view-project-modal-title-name">Project Details</h3>
                        <p class="text-xs text-gray-500" id="view-project-modal-order-id">Order ID: -</p>
                    </div>
                </div>
            </div>
            <div class="p-5 md:p-8 overflow-y-auto flex-grow space-y-6 styled-scrollbar">
                {{-- Basic Info --}}
                <div class="grid grid-cols-1 md:grid-cols-2 gap-x-8 gap-y-5">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Project Name</label>
                        <p id="view-project-name" class="mt-1 text-lg font-semibold text-gray-700">-</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Client Name</label>
                        <p id="view-client-name" class="mt-1 text-md text-gray-700">-</p>
                    </div>
                </div>
                {{-- Dates & Budget --}}
                <div class="grid grid-cols-1 sm:grid-cols-3 gap-x-6 gap-y-5 border-t border-b border-gray-200 py-5">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Start Date</label>
                        <p id="view-start-date" class="mt-1 text-sm text-gray-700 flex items-center"><i class="ri-calendar-2-line mr-2 text-gray-400" aria-hidden="true"></i>-</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Deadline</label>
                        <p id="view-end-date" class="mt-1 text-sm text-gray-700 flex items-center"><i class="ri-calendar-event-line mr-2 text-gray-400" aria-hidden="true"></i>-</p>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Project Price</label>
                        <p id="view-budget" class="mt-1 text-sm font-semibold text-primary flex items-center"><i class="ri-money-dollar-circle-line mr-2 text-gray-400" aria-hidden="true"></i>-</p>
                    </div>
                </div>
                {{-- Statuses --}}
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-x-6 gap-y-5">
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Project Status</label>
                        <div id="view-status-badge-container" class="mt-1">-</div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Payment Status</label>
                        <div id="view-payment-status-badge-container" class="mt-1">-</div>
                    </div>
                </div>
                 {{-- Staff --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Assigned Staff</label>
                    <div id="view-assignees-list" class="flex flex-wrap gap-2.5">
                        <p class="text-sm text-gray-500 italic">No staff assigned.</p>
                    </div>
                </div>
                 {{-- Description --}}
                <div>
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Description</label>
                    <div id="view-description" class="mt-1 text-sm text-gray-700 leading-relaxed bg-slate-100 p-3.5 rounded-md prose prose-sm max-w-none">-</div>
                </div>
                {{-- Requirements --}}
                <div id="view-project-requirements-section">
                    <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider mb-2">Project Requirements / Deliverables</label>
                    <ul id="view-project-requirements-list" class="space-y-1.5 text-sm text-gray-700 bg-slate-100 p-3.5 rounded-md">
                        <li class="italic text-slate-500">No requirements listed.</li>
                    </ul>
                </div>
                {{-- Files & Notes --}}
                <div class="space-y-5 pt-3 border-t border-gray-200">
                     <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">POC Document</label>
                        <div id="view-file-poc" class="mt-1 text-sm"></div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">BAST Document</label>
                        <div id="view-file-bast" class="mt-1 text-sm"></div>
                    </div>
                    <div>
                        <label class="block text-xs font-medium text-gray-500 uppercase tracking-wider">Additional Notes</label>
                        <div id="view-notes" class="mt-1 text-sm text-gray-700 leading-relaxed bg-slate-100 p-3.5 rounded-md prose prose-sm max-w-none">-</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <style>
        /* Enhanced Header Section */
        .header-section {
            position: -webkit-sticky;
            position: sticky;
            top: 0;
            z-index: 20;
        }

        .filter-section {
            position: -webkit-sticky;
            position: sticky;
            z-index: 19;
        }

        /* View Toggle Styles */
        .view-toggle-btn {
            background: transparent;
            color: #64748b;
        }
        .view-toggle-btn.active {
            background: white;
            color: #6366f1;
            box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1);
        }

        /* Enhanced Button Styles */
        .btn-primary {
            background: linear-gradient(135deg, #6366f1 0%, #4f46e5 100%);
            color: white;
            border: none;
            border-radius: 0.75rem;
            font-weight: 600;
            position: relative;
            overflow: hidden;
        }
        .btn-primary:hover {
            background: linear-gradient(135deg, #4f46e5 0%, #4338ca 100%);
            transform: translateY(-1px);
        }
        .btn-primary:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 100%;
            background: linear-gradient(90deg, transparent, rgba(255,255,255,0.2), transparent);
            transition: left 0.5s;
        }
        .btn-primary:hover:before {
            left: 100%;
        }

        /* Enhanced Animations */
        @keyframes slideInDown {
            from {
            opacity: 0;
                transform: translateY(-30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        .animate-slideInDown {
            animation: slideInDown 0.4s ease-out;
        }

        @keyframes fadeInUpSm {
            from { 
                opacity: 0; 
                transform: translateY(-10px) scale(0.95); 
            }
            to { 
                opacity: 1; 
                transform: translateY(0) scale(1); 
            }
        }
        .animate-fadeInUpSm { 
            animation: fadeInUpSm 0.2s ease-out forwards; 
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }
        .animate-fadeIn { 
            animation: fadeIn 0.3s ease-out forwards; 
        }

        /* Enhanced Scrollbar */
        .styled-scrollbar::-webkit-scrollbar { 
            width: 8px; 
            height: 8px; 
        }
        .styled-scrollbar::-webkit-scrollbar-track { 
            background: #f1f5f9; 
            border-radius: 10px; 
        }
        .styled-scrollbar::-webkit-scrollbar-thumb { 
            background: linear-gradient(135deg, #cbd5e1, #94a3b8);
            border-radius: 10px; 
            border: 2px solid #f1f5f9; 
        }
        .styled-scrollbar::-webkit-scrollbar-thumb:hover { 
            background: linear-gradient(135deg, #94a3b8, #64748b);
        }

        /* Hover Effects */
        .hover-lift {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
        }
        .hover-lift:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
        }

        /* Tooltip Styles */
        [title]:hover:after {
            content: attr(title);
            position: absolute;
            bottom: 100%;
            left: 50%;
            transform: translateX(-50%);
            background: #1e293b;
            color: white;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            white-space: nowrap;
            z-index: 1000;
            animation: fadeIn 0.2s ease-out;
        }

        /* Enhanced Focus States */
        .focus-enhanced:focus {
            outline: none;
            ring: 2px;
            ring-color: #6366f1;
            ring-opacity: 0.5;
            border-color: #6366f1;
        }

        /* Loading States */
        .loading {
            position: relative;
            pointer-events: none;
        }
        .loading:after {
            content: '';
            position: absolute;
            top: 50%;
            left: 50%;
            width: 16px;
            height: 16px;
            margin: -8px 0 0 -8px;
            border: 2px solid #ffffff;
            border-radius: 50%;
            border-top-color: transparent;
            animation: spin 1s linear infinite;
        }

        @keyframes spin {
            to { transform: rotate(360deg); }
        }

        /* Progress Bar Styles */
        .progress-bar {
            background: linear-gradient(90deg, #10b981 0%, #059669 100%);
            height: 4px;
            border-radius: 2px;
            transition: width 0.3s ease;
        }

        /* Card Enhancements */
        .project-card {
            transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
            position: relative;
            overflow: hidden;
        }
        .project-card:before {
            content: '';
            position: absolute;
            top: 0;
            left: -100%;
            width: 100%;
            height: 2px;
            background: linear-gradient(90deg, #6366f1, #8b5cf6, #ec4899);
            transition: left 0.5s ease;
        }
        .project-card:hover:before {
            left: 0;
        }

        /* Enhanced Validation Styles */
        .djokihub-validation-error-field {
            border-color: #ef4444 !important;
            box-shadow: 0 0 0 1px #ef4444 !important;
            animation: shake 0.5s ease-in-out;
        }

        @keyframes shake {
            0%, 100% { transform: translateX(0); }
            25% { transform: translateX(-5px); }
            75% { transform: translateX(5px); }
        }

        .djokihub-validation-error-text {
            color: #b91c1c;
            font-size: 0.75rem;
            margin-top: 0.25rem;
            animation: fadeIn 0.3s ease-out;
        }

        .djokihub-validation-error-outline {
            border: 2px solid #ef4444 !important;
            padding: 0.75rem;
            border-radius: 0.5rem;
            background-color: #fef2f2;
            animation: pulse 1s ease-in-out;
        }

        @keyframes pulse {
            0%, 100% { opacity: 1; }
            50% { opacity: 0.8; }
        }

        /* Dark Mode Support */
        @media (prefers-color-scheme: dark) {
            .dark-mode-support {
                background: #1e293b;
                color: #f1f5f9;
            }
        }

        /* Print Styles */
        @media print {
            .no-print {
                display: none !important;
            }
            .print-friendly {
                background: white !important;
                color: black !important;
            }
        }
    </style>
@endsection

@push('scripts')
<script id="enhanced-project-management-scripts">
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced initialization
    initializeEnhancements();
    setupViewToggle();
    setupEnhancedAnimations();
    setupAccessibility();
    setupTooltips();
    setupProjectModals(); // Add this new function
    
    function initializeEnhancements() {
    const headerSection = document.querySelector('.header-section');
    const filterSection = document.querySelector('.filter-section');

    function setFilterSectionTop() {
        if (headerSection && filterSection) {
            const headerHeight = headerSection.offsetHeight;
            filterSection.style.top = headerHeight + 'px';
        }
    }

    setFilterSectionTop();
    window.addEventListener('resize', setFilterSectionTop);
    }

    // View Toggle Functionality
    function setupViewToggle() {
        const tableViewBtn = document.getElementById('table-view-btn');
        const cardsViewBtn = document.getElementById('cards-view-btn');
        const tableView = document.getElementById('table-view');
        const cardsView = document.getElementById('cards-view');

        function switchView(view) {
            // Update buttons
            document.querySelectorAll('.view-toggle-btn').forEach(btn => {
                btn.classList.remove('active');
            });
            
            // Update views
            if (view === 'table') {
                tableViewBtn.classList.add('active');
                tableView.classList.remove('hidden');
                cardsView.classList.add('hidden');
                localStorage.setItem('preferredView', 'table');
            } else {
                cardsViewBtn.classList.add('active');
                cardsView.classList.remove('hidden');
                tableView.classList.add('hidden');
                localStorage.setItem('preferredView', 'cards');
            }
        }

        // Event listeners
        if (tableViewBtn) {
            tableViewBtn.addEventListener('click', () => switchView('table'));
        }
        if (cardsViewBtn) {
            cardsViewBtn.addEventListener('click', () => switchView('cards'));
        }

        // Load saved preference
        const savedView = localStorage.getItem('preferredView') || 'table';
        switchView(savedView);
    }

    // Enhanced Animations
    function setupEnhancedAnimations() {
        // Intersection Observer for scroll animations
        const observerOptions = {
            threshold: 0.1,
            rootMargin: '0px 0px -50px 0px'
        };

        const observer = new IntersectionObserver((entries) => {
            entries.forEach(entry => {
                if (entry.isIntersecting) {
                    entry.target.style.opacity = '1';
                    entry.target.style.transform = 'translateY(0)';
                }
            });
        }, observerOptions);

        // Observe elements for animations
        document.querySelectorAll('.project-row, .project-card').forEach(el => {
            el.style.opacity = '0';
            el.style.transform = 'translateY(20px)';
            el.style.transition = 'opacity 0.5s ease, transform 0.5s ease';
            observer.observe(el);
        });
    }

    // Enhanced Accessibility
    function setupAccessibility() {
        // Tab navigation enhancement
        document.addEventListener('keydown', function(e) {
            if (e.key === 'Tab') {
                const focusableElements = document.querySelectorAll(
                    'button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])'
                );
                
                // Add visual focus indicators
                focusableElements.forEach(el => {
                    el.addEventListener('focus', function() {
                        this.classList.add('focus-enhanced');
                    });
                    el.addEventListener('blur', function() {
                        this.classList.remove('focus-enhanced');
                    });
                });
            }
        });

        // ARIA enhancements
        document.querySelectorAll('[data-tooltip]').forEach(el => {
            el.setAttribute('aria-label', el.dataset.tooltip);
        });
    }

    // Enhanced Tooltips
    function setupTooltips() {
        const tooltip = document.createElement('div');
        tooltip.className = 'fixed bg-gray-800 text-white px-2 py-1 rounded text-sm pointer-events-none z-50 opacity-0 transition-opacity duration-200';
        document.body.appendChild(tooltip);

        document.querySelectorAll('[title]').forEach(el => {
            el.addEventListener('mouseenter', function(e) {
                const title = this.getAttribute('title');
                this.setAttribute('data-original-title', title);
                this.removeAttribute('title');
                
                tooltip.textContent = title;
                tooltip.style.opacity = '1';
                
                const rect = this.getBoundingClientRect();
                tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
                tooltip.style.top = rect.top - tooltip.offsetHeight - 5 + 'px';
            });

            el.addEventListener('mouseleave', function() {
                tooltip.style.opacity = '0';
                const originalTitle = this.getAttribute('data-original-title');
                if (originalTitle) {
                    this.setAttribute('title', originalTitle);
                    this.removeAttribute('data-original-title');
                }
            });
        });
    }

    // Setup Project Modals - THIS IS THE MISSING FUNCTIONALITY
    function setupProjectModals() {
        console.log('Setting up project modals...');
        
        // Modal elements
        const projectFormModal = document.getElementById('project-form-modal');
        const projectFormModalContent = document.getElementById('project-modal-inner-content');
        const viewProjectModal = document.getElementById('view-project-details-modal');
    const viewProjectModalContent = document.getElementById('view-project-modal-content');
        const deleteConfirmModal = document.getElementById('delete-confirm-modal');
        const deleteConfirmModalContent = document.getElementById('delete-modal-inner-content');
        
        // Form elements
        const projectForm = document.getElementById('project-modal-form');
        const deleteForm = document.getElementById('delete-project-form');

        // Project action buttons
        const addProjectButton = document.getElementById('add-project-button');
        const addProjectFromEmpty = document.getElementById('add-project-from-empty');
        const addProjectFromEmptyCards = document.getElementById('add-project-from-empty-cards');

        // Close buttons
        const closeProjectModal = document.getElementById('close-project-modal-button');
        const cancelProjectModal = document.getElementById('cancel-project-modal-button');
        const closeViewModal = document.getElementById('close-view-project-modal-button');
        const secondaryCloseViewModal = document.getElementById('secondary-close-view-project-modal-button');
        const cancelDeleteButton = document.getElementById('cancel-delete-button');

        // Add Project Modal
        function openAddProjectModal() {
            console.log('Opening add project modal...');
            if (projectForm) {
                projectForm.reset();
                projectForm.action = "{{ route('projects.store') }}";
                document.getElementById('form-method-input').value = 'POST';
                document.getElementById('project-modal-title').textContent = 'Add New Project';
                
                // Clear any existing data
                clearProjectForm();
                
                openModal(projectFormModal, projectFormModalContent);
            }
        }

        // Event listeners for add project buttons
        if (addProjectButton) {
            addProjectButton.addEventListener('click', openAddProjectModal);
        }
        if (addProjectFromEmpty) {
            addProjectFromEmpty.addEventListener('click', openAddProjectModal);
        }
        if (addProjectFromEmptyCards) {
            addProjectFromEmptyCards.addEventListener('click', openAddProjectModal);
        }

        // Close modal event listeners only for delete modal
        if (cancelDeleteButton) {
            cancelDeleteButton.addEventListener('click', (e) => {
                e.preventDefault();
                e.stopPropagation();
                console.log('Delete modal cancel button clicked');
                closeModal(deleteConfirmModal, deleteConfirmModalContent);
            });
        }

        // Enhanced modal close functionality
        // Add click outside modal to close for all modals
        [projectFormModal, viewProjectModal, deleteConfirmModal].forEach(modal => {
            if (modal) {
                modal.addEventListener('click', function(e) {
                    // Close if clicking on the backdrop (modal itself, not its content)
                    if (e.target === modal) {
                        const innerContent = modal.querySelector('.bg-white, .bg-gradient-to-br');
                        if (innerContent) {
                            console.log('Clicked outside modal, closing:', modal.id);
                            closeModal(modal, innerContent);
                        }
                    }
                });
            }
        });

        // Setup modal close buttons to ensure they work
        setupModalCloseButtons();

        // Event delegation for project action buttons
        document.addEventListener('click', function(e) {
            const target = e.target.closest('button');
            if (!target) return;

            // View project details
            if (target.classList.contains('view-project-details-button')) {
                const projectId = target.getAttribute('data-project-id');
                if (projectId) {
                    console.log('View project clicked:', projectId);
                    fetchAndShowProjectDetails(projectId);
                }
            }

            // Edit project
            if (target.classList.contains('edit-project-button')) {
                const projectId = target.getAttribute('data-project-id');
                if (projectId) {
                    console.log('Edit project clicked:', projectId);
                    fetchAndEditProject(projectId);
                }
            }

            // Delete project
            if (target.classList.contains('delete-project-button')) {
                const projectId = target.getAttribute('data-project-id');
                const projectName = target.getAttribute('data-project-name');
                if (projectId) {
                    console.log('Delete project clicked:', projectId);
                    showDeleteConfirmation(projectId, projectName);
                }
            }
        });

        // Fetch and show project details
        function fetchAndShowProjectDetails(projectId) {
            console.log('Fetching project details for ID:', projectId);
            
            // Show loading in view modal
            if (viewProjectModal && viewProjectModalContent) {
                openModal(viewProjectModal, viewProjectModalContent, () => {
                    // Fetch project data
                    fetch(`{{ url('projects') }}/${projectId}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(project => {
                        console.log('Project data received:', project);
                        populateViewModal(project);
                    })
                    .catch(error => {
                        console.error('Error fetching project:', error);
                        closeModal(viewProjectModal, viewProjectModalContent);
                        showToast('Failed to load project details: ' + error.message, 'error');
                    });
            });
        }
    }

        // Fetch and edit project
        function fetchAndEditProject(projectId) {
            console.log('Fetching project data for editing:', projectId);
            
            fetch(`{{ url('projects') }}/${projectId}/edit`, {
                headers: {
                    'Accept': 'application/json',
                    'X-Requested-With': 'XMLHttpRequest'
                }
            })
            .then(response => {
                console.log('Edit response status:', response.status);
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                return response.json();
            })
            .then(data => {
                console.log('Project edit data received:', data);
                
                // Handle different response formats from backend
                let project = null;
                if (data.project) {
                    // Format: {project: {...}, assignedStaffIds: [...]}
                    project = data.project;
                } else if (data.id) {
                    // Format: direct project object
                    project = data;
                } else {
                    console.warn('Unexpected data format:', data);
                    project = data;
                }
                
                console.log('Processed project object:', project);
                console.log('Project properties:', {
                    id: project?.id,
                    project_name: project?.project_name,
                    client_name: project?.client_name,
                    status: project?.status,
                    budget: project?.budget,
                    description: project?.description
                });
                
                // If project data seems empty or invalid, try alternative approach
                if (!project || !project.project_name || !project.id) {
                    console.warn('Project data appears empty or invalid, fetching via show endpoint instead...');
                    
                    // Try the show endpoint instead
                    fetch(`{{ url('projects') }}/${projectId}`, {
                        headers: {
                            'Accept': 'application/json',
                            'X-Requested-With': 'XMLHttpRequest'
                        }
                    })
                    .then(response => {
                        if (!response.ok) {
                            throw new Error(`HTTP error! status: ${response.status}`);
                        }
                        return response.json();
                    })
                    .then(showProject => {
                        console.log('Project show data received:', showProject);
                        populateEditModal(showProject);
                        openModal(projectFormModal, projectFormModalContent);
                    })
                    .catch(error => {
                        console.error('Error fetching project via show:', error);
                        showToast('Failed to load project for editing: ' + error.message, 'error');
                    });
        } else {
                    populateEditModal(project);
                    openModal(projectFormModal, projectFormModalContent);
                }
            })
            .catch(error => {
                console.error('Error fetching project for edit:', error);
                showToast('Failed to load project for editing: ' + error.message, 'error');
            });
        }

        // Show delete confirmation
        function showDeleteConfirmation(projectId, projectName) {
            const projectNameElement = document.getElementById('project-to-delete-name-modal');
            if (projectNameElement) {
                projectNameElement.textContent = projectName || 'this project';
            }
            
            if (deleteForm) {
                deleteForm.action = `{{ url('projects') }}/${projectId}`;
            }
            
            openModal(deleteConfirmModal, deleteConfirmModalContent);
        }

        // Populate view modal with project data
        function populateViewModal(project) {
            const elements = {
                'view-project-modal-title-name': project.project_name || 'Unknown Project',
                'view-project-modal-order-id': `Order ID: ${project.order_id || '#FALLBACK-' + project.id}`,
                'view-project-name': project.project_name || '-',
                'view-client-name': project.client_name || '-',
                'view-start-date': project.start_date ? formatDate(project.start_date) : '-',
                'view-end-date': project.end_date ? formatDate(project.end_date) : '-',
                'view-budget': project.budget ? `Rp ${numberFormat(project.budget)}` : '-',
                'view-description': project.description || '-',
                'view-notes': project.notes || '-'
            };

            Object.entries(elements).forEach(([id, content]) => {
                const element = document.getElementById(id);
                if (element) {
                    element.textContent = content;
                }
            });

            // Populate status badges
            populateStatusBadges(project);

            // Populate assignees
            populateAssignees(project);

            // Populate requirements - FIX: Handle requirements as objects
            populateRequirements(project);

            // Populate file links
            populateFileLinks(project);
        }

        // Populate edit modal with project data
        function populateEditModal(project) {
            console.log('Populating edit modal with project:', project);
            console.log('Project type:', typeof project);
            console.log('Project keys:', Object.keys(project || {}));
            console.log('Full project object:', JSON.stringify(project, null, 2));
            
            // Set modal title with safe check
            const modalTitle = document.getElementById('project-modal-title');
            if (modalTitle) {
                const projectName = project.project_name || project.name || 'Unknown Project';
                modalTitle.textContent = `Edit Project: ${projectName}`;
                console.log('Modal title set to:', modalTitle.textContent);
            }
            
            // Set form method and action
            const methodInput = document.getElementById('form-method-input');
            if (methodInput) {
                methodInput.value = 'PUT';
            }
            
            if (projectForm) {
                projectForm.action = `{{ url('projects') }}/${project.id}`;
            }

            // Helper function to convert ISO date to YYYY-MM-DD
            function formatDateForInput(dateString) {
                if (!dateString) return '';
                const date = new Date(dateString);
                if (isNaN(date.getTime())) return '';
                return date.toISOString().split('T')[0];
            }

            // Populate form fields with safe checks and multiple property attempts
            const fields = {
                'project_name_modal': project.project_name || project.name || '',
                'order_id_modal': project.order_id || '',
                'client_name_modal': project.client_name || '',
                'start_date_modal': formatDateForInput(project.start_date),
                'end_date_modal': formatDateForInput(project.end_date),
                'budget_display_modal': project.budget ? numberFormat(project.budget) : '',
                'status_modal': project.status || 'Draft',
                'payment_status_modal': project.payment_status || '',
                'description_modal': project.description || '',
                'notes_modal': project.notes || '',
                'file_poc_link_modal': project.poc_link || project.file_poc_link || '',
                'file_bast_link_modal': project.bast_link || project.file_bast_link || ''
            };

            Object.entries(fields).forEach(([id, value]) => {
                const element = document.getElementById(id);
                if (element) {
                    element.value = value;
                    console.log(`Set ${id} to:`, value);
        } else {
                    console.warn(`Element not found: ${id}`);
                }
            });

            // Handle budget hidden field
            const budgetHidden = document.getElementById('budget_modal');
            if (budgetHidden && project.budget) {
                budgetHidden.value = project.budget;
            }

            // Populate assignees if available
            const assigneesSelect = document.getElementById('assignees_modal');
            if (assigneesSelect && project.staff) {
                console.log('Populating assignees. Project staff:', project.staff);
                Array.from(assigneesSelect.options).forEach(option => {
                    const staffIds = project.staff.map(s => s.id.toString());
                    option.selected = staffIds.includes(option.value);
                    if (option.selected) {
                        console.log('Selected staff option:', option.text, 'ID:', option.value);
                    }
                });
                } else {
                console.log('No assignees select element or no staff data');
            }

            // Handle requirements if they exist - FIX: Handle requirements as objects
            const requirementsContainer = document.getElementById('project-requirements-container');
            if (requirementsContainer) {
                console.log('Requirements container found. Project requirements:', project.requirements);
                
                if (project.requirements && project.requirements.length > 0) {
                    requirementsContainer.innerHTML = '';
                    project.requirements.forEach((requirement, index) => {
                        // FIX: requirement is an object, extract description
                        const description = typeof requirement === 'object' ? requirement.description : requirement;
                        console.log(`Adding requirement ${index}:`, description);
                        addRequirementFieldWithValue(description || '');
                    });
                } else {
                    // If no requirements, add one empty field
                    console.log('No requirements found, adding empty field');
                    requirementsContainer.innerHTML = '';
                    addRequirementField();
                }
            } else {
                console.warn('Requirements container not found!');
            }

            // Handle file info display
            if (project.poc || project.file_poc) {
                console.log('POC file found:', project.poc || project.file_poc);
                const pocInfoEl = document.getElementById('current_file_poc_info');
                if (pocInfoEl) {
                    pocInfoEl.innerHTML = `<p class="text-xs text-slate-500">Current file: ${project.poc || project.file_poc}</p>`;
                }
                const removePocContainer = document.getElementById('remove_file_poc_container');
                if (removePocContainer) {
                    removePocContainer.style.display = 'block';
                }
            } else {
                console.log('No POC file found');
            }

            if (project.bast || project.file_bast) {
                console.log('BAST file found:', project.bast || project.file_bast);
                const bastInfoEl = document.getElementById('current_file_bast_info');
                if (bastInfoEl) {
                    bastInfoEl.innerHTML = `<p class="text-xs text-slate-500">Current file: ${project.bast || project.file_bast}</p>`;
                }
                const removeBastContainer = document.getElementById('remove_file_bast_container');
                if (removeBastContainer) {
                    removeBastContainer.style.display = 'block';
                }
            } else {
                console.log('No BAST file found');
            }
            
            console.log('Edit modal population complete');
        }

        // Add requirement field with value (for edit mode)
        function addRequirementFieldWithValue(value = '') {
            const container = document.getElementById('project-requirements-container');
            if (!container) return;

            const index = container.children.length;
            const div = document.createElement('div');
            div.className = 'flex items-center space-x-2 requirement-item';
            div.innerHTML = `
                <input type="text" name="requirements[${index}][description]" value="${value}" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-300 focus:border-primary-500" placeholder="Enter requirement..." required>
                <button type="button" class="text-red-500 hover:text-red-700 p-1 rounded remove-requirement-btn" title="Remove requirement">
                    <i class="ri-close-circle-line text-lg"></i>
                </button>
            `;
            
            container.appendChild(div);

            // Add remove functionality
            div.querySelector('.remove-requirement-btn').addEventListener('click', () => {
                div.remove();
                // Re-index remaining requirements
                reindexRequirements();
                // Ensure at least one requirement field
                if (container.children.length === 0) {
                    addRequirementField();
                }
            });
        }

        // Re-index requirements after removal
        function reindexRequirements() {
            const container = document.getElementById('project-requirements-container');
            if (!container) return;

            Array.from(container.children).forEach((item, index) => {
                const input = item.querySelector('input[name*="requirements"]');
                if (input) {
                    input.name = `requirements[${index}][description]`;
                }
            });
        }

        // Helper functions
        function populateStatusBadges(project) {
            const statusContainer = document.getElementById('view-status-badge-container');
            const paymentContainer = document.getElementById('view-payment-status-badge-container');
            
            if (statusContainer) {
                statusContainer.innerHTML = createStatusBadge(project.status);
            }
            if (paymentContainer) {
                paymentContainer.innerHTML = createStatusBadge(project.payment_status, 'payment');
            }
        }

        function populateAssignees(project) {
            const container = document.getElementById('view-assignees-list');
            if (!container) return;

            if (project.staff && project.staff.length > 0) {
                container.innerHTML = project.staff.map(staff => 
                    `<div class="flex items-center space-x-2 bg-gray-100 rounded-lg px-3 py-2">
                        <img src="${staff.profile_photo_url || getAvatarUrl(staff.name)}" class="w-8 h-8 rounded-full object-cover" alt="${staff.name}">
                        <span class="text-sm font-medium text-gray-700">${staff.name}</span>
                        <span class="text-xs text-gray-500">(${staff.email})</span>
                    </div>`
                ).join('');
            } else {
                container.innerHTML = '<p class="text-sm text-gray-500 italic">No staff assigned.</p>';
            }
        }

        function populateRequirements(project) {
            const container = document.getElementById('view-project-requirements-list');
            if (!container) return;

            console.log('Populating requirements for view modal. Requirements:', project.requirements);

            if (project.requirements && project.requirements.length > 0) {
                container.innerHTML = project.requirements.map((req, index) => {
                    // FIX: Handle requirements as objects
                    const description = typeof req === 'object' ? req.description : req;
                    const isCompleted = typeof req === 'object' ? req.is_completed : false;
                    const iconClass = isCompleted ? 'ri-checkbox-circle-fill text-green-500' : 'ri-checkbox-circle-line text-primary';
                    
                    console.log(`Requirement ${index}:`, {
                        description,
                        isCompleted,
                        originalReq: req
                    });
                    
                    return `<li class="flex items-center space-x-2">
                        <i class="${iconClass}"></i>
                        <span class="${isCompleted ? 'line-through text-gray-500' : ''}">${description}</span>
                        ${isCompleted ? '<span class="text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full ml-auto">Completed</span>' : ''}
                    </li>`;
                }).join('');
            } else {
                console.log('No requirements found for view modal');
                container.innerHTML = '<li class="italic text-gray-500">No requirements listed.</li>';
            }
        }

        function populateFileLinks(project) {
            const pocContainer = document.getElementById('view-file-poc');
            const bastContainer = document.getElementById('view-file-bast');
            
            if (pocContainer) {
                pocContainer.innerHTML = project.file_poc_url || project.poc_url ? 
                    `<a href="${project.file_poc_url || project.poc_url}" target="_blank" class="text-primary-600 hover:underline flex items-center space-x-1">
                        <i class="ri-file-text-line text-blue-500"></i><span>View POC Document</span>
                    </a>` : 
                    '<span class="text-gray-500 italic">No POC document</span>';
            }
            
            if (bastContainer) {
                bastContainer.innerHTML = project.file_bast_url || project.bast_url ? 
                    `<a href="${project.file_bast_url || project.bast_url}" target="_blank" class="text-primary-600 hover:underline flex items-center space-x-1">
                        <i class="ri-shield-check-line text-green-500"></i><span>View BAST Document</span>
                    </a>` : 
                    '<span class="text-gray-500 italic">No BAST document</span>';
            }
        }

        function clearProjectForm() {
            // Clear all requirements
            const requirementsContainer = document.getElementById('project-requirements-container');
            if (requirementsContainer) {
                requirementsContainer.innerHTML = '';
                addRequirementField(); // Add one default field
            }

            // Clear file info
            ['current_file_poc_info', 'current_file_bast_info'].forEach(id => {
                const element = document.getElementById(id);
                if (element) element.innerHTML = '';
            });

            // Hide remove checkboxes
            ['remove_file_poc_container', 'remove_file_bast_container'].forEach(id => {
                const element = document.getElementById(id);
                if (element) element.style.display = 'none';
            });
        }

        function addRequirementField() {
            const container = document.getElementById('project-requirements-container');
            if (!container) return;

            const index = container.children.length;
            const div = document.createElement('div');
            div.className = 'flex items-center space-x-2 requirement-item';
            div.innerHTML = `
                <input type="text" name="requirements[${index}][description]" class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-2 focus:ring-primary-300 focus:border-primary-500" placeholder="Enter requirement..." required>
                <button type="button" class="text-red-500 hover:text-red-700 p-1 rounded remove-requirement-btn" title="Remove requirement">
                    <i class="ri-close-circle-line text-lg"></i>
                </button>
            `;
            
            container.appendChild(div);

            // Add remove functionality
            div.querySelector('.remove-requirement-btn').addEventListener('click', () => {
                div.remove();
                // Re-index remaining requirements
                reindexRequirements();
                // Ensure at least one requirement field
                if (container.children.length === 0) {
                    addRequirementField();
                }
            });
        }

        // Add requirement button
        const addRequirementBtn = document.getElementById('add-requirement-btn');
        if (addRequirementBtn) {
            addRequirementBtn.addEventListener('click', addRequirementField);
        }

        // Initialize with one requirement field
        addRequirementField();

        // Budget field formatting
        const budgetDisplayField = document.getElementById('budget_display_modal');
        const budgetHiddenField = document.getElementById('budget_modal');
        
        if (budgetDisplayField && budgetHiddenField) {
            budgetDisplayField.addEventListener('input', function() {
                // Remove non-digit characters for the hidden field
                const numericValue = this.value.replace(/\D/g, '');
                budgetHiddenField.value = numericValue;
                
                // Format display value with thousand separators
                if (numericValue) {
                    const formatted = new Intl.NumberFormat('id-ID').format(numericValue);
                    // Only update if different to avoid cursor jumping
                    if (this.value !== formatted) {
                        const selectionStart = this.selectionStart;
                        this.value = formatted;
                        // Restore cursor position
                        this.setSelectionRange(selectionStart, selectionStart);
                }
            }
        });
            
            budgetDisplayField.addEventListener('keydown', function(e) {
                // Allow: backspace, delete, tab, escape, enter
                if ([8, 9, 27, 13, 46].indexOf(e.keyCode) !== -1 ||
                    // Allow: Ctrl+A, Ctrl+C, Ctrl+V, Ctrl+X
                    (e.keyCode === 65 && e.ctrlKey === true) ||
                    (e.keyCode === 67 && e.ctrlKey === true) ||
                    (e.keyCode === 86 && e.ctrlKey === true) ||
                    (e.keyCode === 88 && e.ctrlKey === true)) {
                    return;
                }
                // Ensure that it is a number and stop the keypress
                if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
                    e.preventDefault();
                }
            });
        }

        console.log('Project modals setup complete!');
        
        // ESC handler is now in global scope - no need for duplicate here
    }

    // Setup modal close buttons with robust event handling
    function setupModalCloseButtons() {
        console.log('Setting up modal close buttons...');
        
        // Define modal mappings - only for delete modal now
        const modalMappings = [
            {
                modal: document.getElementById('delete-confirm-modal'),
                content: document.getElementById('delete-modal-inner-content'),
                closeButtons: [
                    '#cancel-delete-button'
                ]
            }
        ];

        modalMappings.forEach(mapping => {
            if (mapping.modal && mapping.content) {
                mapping.closeButtons.forEach(selector => {
                    const button = document.querySelector(selector);
                    if (button) {
                        // Remove any existing listeners to avoid duplicates
                        button.removeEventListener('click', button._closeHandler);
                        
                        // Create new handler
                        button._closeHandler = function(e) {
                            e.preventDefault();
                            e.stopPropagation();
                            console.log('Close button clicked:', selector);
                            closeModal(mapping.modal, mapping.content);
                        };
                        
                        // Add new listener
                        button.addEventListener('click', button._closeHandler);
                        console.log('Added close handler for:', selector);
                    } else {
                        console.warn('Close button not found:', selector);
                    }
                });
            } else {
                console.warn('Modal or content not found for mapping:', mapping);
            }
        });

        console.log('Modal close buttons setup complete!');
    }

    // Enhanced Modal Functions
    function openModal(modalElement, innerContentElement, callback) {
        if (!modalElement || !innerContentElement) {
            console.error('Modal elements not found:', modalElement, innerContentElement);
            return;
        }

        console.log('Opening modal:', modalElement.id);
        modalElement.classList.remove('hidden');
        modalElement.classList.add('flex');
        
        // Add loading state if needed
        if (callback && typeof callback === 'function') {
            const originalContent = innerContentElement.innerHTML;
            innerContentElement.innerHTML = '<div class="flex items-center justify-center p-8"><div class="animate-spin rounded-full h-8 w-8 border-b-2 border-primary"></div></div>';
            
            setTimeout(() => {
                innerContentElement.innerHTML = originalContent;
                callback();
                animateModalIn(innerContentElement);
            }, 300);
        } else {
            setTimeout(() => animateModalIn(innerContentElement), 10);
        }
        
        // Focus management
        const firstFocusable = modalElement.querySelector('button, [href], input, select, textarea, [tabindex]:not([tabindex="-1"])');
        if (firstFocusable) {
            firstFocusable.focus();
        }
    }

    function closeModal(modalElement, innerContentElement) {
        if (!modalElement || !innerContentElement) {
            console.error('closeModal called with missing elements:', { modalElement, innerContentElement });
            return;
        }
        
        console.log('Closing modal:', modalElement.id);
        console.log('Modal current classes:', modalElement.className);
        console.log('Content current classes:', innerContentElement.className);
        
        animateModalOut(innerContentElement);
        setTimeout(() => {
            modalElement.classList.add('hidden');
            modalElement.classList.remove('flex');
            console.log('Modal closed, new classes:', modalElement.className);
        }, 300);
    }

    function animateModalIn(element) {
        element.style.transform = 'scale(0.95)';
        element.style.opacity = '0';
        
        requestAnimationFrame(() => {
            element.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
            element.style.transform = 'scale(1)';
            element.style.opacity = '1';
        });
    }

    function animateModalOut(element) {
        element.style.transition = 'all 0.3s cubic-bezier(0.4, 0, 0.2, 1)';
        element.style.transform = 'scale(0.95)';
        element.style.opacity = '0';
    }

    // Helper functions
    function formatDate(dateString) {
        if (!dateString) return '-';
        const date = new Date(dateString);
        return date.toLocaleDateString('id-ID', { day: 'numeric', month: 'short', year: 'numeric' });
    }

    function numberFormat(num) {
        return new Intl.NumberFormat('id-ID').format(num);
    }

    function getAvatarUrl(name) {
        return `https://ui-avatars.com/api/?name=${encodeURIComponent(name)}&color=FFFFFF&background=6366F1&size=32&font-size=0.45&bold=true`;
    }

    function createStatusBadge(status, type = 'project') {
        if (!status) return '<span class="text-gray-500">-</span>';
        
        const statusClasses = {
            'Active': 'bg-green-100 text-green-800 ring-green-200',
            'On-going': 'bg-blue-100 text-blue-800 ring-blue-200',
            'Pending': 'bg-yellow-100 text-yellow-800 ring-yellow-200',
            'Draft': 'bg-gray-100 text-gray-700 ring-gray-200',
            'Completed': 'bg-emerald-100 text-emerald-800 ring-emerald-200',
            'Cancelled': 'bg-red-100 text-red-800 ring-red-200',
            'Fully Paid': 'bg-green-100 text-green-800 ring-green-200',
            'Paid': 'bg-green-100 text-green-800 ring-green-200',
            'DP Paid': 'bg-sky-100 text-sky-800 ring-sky-200',
            'DP': 'bg-sky-100 text-sky-800 ring-sky-200',
            'Unpaid': 'bg-amber-100 text-amber-800 ring-amber-200',
            'Overdue': 'bg-red-100 text-red-800 ring-red-200',
            'Refunded': 'bg-purple-100 text-purple-800 ring-purple-200'
        };
        
        const statusClass = statusClasses[status] || 'bg-gray-100 text-gray-700 ring-gray-200';
        return `<span class="px-3 py-1 inline-flex text-xs font-semibold rounded-full ring-1 ${statusClass}">${status}</span>`;
    }

    function showToast(message, type = 'info') {
        // Create toast notification
        const toast = document.createElement('div');
        toast.className = `fixed top-4 right-4 z-50 p-4 rounded-lg shadow-lg max-w-sm transition-all duration-300 ${
            type === 'error' ? 'bg-red-50 text-red-800 border border-red-200' : 
            type === 'success' ? 'bg-green-50 text-green-800 border border-green-200' :
            'bg-blue-50 text-blue-800 border border-blue-200'
        }`;
        toast.innerHTML = `
            <div class="flex items-center">
                <i class="ri-${type === 'error' ? 'error-warning' : type === 'success' ? 'checkbox-circle' : 'information'}-line mr-2"></i>
                <span class="text-sm">${message}</span>
            </div>
        `;
        
        document.body.appendChild(toast);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            toast.style.opacity = '0';
            toast.style.transform = 'translateX(100%)';
            setTimeout(() => toast.remove(), 300);
        }, 5000);
    }

    // Enhanced Search with debouncing
    const searchInput = document.getElementById('project-search-input');
    if (searchInput) {
        let searchTimeout;
        
        searchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const searchIcon = this.previousElementSibling.querySelector('i');
            
            searchIcon.className = 'ri-loader-4-line animate-spin text-primary';
            
            searchTimeout = setTimeout(() => {
                searchIcon.className = 'ri-search-line text-slate-400 group-focus-within:text-primary transition-colors';
                // Auto-submit search after delay (optional)
                // this.closest('form').submit();
            }, 500);
        });
    }

    // Enhanced Status Filter with improved UX
    const statusFilterButton = document.getElementById('status-filter-button');
    const statusDropdownMenu = document.getElementById('status-dropdown-menu');
    const statusFilterText = document.getElementById('status-filter-text');

    if (statusFilterButton && statusDropdownMenu) {
        statusFilterButton.addEventListener('click', (event) => {
            event.stopPropagation();
            const isHidden = statusDropdownMenu.classList.contains('hidden');
            
            if (isHidden) {
                statusDropdownMenu.classList.remove('hidden');
                statusDropdownMenu.style.opacity = '0';
                statusDropdownMenu.style.transform = 'translateY(-10px)';
                
                requestAnimationFrame(() => {
                    statusDropdownMenu.style.transition = 'all 0.2s ease';
                    statusDropdownMenu.style.opacity = '1';
                    statusDropdownMenu.style.transform = 'translateY(0)';
                });
                
                // Rotate arrow
                const arrow = statusFilterButton.querySelector('.ri-arrow-down-s-line');
                if (arrow) {
                    arrow.style.transform = 'rotate(180deg)';
                }
            } else {
                statusDropdownMenu.style.opacity = '0';
                statusDropdownMenu.style.transform = 'translateY(-10px)';
                
                setTimeout(() => {
                    statusDropdownMenu.classList.add('hidden');
                }, 200);
                
                // Reset arrow
                const arrow = statusFilterButton.querySelector('.ri-arrow-down-s-line');
                if (arrow) {
                    arrow.style.transform = 'rotate(0deg)';
                }
            }
        });

        // Close dropdown when clicking outside
        document.addEventListener('click', (event) => {
            if (!statusFilterButton.contains(event.target) && !statusDropdownMenu.contains(event.target)) {
                statusDropdownMenu.style.opacity = '0';
                statusDropdownMenu.style.transform = 'translateY(-10px)';
                
                setTimeout(() => {
                    statusDropdownMenu.classList.add('hidden');
                }, 200);
                
                const arrow = statusFilterButton.querySelector('.ri-arrow-down-s-line');
                if (arrow) {
                    arrow.style.transform = 'rotate(0deg)';
                }
            }
        });

        // Update filter text
        const statusCheckboxes = statusDropdownMenu.querySelectorAll('.filter-status-checkbox');
        function updateStatusFilterText() {
            const selectedStatuses = Array.from(statusCheckboxes)
                .filter(cb => cb.checked)
                .map(cb => cb.value);
                
            if (selectedStatuses.length === 0 || selectedStatuses.length === statusCheckboxes.length) {
                statusFilterText.textContent = 'Any Status';
            } else if (selectedStatuses.length === 1) {
                statusFilterText.textContent = selectedStatuses[0];
            } else {
                statusFilterText.textContent = `${selectedStatuses.length} statuses`;
            }
        }

        statusCheckboxes.forEach(checkbox => {
            checkbox.addEventListener('change', updateStatusFilterText);
        });
        updateStatusFilterText();
    }
    
    // Auto-hide alerts with enhanced animation
    setTimeout(() => {
        const alerts = document.querySelectorAll('#alert-success, #alert-error');
        alerts.forEach(alert => {
            if (alert) {
                alert.style.transition = 'all 0.5s ease';
                alert.style.opacity = '0';
                alert.style.transform = 'translateY(-20px)';
                setTimeout(() => {
                    alert.style.display = 'none';
                }, 500);
            }
        });
    }, 5000);

    console.log('Enhanced Project Management JavaScript Initialized Successfully!');

    // ESC key handler for all modals
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            console.log('ESC key pressed globally');
            
            // Check and close project form modal
            const projectModal = document.getElementById('project-form-modal');
            if (projectModal && !projectModal.classList.contains('hidden')) {
                console.log('Project form modal is open, closing...');
                const projectModalContent = document.getElementById('project-modal-inner-content');
                if (projectModalContent) {
                    closeModal(projectModal, projectModalContent);
                    return;
                }
            }
            
            // Check and close view project details modal
            const viewModal = document.getElementById('view-project-details-modal');
            if (viewModal && !viewModal.classList.contains('hidden')) {
                console.log('View project modal is open, closing...');
                const viewModalContent = document.getElementById('view-project-modal-content');
                if (viewModalContent) {
                    closeModal(viewModal, viewModalContent);
                    return;
                }
            }
            
            // Check and close delete modal
            const deleteModal = document.getElementById('delete-confirm-modal');
            if (deleteModal && !deleteModal.classList.contains('hidden')) {
                console.log('Delete modal is open, closing...');
                const deleteModalContent = document.getElementById('delete-modal-inner-content');
                if (deleteModalContent) {
                    closeModal(deleteModal, deleteModalContent);
                }
            }
        }
    });
});
</script>
@endpush