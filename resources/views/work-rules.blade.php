@php
    $currentUser = auth()->user();
    $isAdmin = $currentUser && $currentUser->role === 'admin';
    $isStaff = $currentUser && $currentUser->role === 'staff';

    $sopSectionsCount = isset($sopSections) ? $sopSections->count() : 0;
    $regulationsCount = isset($regulations) ? $regulations->count() : 0;
@endphp

@extends('layouts.app')

@section('title', 'Work Rules & SOPs - DjokiHub')
@section('page_title', 'Work Rules & Standard Operating Procedures')


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
    <div class="relative w-full sm:w-auto flex-grow sm:flex-grow-0">
        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
            <i class="ri-search-line text-slate-400 text-lg" aria-hidden="true"></i>
        </div>
        <input type="text" id="rules-search-input" 
               class="w-full md:w-72 pl-12 pr-4 py-3 text-sm border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-300 focus:border-primary-500 transition-all duration-200 placeholder-slate-400 bg-white"
               placeholder="Search procedures, regulations, or policies...">
        <div id="search-results-indicator" class="absolute right-3 top-1/2 transform -translate-y-1/2 hidden">
            <span class="inline-flex items-center px-2 py-1 text-xs font-medium bg-primary-100 text-primary-700 rounded-full">
                <span id="search-count">0</span> found
            </span>
        </div>
    </div>
    @if($isAdmin)
    <button id="add-manage-rules-button" 
            class="btn-primary w-full sm:w-auto px-5 py-3 flex items-center justify-center space-x-2 text-sm font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105"
            title="Add new SOPs and Regulations">
        <i class="ri-add-circle-line text-lg" aria-hidden="true"></i>
        <span>Manage Rules</span>
    </button>
    @endif
@endsection

@section('content')
<div class="bg-slate-50 min-h-screen">
    {{-- Enhanced Header Section --}}
    <div class="bg-gradient-to-br from-white via-slate-50 to-blue-50 shadow-sm border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between md:items-center py-8 border-b border-slate-200">
                <div class="mb-4 md:mb-0">
                    <h1 class="text-2xl lg:text-3xl font-bold text-slate-800 mb-2">Work Rules & Standard Operating Procedures</h1>
                    <p class="text-slate-600 text-base lg:text-lg">
                        Comprehensive guidelines and procedures to ensure smooth operations and compliance
                    </p>
                    <div class="flex items-center mt-3 space-x-6 text-sm text-slate-500">
                        <div class="flex items-center">
                            <i class="ri-file-list-3-line mr-2 text-blue-500"></i>
                            <span>{{ $sopSectionsCount }} SOP Categories</span>
                        </div>
                        <div class="flex items-center">
                            <i class="ri-scales-3-line mr-2 text-purple-500"></i>
                            <span>{{ $regulationsCount }} Regulations</span>
                        </div>
                    </div>
                </div>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 w-full sm:w-auto">
                    @yield('header_actions')
                </div>
            </div>
        </div>
    </div>

    {{-- Enhanced Main Content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-200">
            {{-- Enhanced Tab Navigation --}}
            <div class="border-b border-slate-200 bg-gradient-to-r from-slate-50 to-slate-100">
                <nav class="flex -mb-px" aria-label="Work Rules Tabs">
                    <button data-tab-target="sop-content" 
                            class="tab-button flex-1 py-4 px-6 text-center border-b-3 font-bold text-base text-primary border-primary bg-white relative transition-all duration-300" 
                            aria-current="page">
                        <div class="flex items-center justify-center space-x-2">
                            <i class="ri-file-list-3-line text-xl" aria-hidden="true"></i>
                            <span>Standard Operating Procedures</span>
                        </div>
                        <div class="absolute bottom-0 left-0 right-0 h-1 bg-primary rounded-t-lg"></div>
                    </button>
                    <button data-tab-target="regulations-content" 
                            class="tab-button flex-1 py-4 px-6 text-center border-b-3 font-medium text-base text-slate-500 hover:text-slate-700 border-transparent hover:border-slate-300 hover:bg-slate-50 transition-all duration-300">
                        <div class="flex items-center justify-center space-x-2">
                            <i class="ri-scales-3-line text-xl" aria-hidden="true"></i>
                            <span>Company Regulations</span>
                        </div>
                    </button>
                </nav>
            </div>

            <div class="p-6 md:p-8 lg:p-10 space-y-8">
                {{-- SOP Content --}}
                <div id="sop-content" class="tab-content space-y-8">
                    @if(isset($sopSections) && $sopSections->count() > 0)
                        @foreach ($sopSections as $section)
                            <section id="sop-section-{{ Str::slug($section->title) }}" class="space-y-6 sop-section-item p-6 bg-gradient-to-br from-slate-50 to-blue-50 rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow duration-300">
                                <h2 class="text-xl font-bold text-slate-800 sop-section-title border-b border-slate-300 pb-3 mb-4 flex items-center">
                                    <i class="ri-folder-3-line mr-3 text-blue-600"></i>
                                    {{ $section->title }}
                                </h2>
                                @if(isset($section->introduction) && !empty($section->introduction))
                                    <div class="bg-white rounded-lg p-5 prose prose-sm max-w-none text-slate-700 leading-relaxed border border-slate-200 shadow-sm">
                                        {!! $section->introduction !!}
                                    </div>
                                @endif
                                @if(isset($section->items) && $section->items->count() > 0)
                                    <div class="space-y-4">
                                        @foreach ($section->items as $sop)
                                            <div class="bg-white border border-slate-200 rounded-xl p-5 hover:shadow-lg transition-all duration-300 sop-item transform hover:-translate-y-1">
                                                <div class="flex items-start space-x-4">
                                                    <div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center">
                                                        <i class="ri-file-text-line text-blue-600"></i>
                                                    </div>
                                                    <div class="flex-1">
                                                        <h3 class="text-lg font-semibold text-slate-900 mb-2">{{ $sop->title }}</h3>
                                                <div class="prose prose-sm max-w-none text-slate-600 leading-relaxed">
                                                    {!! $sop->description !!}
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        @endforeach
                                    </div>
                                @else
                                    <div class="bg-white border border-slate-200 rounded-xl p-8 text-center">
                                        <i class="ri-file-add-line text-4xl text-slate-300 mb-4" aria-hidden="true"></i>
                                        <p class="text-base text-slate-500 font-medium">No procedures defined yet</p>
                                        <p class="text-sm text-slate-400 mt-1">Specific procedures for this category will appear here once added.</p>
                                    </div>
                                @endif
                            </section>
                        @endforeach
                    @else
                        <div class="text-center py-16">
                            <div class="max-w-md mx-auto">
                                <i class="ri-file-list-3-line text-8xl text-slate-300 mb-6" aria-hidden="true"></i>
                                <h3 class="text-2xl font-bold text-slate-600 mb-4">No SOPs Created Yet</h3>
                                <p class="text-slate-500 mb-8 leading-relaxed">
                                    Standard Operating Procedures help maintain consistency and quality in your operations. 
                                    Get started by creating your first SOP category.
                                </p>
                                @if($isAdmin)
                                <button id="create-first-sop-btn" 
                                        class="inline-flex items-center px-6 py-3 bg-primary-600 hover:bg-primary-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                    <i class="ri-add-circle-line mr-2"></i>
                                    Create Your First SOP
                                </button>
                                @else
                                <div class="inline-flex items-center px-6 py-3 bg-slate-100 text-slate-500 font-medium rounded-xl">
                                    <i class="ri-information-line mr-2"></i>
                                    Contact an administrator to add SOPs
                                </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>

                {{-- Regulations Content --}}
                <div id="regulations-content" class="tab-content hidden space-y-6">
                    @if(isset($regulations) && $regulations->count() > 0)
                        <section class="space-y-6 regulation-section-item p-6 bg-gradient-to-br from-slate-50 to-purple-50 rounded-xl border border-slate-200 shadow-sm">
                             <h2 class="text-xl font-bold text-slate-800 regulation-section-title border-b border-slate-300 pb-3 mb-4 flex items-center">
                                <i class="ri-scales-3-line mr-3 text-purple-600"></i>
                                Company Regulations & Policies
                             </h2>
                            <div class="space-y-4">
                                @foreach ($regulations as $regulation)
                                    <div class="bg-white border border-slate-200 rounded-xl p-5 hover:shadow-lg transition-all duration-300 regulation-item transform hover:-translate-y-1">
                                        <div class="flex items-start space-x-4">
                                            <div class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                                                <i class="ri-shield-check-line text-purple-600"></i>
                                            </div>
                                            <div class="flex-1">
                                                <h3 class="text-lg font-semibold text-slate-900 mb-2">{{ $regulation->title }}</h3>
                                        <div class="prose prose-sm max-w-none text-slate-600 leading-relaxed">
                                            {!! $regulation->description !!}
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                @endforeach
                            </div>
                        </section>
                    @else
                         <div class="text-center py-16">
                            <div class="max-w-md mx-auto">
                                <i class="ri-scales-3-line text-8xl text-slate-300 mb-6" aria-hidden="true"></i>
                                <h3 class="text-2xl font-bold text-slate-600 mb-4">No Regulations Defined</h3>
                                <p class="text-slate-500 mb-8 leading-relaxed">
                                    Company regulations establish clear expectations and maintain compliance. 
                                    Start by adding your first company policy or regulation.
                                </p>
                                @if($isAdmin)
                                <button id="create-first-regulation-btn" 
                                        class="inline-flex items-center px-6 py-3 bg-purple-600 hover:bg-purple-700 text-white font-medium rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                    <i class="ri-add-circle-line mr-2"></i>
                                    Add Your First Regulation
                                </button>
                                @else
                                <div class="inline-flex items-center px-6 py-3 bg-slate-100 text-slate-500 font-medium rounded-xl">
                                    <i class="ri-information-line mr-2"></i>
                                    Contact an administrator to add regulations
                                </div>
                                @endif
                            </div>
                        </div>
                    @endif
                </div>
            </div>
        </div>
    </div>
</div>

{{-- Enhanced Success/Error Notifications --}}
<div id="toast-notifications" class="fixed top-4 right-4 z-50 space-y-3"></div>

{{-- Enhanced Modal for Adding/Managing SOP & Regulations --}}
<div id="add-manage-rules-modal" class="fixed inset-0 z-[100] hidden bg-black bg-opacity-60 backdrop-blur-sm flex items-center justify-center p-4 transition-all duration-300 ease-in-out" style="opacity: 0;">
    <div class="bg-white rounded-2xl shadow-2xl w-full max-w-4xl max-h-[90vh] flex flex-col transform transition-all duration-300 ease-in-out" 
         id="modal-content-area" style="transform: scale(0.95); opacity: 0;">
        
        {{-- Modal Header --}}
        <div class="flex items-center justify-between p-6 border-b border-slate-200 bg-gradient-to-r from-slate-50 to-slate-100 rounded-t-2xl">
            <div>
                <h3 class="text-2xl font-bold text-slate-800">Manage Work Rules</h3>
                <p class="text-slate-600 mt-1">Add and organize your SOPs and company regulations</p>
            </div>
            <button id="close-modal-button" 
                    class="text-slate-400 hover:text-red-600 p-2 rounded-xl hover:bg-red-50 transition-all duration-200 transform hover:scale-110"
                    title="Close modal">
                <span class="sr-only">Close modal</span>
                <i class="ri-close-line text-2xl" aria-hidden="true"></i>
            </button>
        </div>
        
        {{-- Modal Content --}}
        <div class="flex-1 overflow-hidden flex flex-col">
            {{-- Enhanced Modal Tabs --}}
            <div class="border-b border-slate-200 bg-slate-50">
                <nav class="flex -mb-px" aria-label="Modal Management Tabs">
                    <button data-modal-tab-target="modal-sop-management" 
                            class="modal-tab-button flex-1 py-4 px-6 text-center border-b-3 font-bold text-base text-primary border-primary bg-white transition-all duration-300" 
                            aria-current="page">
                        <div class="flex items-center justify-center space-x-2">
                            <i class="ri-file-list-3-line"></i>
                            <span>Manage SOPs</span>
                        </div>
                    </button>
                    <button data-modal-tab-target="modal-regulation-management" 
                            class="modal-tab-button flex-1 py-4 px-6 text-center border-b-3 font-medium text-base text-slate-500 hover:text-slate-700 border-transparent hover:border-slate-300 hover:bg-slate-100 transition-all duration-300">
                        <div class="flex items-center justify-center space-x-2">
                            <i class="ri-scales-3-line"></i>
                            <span>Manage Regulations</span>
                        </div>
                    </button>
                </nav>
            </div>

            {{-- Scrollable Content Area --}}
            <div class="flex-1 overflow-y-auto p-6 space-y-6 styled-scrollbar">
                {{-- Enhanced Notification Area --}}
                <div id="modal-notification-area" class="sticky top-0 bg-white py-2 z-10"></div>

                {{-- SOP Management Tab --}}
                <div id="modal-sop-management" class="modal-tab-content space-y-8">
                    {{-- Add New SOP Category --}}
                    <div class="p-6 border-2 border-blue-200 rounded-xl bg-gradient-to-br from-blue-50 to-white shadow-sm">
                        <div class="flex items-center mb-4">
                            <i class="ri-folder-add-line text-2xl text-blue-600 mr-3"></i>
                            <h5 class="text-xl font-bold text-slate-700">Create New SOP Category</h5>
                        </div>
                    <form id="add-sop-category-form" class="space-y-4">
                        <div>
                                <label for="sop-category-title" class="block text-sm font-semibold text-slate-700 mb-2">Category Title <span class="text-red-500">*</span></label>
                                <input type="text" name="title" id="sop-category-title" 
                                       class="w-full px-4 py-3 text-sm border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-500 transition-all duration-200" 
                                       placeholder="e.g., General Office Procedures, IT Security Guidelines" required>
                            <p class="text-xs text-red-500 mt-1 hidden" data-error-for="title"></p>
                        </div>
                        <div>
                                <label for="sop-category-intro" class="block text-sm font-semibold text-slate-700 mb-2">Category Introduction (Optional)</label>
                                <textarea name="introduction" id="sop-category-intro" rows="3" 
                                          class="w-full px-4 py-3 text-sm border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-500 transition-all duration-200" 
                                          placeholder="Brief overview of what this category covers..."></textarea>
                            <p class="text-xs text-red-500 mt-1 hidden" data-error-for="introduction"></p>
                        </div>
                            <button type="submit" class="btn-primary w-full sm:w-auto px-6 py-3 text-sm font-semibold flex items-center justify-center rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                <i class="ri-add-line mr-2 text-lg" aria-hidden="true"></i>Create Category
                        </button>
                    </form>
                </div>

                    {{-- Manage Existing SOPs --}}
                    <div class="p-6 border-2 border-slate-200 rounded-xl bg-gradient-to-br from-slate-50 to-white shadow-sm space-y-6">
                        <div class="flex items-center mb-4">
                            <i class="ri-settings-3-line text-2xl text-slate-600 mr-3"></i>
                            <h5 class="text-xl font-bold text-slate-700">Manage Existing SOPs</h5>
                        </div>
                        
                    <div>
                            <label for="select-sop-category-to-manage" class="block text-sm font-semibold text-slate-700 mb-2">Select Category to Edit</label>
                            <select id="select-sop-category-to-manage" name="selected_sop_category_id" 
                                    class="w-full px-4 py-3 text-sm border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-500 appearance-none bg-white transition-all duration-200">
                                <option value="">-- Choose an SOP Category --</option>
                        </select>
                    </div>

                        {{-- Category Edit Form --}}
                        <div id="edit-sop-category-form-container" class="hidden mt-6 p-5 border rounded-xl bg-white shadow-inner space-y-4">
                            <h6 class="text-lg font-semibold text-slate-700 flex items-center">
                                <i class="ri-edit-line mr-2 text-blue-600"></i>
                                Edit Category Details
                            </h6>
                        <form id="edit-sop-category-form" class="space-y-4">
                            <input type="hidden" name="id" id="edit-sop-category-id">
                            <div>
                                    <label for="edit-sop-category-title" class="block text-sm font-semibold text-slate-700 mb-2">Title <span class="text-red-500">*</span></label>
                                    <input type="text" name="title" id="edit-sop-category-title" 
                                           class="w-full px-4 py-3 text-sm border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-500 transition-all duration-200" required>
                                <p class="text-xs text-red-500 mt-1 hidden" data-error-for="title"></p>
                            </div>
                            <div>
                                    <label for="edit-sop-category-intro" class="block text-sm font-semibold text-slate-700 mb-2">Introduction</label>
                                    <textarea name="introduction" id="edit-sop-category-intro" rows="3" 
                                              class="w-full px-4 py-3 text-sm border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-blue-300 focus:border-blue-500 transition-all duration-200"></textarea>
                                <p class="text-xs text-red-500 mt-1 hidden" data-error-for="introduction"></p>
                            </div>
                                <div class="flex space-x-3 pt-2">
                                    <button type="submit" class="bg-blue-600 text-white px-5 py-2.5 text-sm font-semibold rounded-xl hover:bg-blue-700 transition-all duration-200 shadow-md hover:shadow-lg">
                                        <i class="ri-save-line mr-1"></i>Update Category
                                    </button>
                                    <button type="button" id="delete-sop-category-button" 
                                            class="bg-red-600 text-white px-5 py-2.5 text-sm font-semibold rounded-xl hover:bg-red-700 transition-all duration-200 shadow-md hover:shadow-lg">
                                        <i class="ri-delete-bin-line mr-1"></i>Delete Category
                                    </button>
                            </div>
                        </form>
                    </div>

                        {{-- Add SOP Item Form --}}
                        <div id="add-sop-item-form-container" class="hidden mt-6 p-5 border rounded-xl bg-green-50 shadow-inner space-y-4">
                             <h6 class="text-lg font-semibold text-slate-700 flex items-center">
                                <i class="ri-file-add-line mr-2 text-green-600"></i>
                                Add New SOP Item to "<span id="selected-category-name-for-item" class="text-blue-600"></span>"
                             </h6>
                        <form id="add-sop-item-form" class="space-y-4">
                            <input type="hidden" name="sop_section_id" id="add-sop-item-section-id">
                            <div>
                                    <label for="sop-item-title" class="block text-sm font-semibold text-slate-700 mb-2">SOP Item Title <span class="text-red-500">*</span></label>
                                    <input type="text" name="title" id="sop-item-title" 
                                           class="w-full px-4 py-3 text-sm border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-green-300 focus:border-green-500 transition-all duration-200" 
                                           placeholder="e.g., 1.1 Daily Startup Procedures" required>
                                <p class="text-xs text-red-500 mt-1 hidden" data-error-for="title"></p>
                            </div>
                            <div>
                                    <label for="sop-item-description" class="block text-sm font-semibold text-slate-700 mb-2">SOP Item Description <span class="text-red-500">*</span></label>
                                    <textarea name="description" id="sop-item-description" rows="4" 
                                              class="w-full px-4 py-3 text-sm border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-green-300 focus:border-green-500 transition-all duration-200 editor-minimal" 
                                              placeholder="Detailed step-by-step instructions... Use Markdown for formatting." required></textarea>
                                <p class="text-xs text-red-500 mt-1 hidden" data-error-for="description"></p>
                            </div>
                                <button type="submit" class="btn-primary px-6 py-3 text-sm font-semibold rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                    <i class="ri-add-line mr-2"></i>Add SOP Item
                                </button>
                        </form>
                    </div>

                        {{-- SOP Items List --}}
                        <div id="sop-items-list-container" class="hidden mt-6 space-y-3 bg-white p-5 rounded-xl shadow-inner border">
                        {{-- SOP Items will be listed here by JavaScript --}}
                    </div>
                </div>
            </div>

                {{-- Regulation Management Tab --}}
                <div id="modal-regulation-management" class="modal-tab-content hidden space-y-8">
                    {{-- Add New Regulation --}}
                    <div class="p-6 border-2 border-purple-200 rounded-xl bg-gradient-to-br from-purple-50 to-white shadow-sm">
                        <div class="flex items-center mb-4">
                            <i class="ri-shield-check-line text-2xl text-purple-600 mr-3"></i>
                            <h5 class="text-xl font-bold text-slate-700">Add New Company Regulation</h5>
                        </div>
                    <form id="add-regulation-form" class="space-y-4">
                        <div>
                                <label for="regulation-title" class="block text-sm font-semibold text-slate-700 mb-2">Regulation Title <span class="text-red-500">*</span></label>
                                <input type="text" name="title" id="regulation-title" 
                                       class="w-full px-4 py-3 text-sm border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-purple-300 focus:border-purple-500 transition-all duration-200" 
                                       placeholder="e.g., REG-001: Data Security and Privacy Policy" required>
                            <p class="text-xs text-red-500 mt-1 hidden" data-error-for="title"></p>
                        </div>
                        <div>
                                <label for="regulation-description" class="block text-sm font-semibold text-slate-700 mb-2">Regulation Description <span class="text-red-500">*</span></label>
                                <textarea name="description" id="regulation-description" rows="4" 
                                          class="w-full px-4 py-3 text-sm border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-purple-300 focus:border-purple-500 transition-all duration-200 editor-minimal" 
                                          placeholder="Detailed description of the regulation, requirements, and compliance guidelines... Use Markdown for formatting." required></textarea>
                            <p class="text-xs text-red-500 mt-1 hidden" data-error-for="description"></p>
                        </div>
                            <button type="submit" class="btn-primary w-full sm:w-auto px-6 py-3 text-sm font-semibold flex items-center justify-center rounded-xl shadow-lg hover:shadow-xl transition-all duration-200 transform hover:scale-105">
                                 <i class="ri-add-line mr-2 text-lg" aria-hidden="true"></i>Add Regulation
                        </button>
                    </form>
                </div>

                    {{-- Manage Existing Regulations --}}
                    <div class="p-6 border-2 border-slate-200 rounded-xl bg-gradient-to-br from-slate-50 to-white shadow-sm space-y-4">
                        <div class="flex items-center mb-4">
                            <i class="ri-settings-3-line text-2xl text-slate-600 mr-3"></i>
                            <h5 class="text-xl font-bold text-slate-700">Manage Existing Regulations</h5>
                    </div>
                        <div id="regulations-list-container" class="space-y-3 bg-white p-4 rounded-xl border">
                            <p class="text-sm text-slate-500 p-3 text-center">Loading regulations...</p>
                </div>
            </div>
                </div>
        </div>

            {{-- Modal Footer --}}
            <div class="flex items-center justify-between p-6 border-t border-slate-200 bg-slate-50 rounded-b-2xl">
                <p class="text-sm text-slate-500">
                    <i class="ri-information-line mr-1"></i>
                    Changes will be reflected after page refresh
                </p>
                <button id="cancel-modal-button" type="button" 
                        class="px-6 py-3 text-sm font-semibold text-slate-700 bg-white border border-slate-300 rounded-xl shadow-sm hover:bg-slate-100 focus:outline-none focus:ring-2 focus:ring-offset-1 focus:ring-slate-400 transition-all duration-200">
                Close
            </button>
            </div>
        </div>
    </div>
</div>

{{-- Enhanced Styles --}}
<style>
    /* Typography enhancements */
    .prose-sm h1, .prose-sm h2, .prose-sm h3 { 
        margin-top: 1em; margin-bottom: 0.5em; 
        font-weight: 600;
    }
    .prose-sm p { 
        margin-top: 0.8em; margin-bottom: 0.8em; 
        line-height: 1.6;
    }
    .prose-sm ul, .prose-sm ol { 
        margin-top: 0.8em; margin-bottom: 0.8em; 
        padding-left: 1.5em; 
    }
    .prose-sm li { 
        margin-top: 0.3em; margin-bottom: 0.3em; 
    }
    .prose-sm blockquote { 
        margin-top: 1em; margin-bottom: 1em; 
        padding-left: 1em; border-left-width: 0.25em;
        background: #f8fafc;
        border-left-color: #e2e8f0;
        padding: 1em;
        border-radius: 0.5rem;
    }

    /* Enhanced animations */
    .animate-fadeInUpSm { 
        animation: fadeInUpSm 0.3s ease-out forwards; 
    }
    @keyframes fadeInUpSm {
        from { opacity: 0; transform: translateY(-20px) scale(0.95); }
        to { opacity: 1; transform: translateY(0) scale(1); }
    }
    
    .animate-fadeIn { 
        animation: fadeInGlobal 0.4s ease-out forwards; 
    }
    @keyframes fadeInGlobal { 
        from { opacity: 0; } 
        to { opacity: 1; } 
    }

    /* Modal animations */
    #add-manage-rules-modal #modal-content-area.opacity-0 { 
        opacity: 0; 
        transform: scale(0.9); 
    }
    #add-manage-rules-modal #modal-content-area.opacity-100 { 
        opacity: 1; 
        transform: scale(1); 
    }

    /* Enhanced scrollbar */
    .styled-scrollbar::-webkit-scrollbar { 
        width: 8px; 
        height: 8px; 
    }
    .styled-scrollbar::-webkit-scrollbar-track { 
        background: #f1f5f9; 
        border-radius: 4px;
    }
    .styled-scrollbar::-webkit-scrollbar-thumb { 
        background: #cbd5e1; 
        border-radius: 4px; 
        border: 1px solid #f1f5f9;
    }
    .styled-scrollbar::-webkit-scrollbar-thumb:hover { 
        background: #94a3b8; 
    }

    /* Tab enhancements */
    .tab-button.active-tab {
        background: white;
        border-bottom: 3px solid #2563eb;
        color: #2563eb;
        font-weight: 700;
    }

    /* Search result highlighting */
    .search-highlight {
        background-color: #fef3c7;
        padding: 0.125rem 0.25rem;
        border-radius: 0.25rem;
        font-weight: 600;
    }

    /* Toast notification styles */
    .toast {
        @apply bg-white border border-slate-200 rounded-xl shadow-xl p-4 transform transition-all duration-300 ease-in-out max-w-sm;
    }
    .toast.success {
        @apply border-green-200 bg-green-50;
    }
    .toast.error {
        @apply border-red-200 bg-red-50;
    }
    .toast.entering {
        transform: translateX(100%);
        opacity: 0;
    }
    .toast.entered {
        transform: translateX(0);
        opacity: 1;
    }
    .toast.exiting {
        transform: translateX(100%);
        opacity: 0;
    }

    /* Mobile responsiveness */
    @media (max-width: 768px) {
        .tab-button span {
            display: none;
        }
        .tab-button i {
            font-size: 1.5rem;
        }
        #modal-content-area {
            margin: 1rem;
            max-height: calc(100vh - 2rem);
        }
    }

    /* Border width for tabs */
    .border-b-3 {
        border-bottom-width: 3px;
    }
</style>
@endsection

@push('scripts')
<script id="enhanced-work-rules-scripts">
document.addEventListener('DOMContentLoaded', function() {
    // Enhanced tab management
    const mainTabButtons = document.querySelectorAll('.tab-button');
    const rulesSearchInput = document.getElementById('rules-search-input');
    const searchResultsIndicator = document.getElementById('search-results-indicator');
    const searchCount = document.getElementById('search-count');
    let searchTimeout;

    // Modal elements
    const addManageButton = document.getElementById('add-manage-rules-button');
    const modal = document.getElementById('add-manage-rules-modal');
    const modalContentArea = document.getElementById('modal-content-area');
    const closeModalButton = document.getElementById('close-modal-button');
    const cancelModalButton = document.getElementById('cancel-modal-button');
    const modalNotificationArea = document.getElementById('modal-notification-area');

    // Empty state buttons
    const createFirstSopBtn = document.getElementById('create-first-sop-btn');
    const createFirstRegulationBtn = document.getElementById('create-first-regulation-btn');

    // Modal internal tabs
    const modalTabButtons = document.querySelectorAll('.modal-tab-button');

    // SOP Management elements
    const addSopCategoryForm = document.getElementById('add-sop-category-form');
    const selectSopCategoryToManage = document.getElementById('select-sop-category-to-manage');
    const editSopCategoryFormContainer = document.getElementById('edit-sop-category-form-container');
    const editSopCategoryForm = document.getElementById('edit-sop-category-form');
    const deleteSopCategoryButton = document.getElementById('delete-sop-category-button');
    const addSopItemFormContainer = document.getElementById('add-sop-item-form-container');
    const addSopItemForm = document.getElementById('add-sop-item-form');
    const selectedCategoryNameForItem = document.getElementById('selected-category-name-for-item');
    const sopItemsListContainer = document.getElementById('sop-items-list-container');

    // Regulation Management elements
    const addRegulationForm = document.getElementById('add-regulation-form');
    const regulationsListContainer = document.getElementById('regulations-list-container');

    // --- CSRF Token & API URLs ---
    const csrfToken = document.querySelector('meta[name="csrf-token"]')?.getAttribute('content') || '{{ csrf_token() }}';
    const API_URLS = {
        sopSections: "{{ route('admin.sop-sections.index') }}",
        sopSectionDetail: "{{ route('admin.sop-sections.show', ['sop_section' => ':id']) }}",
        sopItems: "{{ route('admin.sop-items.index') }}",
        sopItemDetail: "{{ route('admin.sop-items.show', ['sop_item' => ':id']) }}",
        regulations: "{{ route('admin.regulations.index') }}",
        regulationDetail: "{{ route('admin.regulations.show', ['regulation' => ':id']) }}"
    };

     function getSopSectionDetailUrl(id) { return API_URLS.sopSectionDetail.replace(':id', id); }
     function getSopItemDetailUrl(id) { return API_URLS.sopItemDetail.replace(':id', id); }
     function getRegulationDetailUrl(id) { return API_URLS.regulationDetail.replace(':id', id); }

    // --- ENHANCED TOAST NOTIFICATIONS ---
    function showToast(message, type = 'success', duration = 5000) {
        const toastContainer = document.getElementById('toast-notifications');
        if (!toastContainer) return;

        const toastId = 'toast-' + Date.now();
        const toast = document.createElement('div');
        toast.id = toastId;
        toast.className = `toast ${type} entering`;
        
        const iconClass = type === 'success' ? 'ri-checkbox-circle-line' : 'ri-error-warning-line';
        const iconColor = type === 'success' ? 'text-green-600' : 'text-red-600';
        const titleText = type === 'success' ? 'Success!' : 'Error!';
        
        toast.innerHTML = `
            <div class="flex items-start space-x-3">
                <div class="flex-shrink-0">
                    <i class="${iconClass} ${iconColor} text-xl"></i>
                </div>
                <div class="flex-1 min-w-0">
                    <p class="text-sm font-semibold text-slate-800">${titleText}</p>
                    <p class="text-sm text-slate-600 mt-1">${message}</p>
                </div>
                <button type="button" class="flex-shrink-0 text-slate-400 hover:text-slate-600 p-1 rounded-lg hover:bg-slate-100 transition-colors" onclick="dismissToast('${toastId}')">
                    <i class="ri-close-line text-lg"></i>
                </button>
            </div>
        `;

        toastContainer.appendChild(toast);
        
        // Trigger animation
        requestAnimationFrame(() => {
            toast.classList.remove('entering');
            toast.classList.add('entered');
        });

        // Auto dismiss
        setTimeout(() => dismissToast(toastId), duration);
    }

    window.dismissToast = function(toastId) {
        const toast = document.getElementById(toastId);
        if (toast) {
            toast.classList.remove('entered');
            toast.classList.add('exiting');
            setTimeout(() => toast.remove(), 300);
        }
    };

    // --- UTILITY FUNCTIONS ---
    function escapeHtml(unsafe) {
        if (unsafe === null || unsafe === undefined) return '';
        return String(unsafe)
             .replace(/&/g, "&amp;")
             .replace(/</g, "&lt;")
             .replace(/>/g, "&gt;")
             .replace(/"/g, "&quot;")
             .replace(/'/g, "&#039;");
    }

    function showModalNotification(message, type = 'success') {
        if (!modalNotificationArea) return;
        const alertClass = type === 'success' ? 'bg-green-50 border-green-200 text-green-800' : 'bg-red-50 border-red-200 text-red-800';
        const iconClass = type === 'success' ? 'ri-checkbox-circle-line text-green-600' : 'ri-error-warning-line text-red-600';
        
        modalNotificationArea.innerHTML = `
            <div class="border ${alertClass} rounded-xl p-4 flex items-center space-x-3" role="alert">
                <i class="${iconClass} text-xl"></i>
                <span class="text-sm font-medium">${escapeHtml(message)}</span>
            </div>
        `;
        setTimeout(() => {
            if (modalNotificationArea) modalNotificationArea.innerHTML = '';
        }, 4000);
    }

    function clearFormErrors(formElement) {
        if (!formElement) return;
        formElement.querySelectorAll('[data-error-for]').forEach(el => {
            el.classList.add('hidden');
            el.textContent = '';
        });
         formElement.querySelectorAll('.border-red-500').forEach(el => {
            el.classList.remove('border-red-500');
            el.classList.add('border-slate-300');
        });
    }

    function displayFormErrors(formElement, errors) {
        if (!formElement) return;
        clearFormErrors(formElement);
        for (const field in errors) {
            const inputField = formElement.querySelector(`[name="${field}"]`);
            if (inputField) {
                inputField.classList.remove('border-slate-300');
                inputField.classList.add('border-red-500');
            }
            const errorElement = formElement.querySelector(`[data-error-for="${field}"]`);
            if (errorElement) {
                errorElement.textContent = errors[field][0];
                errorElement.classList.remove('hidden');
            }
        }
    }

    // --- ENHANCED TAB MANAGEMENT ---
    function initializeTabs(buttons, contentsCollection, activeClassPrimary, activeClassBorder, inactiveClassText, inactiveClassBorderHover) {
        if (!buttons || buttons.length === 0) return;

        const contents = Array.from(contentsCollection);
        if (contents.length === 0) return;

        let activeTabButton = Array.from(buttons).find(btn => btn.getAttribute('aria-current') === 'page');
        if (!activeTabButton && buttons.length > 0) {
            activeTabButton = buttons[0];
        }

        buttons.forEach(button => {
            const targetContentId = button.dataset.modalTabTarget || button.dataset.tabTarget;
            const targetContent = document.getElementById(targetContentId);

            if (button === activeTabButton) {
                button.classList.add(activeClassPrimary, activeClassBorder, 'bg-white');
                button.classList.remove(inactiveClassText, 'border-transparent', inactiveClassBorderHover);
                button.setAttribute('aria-current', 'page');
                if (targetContent) targetContent.classList.remove('hidden');
            } else {
                button.classList.add(inactiveClassText, 'border-transparent', inactiveClassBorderHover);
                button.classList.remove(activeClassPrimary, activeClassBorder, 'bg-white');
                button.removeAttribute('aria-current');
                if (targetContent) targetContent.classList.add('hidden');
            }

            button.addEventListener('click', function() {
                // Visual feedback
                this.style.transform = 'scale(0.98)';
                setTimeout(() => this.style.transform = '', 150);

                buttons.forEach(btn => {
                    btn.classList.remove(activeClassPrimary, activeClassBorder, 'bg-white');
                    btn.classList.add(inactiveClassText, 'border-transparent', inactiveClassBorderHover);
                    btn.removeAttribute('aria-current');
                });
                contents.forEach(content => {
                    if (content) content.classList.add('hidden');
                });

                this.classList.add(activeClassPrimary, activeClassBorder, 'bg-white');
                this.classList.remove(inactiveClassText, 'border-transparent', inactiveClassBorderHover);
                this.setAttribute('aria-current', 'page');

                const currentTargetContentId = this.dataset.modalTabTarget || this.dataset.tabTarget;
                const currentTargetContent = document.getElementById(currentTargetContentId);
                if (currentTargetContent) {
                    currentTargetContent.classList.remove('hidden');
                    currentTargetContent.style.opacity = '0';
                    requestAnimationFrame(() => {
                        currentTargetContent.style.transition = 'opacity 0.2s ease-in-out';
                        currentTargetContent.style.opacity = '1';
                    });
                }

                // Clear search when switching main tabs
                if (buttons === mainTabButtons && rulesSearchInput) {
                    rulesSearchInput.value = '';
                    updateSearchResults(0);
                    triggerSearch(rulesSearchInput, '');
                }
            });
        });

        // Ensure active tab content is shown initially
        if (activeTabButton) {
            const activeContentId = activeTabButton.dataset.modalTabTarget || activeTabButton.dataset.tabTarget;
            const activeContent = document.getElementById(activeContentId);
            if (activeContent) activeContent.classList.remove('hidden');
        }
    }

    // Initialize main and modal tabs
    const mainPageTabContents = Array.from(document.querySelectorAll('.tab-content')).filter(el => !el.closest('#add-manage-rules-modal'));
    initializeTabs(mainTabButtons, mainPageTabContents, 'text-primary', 'border-primary', 'text-slate-500', 'hover:border-slate-300');

    const modalPageTabContents = Array.from(document.querySelectorAll('.modal-tab-content')).filter(el => el.closest('#add-manage-rules-modal'));
    initializeTabs(modalTabButtons, modalPageTabContents, 'text-primary', 'border-primary', 'text-slate-500', 'hover:border-slate-300');

    // --- ENHANCED MODAL MANAGEMENT ---
    function openModal() {
        if (modal && modalContentArea) {
            modal.classList.remove('hidden');
            setTimeout(() => {
                modal.style.opacity = '1';
                modalContentArea.style.transform = 'scale(1)';
                modalContentArea.style.opacity = '1';
            }, 10);
            
            console.info('[Modal] Membuka modal manajemen SOP & Regulasi');
            loadSopCategoriesForSelect();
            console.info('[SOP] Memuat ulang daftar SOP untuk modal...');
            renderSopSections();
            console.info('[Regulation] Memuat ulang daftar regulasi untuk modal...');
            renderRegulations();
            
            // Reset to first tab
            if (modalTabButtons.length > 0) {
                modalTabButtons[0].click();
            }
        }
    }

    function closeModal() {
        if (modal && modalContentArea) {
            modalContentArea.style.transform = 'scale(0.95)';
            modalContentArea.style.opacity = '0';
            modal.style.opacity = '0';
            
            setTimeout(() => modal.classList.add('hidden'), 300);

            if (modalNotificationArea) modalNotificationArea.innerHTML = '';
            
            // Clear forms
            [addSopCategoryForm, editSopCategoryForm, addSopItemForm, addRegulationForm].forEach(form => {
                if (form) {
                    form.reset();
                    clearFormErrors(form);
                }
            });

            if (editSopCategoryFormContainer) editSopCategoryFormContainer.classList.add('hidden');
            if (addSopItemFormContainer) addSopItemFormContainer.classList.add('hidden');
            if (sopItemsListContainer) sopItemsListContainer.classList.add('hidden');
            if (selectSopCategoryToManage) selectSopCategoryToManage.value = '';
        }
    }

    // Modal event listeners
    if (addManageButton) addManageButton.addEventListener('click', openModal);
    if (closeModalButton) closeModalButton.addEventListener('click', closeModal);
    if (cancelModalButton) cancelModalButton.addEventListener('click', closeModal);
    if (modal) modal.addEventListener('click', (event) => event.target === modal && closeModal());

    // Empty state button handlers
    if (createFirstSopBtn) createFirstSopBtn.addEventListener('click', openModal);
    if (createFirstRegulationBtn) {
        createFirstRegulationBtn.addEventListener('click', () => {
            openModal();
            setTimeout(() => {
                const regulationTab = document.querySelector('[data-modal-tab-target="modal-regulation-management"]');
                if (regulationTab) regulationTab.click();
            }, 100);
        });
                }

    // --- ENHANCED SEARCH FUNCTIONALITY ---
    function updateSearchResults(count) {
        if (searchResultsIndicator && searchCount) {
            if (count > 0) {
                searchCount.textContent = count;
                searchResultsIndicator.classList.remove('hidden');
            } else {
                searchResultsIndicator.classList.add('hidden');
            }
        }
    }

    function highlightSearchTerms(element, searchTerm) {
        if (!searchTerm) return;
        
        const walker = document.createTreeWalker(
            element,
            NodeFilter.SHOW_TEXT,
            null,
            false
        );
                    
        const textNodes = [];
        let node;
        while (node = walker.nextNode()) {
            textNodes.push(node);
        }

        textNodes.forEach(textNode => {
            const parent = textNode.parentNode;
            if (parent.classList && parent.classList.contains('search-highlight')) return;

            const text = textNode.textContent;
            const regex = new RegExp(`(${escapeHtml(searchTerm)})`, 'gi');
            if (regex.test(text)) {
                const highlightedHTML = text.replace(regex, '<span class="search-highlight">$1</span>');
                const wrapper = document.createElement('div');
                wrapper.innerHTML = highlightedHTML;
                while (wrapper.firstChild) {
                    parent.insertBefore(wrapper.firstChild, textNode);
                }
                parent.removeChild(textNode);
            }
        });
    }

    function removeSearchHighlights() {
        document.querySelectorAll('.search-highlight').forEach(highlight => {
            const parent = highlight.parentNode;
            parent.replaceChild(document.createTextNode(highlight.textContent), highlight);
            parent.normalize();
        });
            }

    function triggerSearch(inputElement, searchTerm) {
        const activeTabContent = document.querySelector('.tab-content:not(.hidden):not(.modal-tab-content)');
        if (!activeTabContent) return;

        removeSearchHighlights();

        const allSections = activeTabContent.querySelectorAll('section.sop-section-item, section.regulation-section-item');
        const allItems = activeTabContent.querySelectorAll('.sop-item, .regulation-item');
        const allSectionIntroductions = activeTabContent.querySelectorAll('section > .prose');
        
        const noDataOriginallyPresent = (activeTabContent.id === 'sop-content' && {{ $sopSectionsCount }} === 0) ||
                                     (activeTabContent.id === 'regulations-content' && {{ $regulationsCount }} === 0);

        const emptyStateContainer = activeTabContent.querySelector('.text-center.py-16');
        
        let hasVisibleContentOverall = false;
        let visibleCount = 0;

        // Hide all initially
        allSections.forEach(s => s.style.display = 'none');
        allItems.forEach(i => i.style.display = 'none');
        allSectionIntroductions.forEach(i => i.style.display = 'none');
        if (emptyStateContainer) emptyStateContainer.style.display = 'none';

        if (searchTerm === "") {
            allSections.forEach(s => s.style.display = '');
            allItems.forEach(i => i.style.display = '');
            allSectionIntroductions.forEach(i => i.style.display = '');
            
            if (noDataOriginallyPresent && emptyStateContainer) {
                emptyStateContainer.style.display = '';
            }
            updateSearchResults(0);
            return;
        }

        // Search items first
        allItems.forEach(itemCard => {
            if (itemCard.textContent.toLowerCase().includes(searchTerm)) {
                itemCard.style.display = '';
                highlightSearchTerms(itemCard, searchTerm);
                visibleCount++;
                
                const parentSection = itemCard.closest('section.sop-section-item, section.regulation-section-item');
                if (parentSection) {
                    parentSection.style.display = '';
                    const titleEl = parentSection.querySelector('h2');
                    if (titleEl) titleEl.style.display = '';
                    
                    const directIntroEl = Array.from(parentSection.children).find(child => child.matches('div.prose'));
                    if (directIntroEl) directIntroEl.style.display = '';

                    hasVisibleContentOverall = true;
                }
            }
        });
        
        // Search section titles and introductions
        allSections.forEach(section => {
            const sectionTitle = section.querySelector('h2');
            const sectionIntro = Array.from(section.children).find(child => child.matches('div.prose'));
            
            let sectionTitleMatches = sectionTitle && sectionTitle.textContent.toLowerCase().includes(searchTerm);
            let sectionIntroMatches = sectionIntro && sectionIntro.textContent.toLowerCase().includes(searchTerm);

            if (sectionTitleMatches || sectionIntroMatches) {
                section.style.display = '';
                if (sectionTitle) {
                    sectionTitle.style.display = '';
                    if (sectionTitleMatches) highlightSearchTerms(sectionTitle, searchTerm);
                }
                if (sectionIntro) {
                    sectionIntro.style.display = '';
                    if (sectionIntroMatches) highlightSearchTerms(sectionIntro, searchTerm);
                }
                
                section.querySelectorAll('.sop-item, .regulation-item').forEach(item => {
                    item.style.display = '';
                    highlightSearchTerms(item, searchTerm);
                });
                
                visibleCount++;
                hasVisibleContentOverall = true;
            } else {
                const hasVisibleItems = Array.from(section.querySelectorAll('.sop-item, .regulation-item')).some(item => item.style.display !== 'none');
                if (hasVisibleItems) {
                     section.style.display = '';
                     if (sectionTitle) sectionTitle.style.display = '';
                     if (sectionIntro) sectionIntro.style.display = '';
                     hasVisibleContentOverall = true;
                }
            }
        });
            
        if (!hasVisibleContentOverall && emptyStateContainer) {
            emptyStateContainer.style.display = '';
            const h3Element = emptyStateContainer.querySelector('h3');
            const pElement = emptyStateContainer.querySelector('p');
            if (h3Element) h3Element.textContent = `No results found for "${searchTerm}"`;
            if (pElement) pElement.textContent = 'Try adjusting your search terms or clearing the search.';
        }

        updateSearchResults(visibleCount);
    }

    // Enhanced search input handling
    if (rulesSearchInput) {
        rulesSearchInput.addEventListener('input', function() {
            clearTimeout(searchTimeout);
            const currentSearchTerm = this.value.toLowerCase().trim();
            searchTimeout = setTimeout(() => {
                triggerSearch(this, currentSearchTerm);
            }, 300);
        });

        // Search on Enter key
        rulesSearchInput.addEventListener('keydown', function(e) {
            if (e.key === 'Enter') {
                clearTimeout(searchTimeout);
                triggerSearch(this, this.value.toLowerCase().trim());
            }
        });
    }

    // --- SOP CATEGORY MANAGEMENT (Continuing existing functionality) ---
    async function loadSopCategoriesForSelect() {
        if (!selectSopCategoryToManage) return;
        try {
            const response = await fetch(API_URLS.sopSections, { headers: { 'Accept': 'application/json' }});
            if (!response.ok) throw new Error(`HTTP error! Status: ${response.status}`);
            const categories = await response.json();

            selectSopCategoryToManage.innerHTML = '<option value="">-- Choose an SOP Category --</option>';
            categories.forEach(cat => {
                const option = document.createElement('option');
                option.value = cat.id;
                option.textContent = escapeHtml(cat.title);
                selectSopCategoryToManage.appendChild(option);
            });
        } catch (error) {
            console.error('Error loading SOP categories:', error);
            showModalNotification('Could not load SOP categories for selection.', 'error');
        }
    }

    // Add enhanced form submission with loading states
    if (addSopCategoryForm) {
        addSopCategoryForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            clearFormErrors(this);
            
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="ri-loader-4-line animate-spin mr-2"></i>Creating...';
            submitBtn.disabled = true;

            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());

            try {
                const response = await fetch(API_URLS.sopSections, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify(data),
                });
                const result = await response.json();
                
                if (!response.ok) {
                    if (response.status === 422 && result.errors) displayFormErrors(this, result.errors);
                    else throw new Error(result.message || `HTTP error! Status: ${response.status}`);
                    return;
                }
                
                showToast(result.message || 'SOP Category created successfully!', 'success');
                showModalNotification(result.message || 'SOP Category created successfully!', 'success');
                this.reset();
                await renderSopSections();
            } catch (error) {
                console.error('Error adding SOP category:', error);
                showToast(error.message || 'Failed to create SOP category.', 'error');
                showModalNotification(error.message || 'Failed to create SOP category.', 'error');
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
    }

    // --- RENDER DINAMIS DAFTAR SOP & REGULATION ---
    async function renderSopSections() {
        const sopContent = document.getElementById('sop-content');
        if (!sopContent) return;
        try {
            console.log('[SOP] Fetching SOP sections with items...');
            const response = await fetch(API_URLS.sopSections + '?with_items=1', { headers: { 'Accept': 'application/json' } });
            const sopSections = await response.json();
            console.log('[SOP] Data diterima:', sopSections);
            let html = '';
            if (sopSections.length === 0) {
                html = `<div class="text-center py-16"><div class="max-w-md mx-auto"><i class="ri-file-list-3-line text-8xl text-slate-300 mb-6"></i><h3 class="text-2xl font-bold text-slate-600 mb-4">No SOPs Created Yet</h3><p class="text-slate-500 mb-8 leading-relaxed">Standard Operating Procedures help maintain consistency and quality in your operations. Get started by creating your first SOP category.</p></div></div>`;
            } else {
                sopSections.forEach(section => {
                    html += `<section id="sop-section-${section.id}" class="space-y-6 sop-section-item p-6 bg-gradient-to-br from-slate-50 to-blue-50 rounded-xl border border-slate-200 shadow-sm hover:shadow-md transition-shadow duration-300">
                        <h2 class="text-xl font-bold text-slate-800 sop-section-title border-b border-slate-300 pb-3 mb-4 flex items-center">
                            <i class="ri-folder-3-line mr-3 text-blue-600"></i>${section.title}
                        </h2>`;
                    if (section.introduction) {
                        html += `<div class="bg-white rounded-lg p-5 prose prose-sm max-w-none text-slate-700 leading-relaxed border border-slate-200 shadow-sm">${section.introduction}</div>`;
                    }
                    if (section.items && section.items.length > 0) {
                        html += '<div class="space-y-4">';
                        section.items.forEach(sop => {
                            html += `<div class="bg-white border border-slate-200 rounded-xl p-5 hover:shadow-lg transition-all duration-300 sop-item transform hover:-translate-y-1"><div class="flex items-start space-x-4"><div class="flex-shrink-0 w-10 h-10 bg-blue-100 rounded-xl flex items-center justify-center"><i class="ri-file-text-line text-blue-600"></i></div><div class="flex-1"><h3 class="text-lg font-semibold text-slate-900 mb-2">${sop.title}</h3><div class="prose prose-sm max-w-none text-slate-600 leading-relaxed">${sop.description}</div></div></div></div>`;
                        });
                        html += '</div>';
                    } else {
                        html += `<div class="bg-white border border-slate-200 rounded-xl p-8 text-center"><i class="ri-file-add-line text-4xl text-slate-300 mb-4"></i><p class="text-base text-slate-500 font-medium">No procedures defined yet</p><p class="text-sm text-slate-400 mt-1">Specific procedures for this category will appear here once added.</p></div>`;
                    }
                    html += '</section>';
                });
            }
            sopContent.innerHTML = html;
            console.info('[SOP] Render selesai.');
        } catch (error) {
            sopContent.innerHTML = '<div class="text-center py-8 text-red-500">Gagal memuat SOP.</div>';
            console.error('[SOP] Error saat render:', error);
        }
    }

    async function renderRegulations() {
        const regContent = document.getElementById('regulations-content');
        if (!regContent) return;
        try {
            console.log('[Regulation] Fetching regulations...');
            const response = await fetch(API_URLS.regulations, { headers: { 'Accept': 'application/json' } });
            const regulations = await response.json();
            console.log('[Regulation] Data diterima:', regulations);
            let html = '';
            if (regulations.length === 0) {
                html = `<div class="text-center py-16"><div class="max-w-md mx-auto"><i class="ri-scales-3-line text-8xl text-slate-300 mb-6"></i><h3 class="text-2xl font-bold text-slate-600 mb-4">No Regulations Defined</h3><p class="text-slate-500 mb-8 leading-relaxed">Company regulations establish clear expectations and maintain compliance. Start by adding your first company policy or regulation.</p></div></div>`;
            } else {
                html += `<section class="space-y-6 regulation-section-item p-6 bg-gradient-to-br from-slate-50 to-purple-50 rounded-xl border border-slate-200 shadow-sm"><h2 class="text-xl font-bold text-slate-800 regulation-section-title border-b border-slate-300 pb-3 mb-4 flex items-center"><i class="ri-scales-3-line mr-3 text-purple-600"></i>Company Regulations & Policies</h2><div class="space-y-4">`;
                regulations.forEach(reg => {
                    html += `<div class="bg-white border border-slate-200 rounded-xl p-5 hover:shadow-lg transition-all duration-300 regulation-item transform hover:-translate-y-1"><div class="flex items-start space-x-4"><div class="flex-shrink-0 w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center"><i class="ri-shield-check-line text-purple-600"></i></div><div class="flex-1"><h3 class="text-lg font-semibold text-slate-900 mb-2">${reg.title}</h3><div class="prose prose-sm max-w-none text-slate-600 leading-relaxed">${reg.description}</div></div></div></div>`;
                });
                html += '</div></section>';
            }
            regContent.innerHTML = html;
            console.info('[Regulation] Render selesai.');
        } catch (error) {
            regContent.innerHTML = '<div class="text-center py-8 text-red-500">Gagal memuat regulasi.</div>';
            console.error('[Regulation] Error saat render:', error);
        }
    }

    // Keyboard shortcuts
    document.addEventListener('keydown', function(e) {
        // ESC to close modal
        if (e.key === 'Escape' && modal && !modal.classList.contains('hidden')) {
            closeModal();
        }
        
        // Ctrl/Cmd + K to focus search
        if ((e.ctrlKey || e.metaKey) && e.key === 'k') {
            e.preventDefault();
            if (rulesSearchInput) {
                rulesSearchInput.focus();
                rulesSearchInput.select();
            }
        }
    });

    // --- Loader Regulations Modal ---
    async function loadRegulationsForManagement() {
        const container = document.getElementById('regulations-list-container');
        if (!container) return;
        container.innerHTML = '<p class="text-sm text-slate-500 p-3 text-center">Loading regulations...</p>';
        try {
            const response = await fetch(API_URLS.regulations, { headers: { 'Accept': 'application/json' } });
            let regulations = await response.json();
            if (!Array.isArray(regulations)) {
                console.error('[Regulation] Response bukan array:', regulations);
                container.innerHTML = '<p class="text-sm text-red-500 p-3 text-center">Failed to load regulations (invalid response).</p>';
                return;
            }
            console.log('[Regulation] Data untuk manajemen:', regulations);
            if (regulations.length === 0) {
                container.innerHTML = '<p class="text-sm text-slate-500 p-3 text-center">No regulations found.</p>';
                return;
            }
            container.innerHTML = regulations.map(reg => `
                <div class="flex justify-between items-center border-b py-2">
                    <div>
                        <div class="font-semibold">${reg.title}</div>
                        <div class="text-xs text-slate-500">${reg.description}</div>
                    </div>
                    <div class="flex space-x-2">
                        <button class="edit-reg-btn px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded" data-id="${reg.id}">Edit</button>
                        <button class="delete-reg-btn px-2 py-1 text-xs bg-red-100 text-red-700 rounded" data-id="${reg.id}">Delete</button>
                    </div>
                </div>
            `).join('');
            // Event handler edit/delete
            container.querySelectorAll('.edit-reg-btn').forEach(btn => {
                btn.addEventListener('click', async e => {
                    const id = btn.getAttribute('data-id');
                    try {
                        console.info('[Regulation] Fetch detail untuk edit:', id);
                        const resp = await fetch(API_URLS.regulationDetail.replace(':id', id), { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
                        if (resp.status === 403) {
                            showToast('Akses ditolak. Anda tidak punya izin edit regulation.', 'error');
                            console.error('[Regulation] Unauthorized (403) saat fetch detail:', id);
                            return;
                        }
                        if (resp.status === 404) {
                            showToast('Regulation tidak ditemukan.', 'error');
                            console.error('[Regulation] Not found (404) saat fetch detail:', id);
                            return;
                        }
                        if (resp.status === 422) {
                            showToast('Data regulation tidak valid.', 'error');
                            console.error('[Regulation] Unprocessable (422) saat fetch detail:', id);
                            return;
                        }
                        if (!resp.ok) {
                            showToast('Gagal mengambil data regulation.', 'error');
                            console.error('[Regulation] Error lain saat fetch detail:', id, resp.status);
                            return;
                        }
                        const reg = await resp.json();
                        showEditRegulationForm(id, reg.title, reg.description || '');
                    } catch (err) {
                        showToast(err.message || 'Gagal memuat data regulation.', 'error');
                        console.error('[Regulation] Error fetch detail:', err);
                    }
                });
            });
            container.querySelectorAll('.delete-reg-btn').forEach(btn => {
                btn.addEventListener('click', async e => {
                    const id = btn.getAttribute('data-id');
                    if (!confirm('Yakin ingin menghapus regulation ini?')) return;
                    try {
                        const delResp = await fetch(API_URLS.regulationDetail.replace(':id', id), {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                        });
                        const delResult = await delResp.json();
                        if (!delResp.ok) throw new Error(delResult.message || 'Gagal menghapus regulation.');
                        showToast(delResult.message || 'Regulation deleted!', 'success');
                        loadRegulationsForManagement();
                        renderRegulations();
                    } catch (err) {
                        showToast(err.message || 'Gagal menghapus regulation.', 'error');
                        console.error('[Regulation] Delete error:', err);
                    }
                });
            });
        } catch (error) {
            container.innerHTML = '<p class="text-sm text-red-500 p-3 text-center">Failed to load regulations.</p>';
            console.error('[Regulation] Error load for management:', error);
        }
    }

    // --- Loader SOP Items Modal ---
    async function loadSopItemsForManagement(categoryId) {
        const container = document.getElementById('sop-items-list-container');
        if (!container || !categoryId) return;
        container.innerHTML = '<p class="text-sm text-slate-500 p-3 text-center">Loading SOP items...</p>';
        try {
            const response = await fetch(API_URLS.sopItems + '?sop_section_id=' + categoryId, { headers: { 'Accept': 'application/json' } });
            let items = await response.json();
            if (!Array.isArray(items)) {
                console.error('[SOP] Response bukan array:', items);
                container.innerHTML = '<p class="text-sm text-red-500 p-3 text-center">Failed to load SOP items (invalid response).</p>';
                return;
            }
            console.log('[SOP] Items untuk manajemen:', items);
            if (items.length === 0) {
                container.innerHTML = '<p class="text-sm text-slate-500 p-3 text-center">No SOP items found.</p>';
                return;
            }
            container.innerHTML = items.map(item => `
                <div class="flex justify-between items-center border-b py-2">
                    <div>
                        <div class="font-semibold">${item.title}</div>
                        <div class="text-xs text-slate-500">${item.description}</div>
                    </div>
                    <div class="flex space-x-2">
                        <button class="edit-sop-btn px-2 py-1 text-xs bg-blue-100 text-blue-700 rounded" data-id="${item.id}">Edit</button>
                        <button class="delete-sop-btn px-2 py-1 text-xs bg-red-100 text-red-700 rounded" data-id="${item.id}">Delete</button>
                    </div>
                </div>
            `).join('');
            // Event handler edit/delete
            container.querySelectorAll('.edit-sop-btn').forEach(btn => {
                btn.addEventListener('click', async e => {
                    const id = btn.getAttribute('data-id');
                    try {
                        console.info('[SOP] Fetch detail item untuk edit:', id);
                        const resp = await fetch(API_URLS.sopItemDetail.replace(':id', id), { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
                        if (resp.status === 403) {
                            showToast('Akses ditolak. Anda tidak punya izin edit SOP item.', 'error');
                            console.error('[SOP] Unauthorized (403) saat fetch detail item:', id);
                            return;
                        }
                        if (resp.status === 404) {
                            showToast('SOP item tidak ditemukan.', 'error');
                            console.error('[SOP] Not found (404) saat fetch detail item:', id);
                            return;
                        }
                        if (resp.status === 422) {
                            showToast('Data SOP item tidak valid.', 'error');
                            console.error('[SOP] Unprocessable (422) saat fetch detail item:', id);
                            return;
                        }
                        if (!resp.ok) {
                            showToast('Gagal mengambil data SOP item.', 'error');
                            console.error('[SOP] Error lain saat fetch detail item:', id, resp.status);
                            return;
                        }
                        const item = await resp.json();
                        showEditSopItemForm(id, item.title, item.description || '', categoryId);
                    } catch (err) {
                        showToast(err.message || 'Gagal memuat data SOP item.', 'error');
                        console.error('[SOP] Error fetch detail item:', err);
                    }
                });
            });
            container.querySelectorAll('.delete-sop-btn').forEach(btn => {
                btn.addEventListener('click', async e => {
                    const id = btn.getAttribute('data-id');
                    if (!confirm('Yakin ingin menghapus SOP item ini?')) return;
                    try {
                        const delResp = await fetch(API_URLS.sopItemDetail.replace(':id', id), {
                            method: 'DELETE',
                            headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                        });
                        const delResult = await delResp.json();
                        if (!delResp.ok) throw new Error(delResult.message || 'Gagal menghapus SOP item.');
                        showToast(delResult.message || 'SOP item deleted!', 'success');
                        loadSopItemsForManagement(categoryId);
                        renderSopSections();
                    } catch (err) {
                        showToast(err.message || 'Gagal menghapus SOP item.', 'error');
                        console.error('[SOP] Delete error:', err);
                    }
                });
            });
        } catch (error) {
            container.innerHTML = '<p class="text-sm text-red-500 p-3 text-center">Failed to load SOP items.</p>';
            console.error('[SOP] Error load for management:', error);
        }
    }

    // --- Add Regulation AJAX ---
    if (addRegulationForm) {
        addRegulationForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            clearFormErrors(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="ri-loader-4-line animate-spin mr-2"></i>Adding...';
            submitBtn.disabled = true;
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            try {
                const response = await fetch(API_URLS.regulations, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify(data),
                });
                const result = await response.json();
                if (!response.ok) {
                    if (response.status === 422 && result.errors) displayFormErrors(this, result.errors);
                    else throw new Error(result.message || `HTTP error! Status: ${response.status}`);
                    return;
                }
                showToast(result.message || 'Regulation created successfully!', 'success');
                showModalNotification(result.message || 'Regulation created successfully!', 'success');
                this.reset();
                loadRegulationsForManagement();
                renderRegulations();
            } catch (error) {
                showToast(error.message || 'Failed to create regulation.', 'error');
                showModalNotification(error.message || 'Failed to create regulation.', 'error');
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
    }

    // --- Add SOP Item AJAX ---
    if (addSopItemForm) {
        addSopItemForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            clearFormErrors(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="ri-loader-4-line animate-spin mr-2"></i>Adding...';
            submitBtn.disabled = true;
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            try {
                console.info('[SOP] Add SOP item:', data);
                const response = await fetch(API_URLS.sopItems, {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify(data),
                });
                const result = await response.json();
                if (!response.ok) {
                    if (response.status === 422 && result.errors) displayFormErrors(this, result.errors);
                    else throw new Error(result.message || `HTTP error! Status: ${response.status}`);
                    return;
                }
                showToast(result.message || 'SOP item created successfully!', 'success');
                showModalNotification(result.message || 'SOP item created successfully!', 'success');
                this.reset();
                if (data.sop_section_id) {
                    console.info('[SOP] Reload list setelah add:', data.sop_section_id);
                    await loadSopItemsForManagement(data.sop_section_id);
                }
                renderSopSections();
            } catch (error) {
                showToast(error.message || 'Failed to create SOP item.', 'error');
                showModalNotification(error.message || 'Failed to create SOP item.', 'error');
                console.error('[SOP] Error add:', error);
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
    }

    // --- Edit Regulation (inline form) ---
    function showEditRegulationForm(id, currentTitle, currentDesc) {
        const container = document.getElementById('regulations-list-container');
        const formHtml = `
            <form id="edit-reg-form" class="p-3 bg-slate-50 rounded-xl mb-3 animate-fadeIn">
                <input type="hidden" name="id" value="${id}">
                <input type="text" name="title" value="${currentTitle}" class="w-full mb-2 px-2 py-1 border rounded" required>
                <textarea name="description" class="w-full mb-2 px-2 py-1 border rounded" required>${currentDesc}</textarea>
                <div class="flex space-x-2">
                    <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded">Update</button>
                    <button type="button" id="cancel-edit-reg" class="bg-slate-300 px-3 py-1 rounded">Cancel</button>
                </div>
                <div id="edit-reg-error" class="text-red-600 text-sm mt-2"></div>
            </form>
        `;
        container.innerHTML = formHtml + container.innerHTML;
        document.getElementById('cancel-edit-reg').onclick = () => loadRegulationsForManagement();
        document.getElementById('edit-reg-form').onsubmit = async function(e) {
            e.preventDefault();
            const data = {
                title: this.title.value,
                description: this.description.value
            };
            try {
                const resp = await fetch(API_URLS.regulationDetail.replace(':id', id), {
                    method: 'PUT',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                const result = await resp.json();
                if (!resp.ok) {
                    if (resp.status === 422 && result.errors) {
                        const errorDiv = document.getElementById('edit-reg-error');
                        errorDiv.textContent = Object.values(result.errors).flat().join(', ');
                    } else if (resp.status === 403) {
                        showToast('Akses ditolak. Anda tidak punya izin edit regulation.', 'error');
                    } else {
                        showToast(result.message || 'Gagal update regulation.', 'error');
                    }
                    return;
                }
                showToast(result.message || 'Regulation updated!', 'success');
                loadRegulationsForManagement();
                renderRegulations();
            } catch (err) {
                showToast(err.message || 'Gagal update regulation.', 'error');
                console.error('[Regulation] Error update:', err);
            }
        };
    }

    // --- Edit SOP Item (inline form) ---
    function showEditSopItemForm(id, currentTitle, currentDesc, categoryId) {
        const container = document.getElementById('sop-items-list-container');
        const formHtml = `
            <form id="edit-sopitem-form" class="p-3 bg-slate-50 rounded-xl mb-3 animate-fadeIn">
                <input type="hidden" name="id" value="${id}">
                <input type="hidden" name="sop_section_id" value="${categoryId}">
                <input type="text" name="title" value="${currentTitle}" class="w-full mb-2 px-2 py-1 border rounded" required>
                <textarea name="description" class="w-full mb-2 px-2 py-1 border rounded" required>${currentDesc}</textarea>
                <div class="flex space-x-2">
                    <button type="submit" class="bg-blue-600 text-white px-3 py-1 rounded">Update</button>
                    <button type="button" id="cancel-edit-sopitem" class="bg-slate-300 px-3 py-1 rounded">Cancel</button>
                </div>
                <div id="edit-sopitem-error" class="text-red-600 text-sm mt-2"></div>
            </form>
        `;
        container.innerHTML = formHtml + container.innerHTML;
        document.getElementById('cancel-edit-sopitem').onclick = () => loadSopItemsForManagement(categoryId);
        document.getElementById('edit-sopitem-form').onsubmit = async function(e) {
            e.preventDefault();
            const data = {
                title: this.title.value,
                description: this.description.value,
                sop_section_id: this.sop_section_id.value
            };
            try {
                const resp = await fetch(API_URLS.sopItemDetail.replace(':id', id), {
                    method: 'PUT',
                    headers: { 
                        'Content-Type': 'application/json', 
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json'
                    },
                    body: JSON.stringify(data)
                });
                const result = await resp.json();
                if (!resp.ok) {
                    if (resp.status === 422 && result.errors) {
                        const errorDiv = document.getElementById('edit-sopitem-error');
                        errorDiv.textContent = Object.values(result.errors).flat().join(', ');
                    } else if (resp.status === 403) {
                        showToast('Akses ditolak. Anda tidak punya izin edit SOP item.', 'error');
                    } else {
                        showToast(result.message || 'Gagal update SOP item.', 'error');
                    }
                    return;
                }
                showToast(result.message || 'SOP item updated!', 'success');
                loadSopItemsForManagement(categoryId);
                renderSopSections();
            } catch (err) {
                showToast(err.message || 'Gagal update SOP item.', 'error');
                console.error('[SOP] Error update item:', err);
            }
        };
    }

    console.log('Work Rules page enhanced JavaScript loaded successfully');

    // --- Panggil loader saat tab modal diubah ---
    modalTabButtons.forEach(btn => {
        btn.addEventListener('click', function() {
            if (this.dataset.modalTabTarget === 'modal-regulation-management') {
                console.info('[Tab] Manage Regulations dibuka');
                loadRegulationsForManagement();
            }
            if (this.dataset.modalTabTarget === 'modal-sop-management') {
                console.info('[Tab] Manage SOPs dibuka');
                const selectedCat = selectSopCategoryToManage.value;
                if (selectedCat) {
                    document.getElementById('add-sop-item-section-id').value = selectedCat;
                    sopItemsListContainer.classList.remove('hidden');
                    console.info('[SOP] Loader dipanggil untuk kategori:', selectedCat);
                    loadSopItemsForManagement(selectedCat);
                } else {
                    sopItemsListContainer.classList.add('hidden');
                    console.warn('[SOP] Tidak ada kategori SOP terpilih saat tab dibuka');
                }
            }
        });
    });

    // --- Panggil loader saat kategori SOP dipilih ---
    if (selectSopCategoryToManage) {
        selectSopCategoryToManage.addEventListener('change', function() {
            const catId = this.value;
            document.getElementById('add-sop-item-section-id').value = catId;
            if (catId) {
                sopItemsListContainer.classList.remove('hidden');
                console.info('[SOP] Kategori SOP dipilih:', catId);
                loadSopItemsForManagement(catId);
            } else {
                sopItemsListContainer.classList.add('hidden');
                console.warn('[SOP] Tidak ada kategori SOP terpilih');
            }
        });
    }

    // --- Edit SOP Category (inline form di bawah dropdown) ---
    if (selectSopCategoryToManage) {
        selectSopCategoryToManage.addEventListener('change', async function() {
            const catId = this.value;
            if (catId) {
                try {
                    console.info('[SOP] Fetch detail kategori:', catId);
                    const resp = await fetch(API_URLS.sopSectionDetail.replace(':id', catId), { headers: { 'Accept': 'application/json', 'X-CSRF-TOKEN': csrfToken } });
                    if (resp.status === 403) {
                        showToast('Akses ditolak. Anda tidak punya izin edit kategori.', 'error');
                        console.error('[SOP] Unauthorized (403) saat fetch kategori:', catId);
                        editSopCategoryFormContainer.classList.add('hidden');
                        return;
                    }
                    if (resp.status === 404) {
                        showToast('Kategori SOP tidak ditemukan.', 'error');
                        console.error('[SOP] Not found (404) saat fetch kategori:', catId);
                        editSopCategoryFormContainer.classList.add('hidden');
                        return;
                    }
                    if (resp.status === 422) {
                        showToast('Data kategori tidak valid.', 'error');
                        console.error('[SOP] Unprocessable (422) saat fetch kategori:', catId);
                        editSopCategoryFormContainer.classList.add('hidden');
                        return;
                    }
                    if (!resp.ok) {
                        showToast('Gagal mengambil data kategori SOP.', 'error');
                        console.error('[SOP] Error lain saat fetch kategori:', catId, resp.status);
                        editSopCategoryFormContainer.classList.add('hidden');
                        return;
                    }
                    const cat = await resp.json();
                    editSopCategoryFormContainer.classList.remove('hidden');
                    editSopCategoryForm.title.value = cat.title;
                    editSopCategoryForm.introduction.value = cat.introduction || '';
                    editSopCategoryForm.id.value = cat.id;
                } catch (err) {
                    editSopCategoryFormContainer.classList.add('hidden');
                    showToast(err.message || 'Gagal memuat detail kategori SOP.', 'error');
                    console.error('[SOP] Error fetch kategori:', err);
                }
            } else {
                editSopCategoryFormContainer.classList.add('hidden');
            }
        });
    }
    // --- Edit SOP Category submit ---
    if (editSopCategoryForm) {
        editSopCategoryForm.addEventListener('submit', async function(event) {
            event.preventDefault();
            clearFormErrors(this);
            const submitBtn = this.querySelector('button[type="submit"]');
            const originalText = submitBtn.innerHTML;
            submitBtn.innerHTML = '<i class="ri-loader-4-line animate-spin mr-2"></i>Updating...';
            submitBtn.disabled = true;
            const formData = new FormData(this);
            const data = Object.fromEntries(formData.entries());
            try {
                const resp = await fetch(API_URLS.sopSectionDetail.replace(':id', data.id), {
                    method: 'PUT',
                    headers: { 'Content-Type': 'application/json', 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' },
                    body: JSON.stringify({ title: data.title, introduction: data.introduction })
                });
                const result = await resp.json();
                if (!resp.ok) {
                    if (resp.status === 422 && result.errors) displayFormErrors(this, result.errors);
                    else if (resp.status === 403) showToast('Akses ditolak. Anda tidak punya izin edit kategori.', 'error');
                    else showToast(result.message || 'Gagal update kategori SOP.', 'error');
                    return;
                }
                showToast(result.message || 'Kategori SOP berhasil diupdate!', 'success');
                showModalNotification(result.message || 'Kategori SOP berhasil diupdate!', 'success');
                await loadSopCategoriesForSelect();
                renderSopSections();
            } catch (error) {
                showToast(error.message || 'Gagal update kategori SOP.', 'error');
                showModalNotification(error.message || 'Gagal update kategori SOP.', 'error');
                console.error('[SOP] Error update kategori:', error);
            } finally {
                submitBtn.innerHTML = originalText;
                submitBtn.disabled = false;
            }
        });
    }
    // --- Delete SOP Category ---
    if (deleteSopCategoryButton) {
        deleteSopCategoryButton.addEventListener('click', async function() {
            const catId = editSopCategoryForm.id.value;
            if (!catId) return;
            if (!confirm('Yakin ingin menghapus kategori SOP ini?')) return;
            deleteSopCategoryButton.disabled = true;
            deleteSopCategoryButton.innerHTML = '<i class="ri-loader-4-line animate-spin mr-2"></i>Deleting...';
            try {
                const resp = await fetch(API_URLS.sopSectionDetail.replace(':id', catId), {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                });
                const result = await resp.json();
                if (!resp.ok) {
                    if (resp.status === 403) showToast('Akses ditolak. Anda tidak punya izin hapus kategori.', 'error');
                    else showToast(result.message || 'Gagal hapus kategori SOP.', 'error');
                    return;
                }
                showToast(result.message || 'Kategori SOP berhasil dihapus!', 'success');
                showModalNotification(result.message || 'Kategori SOP berhasil dihapus!', 'success');
                await loadSopCategoriesForSelect();
                renderSopSections();
                editSopCategoryFormContainer.classList.add('hidden');
            } catch (error) {
                showToast(error.message || 'Gagal hapus kategori SOP.', 'error');
                showModalNotification(error.message || 'Gagal hapus kategori SOP.', 'error');
                console.error('[SOP] Error hapus kategori:', error);
            } finally {
                deleteSopCategoryButton.disabled = false;
                deleteSopCategoryButton.innerHTML = '<i class="ri-delete-bin-line mr-1"></i>Delete Category';
            }
        });
    }
});
</script>
@endpush