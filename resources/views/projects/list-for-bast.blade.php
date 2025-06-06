@extends('layouts.app')

@section('title', 'Select Project for BAST - DjokiHub')

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

@section('content')
{{-- Simple White Hero Section --}}
<div class="bg-white shadow-sm border-b border-gray-200">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-16">
        <div class="text-center">
            {{-- Icon --}}
            <div class="mx-auto w-20 h-20 bg-purple-100 rounded-2xl flex items-center justify-center mb-6">
                <i class="ri-file-shield-2-line text-purple-600 text-3xl"></i>
                        </div>
            
            {{-- Title --}}
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 mb-4">
                BAST Generation
            </h1>
            <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
                Generate professional BAST (Berita Acara Serah Terima) documents for completed projects
            </p>
            
            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto mt-12">
                <div class="bg-white border border-gray-200 rounded-2xl p-6 text-center shadow-sm hover:shadow-md transition-all duration-300">
                    <div class="text-3xl font-bold text-gray-900 mb-2">{{ $projects->total() }}</div>
                    <div class="text-gray-600 text-sm">Total Projects</div>
                    </div>
                <div class="bg-white border border-gray-200 rounded-2xl p-6 text-center shadow-sm hover:shadow-md transition-all duration-300">
                    <div class="text-3xl font-bold text-green-600 mb-2">{{ $projects->where('status', 'Completed')->count() }}</div>
                    <div class="text-gray-600 text-sm">Completed Projects</div>
                </div>
                <div class="bg-white border border-gray-200 rounded-2xl p-6 text-center shadow-sm hover:shadow-md transition-all duration-300">
                    <div class="text-3xl font-bold text-blue-600 mb-2">{{ $projects->whereIn('status', ['Active', 'On-going'])->count() }}</div>
                    <div class="text-gray-600 text-sm">In Progress</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        {{-- Enhanced Flash Messages --}}
        @if(session('success'))
            <div id="alert-success" class="mb-8 transform transition-all duration-500 ease-out animate-slideInDown">
                <div class="bg-gradient-to-r from-green-50 to-emerald-50 border border-green-200 rounded-2xl shadow-lg overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-green-500 rounded-xl flex items-center justify-center">
                                    <i class="ri-checkbox-circle-line text-white text-xl"></i>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-bold text-green-800">Success!</h3>
                                <p class="text-sm text-green-700 mt-1">{{ session('success') }}</p>
                            </div>
                            <button type="button" class="ml-4 text-green-400 hover:text-green-600 p-2 rounded-xl hover:bg-green-100 transition-all duration-200" onclick="document.getElementById('alert-success').style.display='none'">
                                <i class="ri-close-line text-xl"></i>
                                </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div id="alert-error" class="mb-8 transform transition-all duration-500 ease-out animate-slideInDown">
                <div class="bg-gradient-to-r from-red-50 to-rose-50 border border-red-200 rounded-2xl shadow-lg overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-12 h-12 bg-red-500 rounded-xl flex items-center justify-center">
                                    <i class="ri-error-warning-line text-white text-xl"></i>
                                </div>
                            </div>
                            <div class="ml-4 flex-1">
                                <h3 class="text-lg font-bold text-red-800">Error!</h3>
                                <p class="text-sm text-red-700 mt-1">{{ session('error') }}</p>
                            </div>
                            <button type="button" class="ml-4 text-red-400 hover:text-red-600 p-2 rounded-xl hover:bg-red-100 transition-all duration-200" onclick="document.getElementById('alert-error').style.display='none'">
                                <i class="ri-close-line text-xl"></i>
                                </button>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        {{-- Enhanced Main Content --}}
        <div class="bg-white rounded-3xl shadow-2xl overflow-hidden border border-gray-100">
            {{-- Simple Header --}}
            <div class="bg-white border-b border-gray-200 px-8 py-8">
                <div class="flex items-center justify-between">
                    <div class="flex items-center space-x-4">
                        <div class="w-12 h-12 bg-purple-100 rounded-xl flex items-center justify-center">
                            <i class="ri-file-shield-2-line text-purple-600 text-xl"></i>
                        </div>
                    <div>
                            <h2 class="text-2xl font-bold text-gray-900">Project Selection for BAST</h2>
                            <p class="text-gray-600 text-sm mt-1">Choose a completed project to generate its BAST document</p>
                        </div>
                    </div>
                    <div class="hidden lg:flex items-center space-x-2 bg-gray-50 rounded-xl px-4 py-2">
                        <div class="w-3 h-3 bg-purple-500 rounded-full"></div>
                        <span class="text-gray-700 text-sm font-medium">Ready for BAST</span>
                    </div>
                </div>
            </div>
            
            {{-- Enhanced Table --}}
            <div class="overflow-x-auto">
                <table class="min-w-full">
                    <thead class="bg-gray-50">
                        <tr>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Project Info</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Client</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Timeline</th>
                            <th scope="col" class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($projects as $project)
                        <tr class="hover:bg-gray-50 transition-all duration-300 group">
                            <td class="px-4 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-purple-100 rounded-lg flex items-center justify-center shadow-sm">
                                        <i class="ri-briefcase-4-line text-purple-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900 group-hover:text-purple-700 transition-colors">
                                            {{ Str::limit($project->project_name, 30) }}
                                        </div>
                                        <div class="text-xs font-medium text-purple-600 bg-purple-100 px-2 py-1 rounded-full inline-block mt-1">
                                            {{ $project->order_id ?? ('#PRJ-' . str_pad($project->id, 4, '0', STR_PAD_LEFT)) }}
                                        </div>
                                    </div>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex items-center space-x-2">
                                    <i class="ri-user-3-line text-gray-400 text-sm"></i>
                                    <span class="text-sm font-medium text-gray-700">{{ $project->client_name ?? 'N/A' }}</span>
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                @php
                                    $statusClasses = [
                                        'Active' => 'bg-green-100 text-green-800 border-green-200',
                                        'On-going' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'Pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'Draft' => 'bg-gray-100 text-gray-800 border-gray-200',
                                        'Completed' => 'bg-purple-100 text-purple-800 border-purple-200',
                                        'Cancelled' => 'bg-red-100 text-red-800 border-red-200'
                                    ];
                                    $statusClass = $statusClasses[$project->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                @endphp
                                <div class="flex items-center space-x-2">
                                    <span class="px-3 py-1 text-xs font-bold rounded-lg border {{ $statusClass }} shadow-sm">
                                    {{ $project->status }}
                                </span>
                                    @if($project->status === 'Completed')
                                        <div class="w-2 h-2 bg-green-400 rounded-full" title="Ready for BAST"></div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="space-y-1">
                                    @if($project->start_date)
                                    <div class="flex items-center text-xs text-gray-600">
                                        <i class="ri-calendar-line mr-1 text-gray-400 text-xs"></i>
                                        Started: {{ \Carbon\Carbon::parse($project->start_date)->format('M d, Y') }}
                                    </div>
                                    @endif
                                    @if($project->end_date)
                                    <div class="flex items-center text-xs text-gray-600">
                                        <i class="ri-calendar-check-line mr-1 text-gray-400 text-xs"></i>
                                        Deadline: {{ \Carbon\Carbon::parse($project->end_date)->format('M d, Y') }}
                                    </div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex justify-center">
                                @if($project->status === 'Completed')
                                    <button onclick="showConfirmModal('{{ $project->id }}', '{{ addslashes($project->project_name) }}', 'BAST', '{{ route('projects.bast.generate', $project->id) }}')"
                                       class="group relative inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium text-sm rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                                        <i class="ri-file-shield-2-line mr-1 text-sm"></i>
                                        <span>Generate BAST</span>
                                </button>
                                @else
                                    <div class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-500 font-medium text-sm rounded-lg cursor-not-allowed">
                                        <i class="ri-lock-line mr-1 text-sm"></i>
                                        <span>Not Completed</span>
                                    </div>
                                @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-purple-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="ri-folder-search-line text-3xl text-purple-400"></i>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">No Projects Available</h3>
                                    <p class="text-gray-500 mb-4 max-w-md text-sm">There are no completed projects available for BAST generation at this time.</p>
                                    <a href="{{ route('projects.index') }}" class="inline-flex items-center px-4 py-2 bg-purple-600 hover:bg-purple-700 text-white font-medium text-sm rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                                        <i class="ri-arrow-left-line mr-2"></i>
                                        Back to Projects
                                    </a>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            
            {{-- Enhanced Pagination --}}
            @if ($projects->hasPages())
            <div class="bg-gray-50 px-4 py-4 border-t border-gray-200">
                <div class="flex items-center justify-between">
                    <div class="text-sm text-gray-700">
                        Showing {{ $projects->firstItem() }} to {{ $projects->lastItem() }} of {{ $projects->total() }} results
                    </div>
                    <div class="pagination-wrapper">
                {{ $projects->links() }}
                    </div>
                </div>
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Enhanced Confirmation Modal --}}
<div id="confirmationModal" class="fixed inset-0 bg-black bg-opacity-50 backdrop-blur-sm hidden flex items-center justify-center z-50 transition-all duration-300" style="opacity: 0;">
    <div class="relative mx-auto p-6 w-full max-w-3xl bg-white rounded-2xl shadow-2xl transform transition-all duration-300 max-h-[90vh] overflow-y-auto" style="transform: scale(0.9);">
        {{-- Modal Header --}}
        <div class="text-center mb-6">
            <div id="modalIconContainer" class="mx-auto w-16 h-16 rounded-2xl flex items-center justify-center mb-4 bg-purple-100">
                <i id="modalIcon" class="text-purple-600 text-2xl"></i>
            </div>
            <h3 id="modalTitle" class="text-xl font-bold text-gray-900 mb-2"></h3>
            <p class="text-gray-600 text-sm">
                Please review the project details before generating the <strong id="modalDocTypeStrong" class="text-purple-600"></strong>
            </p>
        </div>
        
        {{-- Project Info Card --}}
        <div class="bg-purple-50 rounded-xl p-4 mb-6 border border-purple-200">
            <div class="flex items-center space-x-3">
                <div class="w-10 h-10 bg-purple-100 rounded-xl flex items-center justify-center">
                    <i class="ri-briefcase-4-line text-purple-600 text-lg"></i>
                </div>
                <div class="flex-1">
                    <div class="font-bold text-gray-900 text-sm" id="modalProjectName"></div>
                    <div class="text-xs text-gray-600">Project selected for BAST generation</div>
                </div>
            </div>
        </div>

        {{-- Project Details --}}
        <div class="space-y-4 mb-6">
            {{-- Description Section --}}
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-2 text-sm flex items-center">
                    <i class="ri-file-text-line mr-2 text-gray-600"></i>
                    Project Description
                </h4>
                <div id="modalProjectDescription" class="text-sm text-gray-700 leading-relaxed">
                    Loading description...
                </div>
            </div>

            {{-- Requirements Section --}}
            <div class="bg-gray-50 rounded-lg p-4">
                <h4 class="font-semibold text-gray-900 mb-2 text-sm flex items-center">
                    <i class="ri-task-line mr-2 text-gray-600"></i>
                    Project Requirements/Deliverables
                </h4>
                <div id="modalProjectRequirements" class="text-sm text-gray-700">
                    Loading requirements...
                </div>
            </div>

            {{-- Client & Timeline --}}
            <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-2 text-sm flex items-center">
                        <i class="ri-user-3-line mr-2 text-gray-600"></i>
                        Client Information
                    </h4>
                    <div id="modalClientName" class="text-sm text-gray-700">Loading...</div>
                </div>
                <div class="bg-gray-50 rounded-lg p-4">
                    <h4 class="font-semibold text-gray-900 mb-2 text-sm flex items-center">
                        <i class="ri-calendar-check-line mr-2 text-gray-600"></i>
                        Project Timeline
                    </h4>
                    <div id="modalTimeline" class="text-sm text-gray-700">Loading...</div>
                </div>
            </div>
        </div>

        {{-- Confirmation Message --}}
        <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-4 mb-6">
            <div class="flex items-start space-x-2">
                <i class="ri-alert-line text-yellow-600 mt-0.5"></i>
                <div>
                    <h4 class="font-semibold text-yellow-800 text-sm">Confirmation Required</h4>
                    <p class="text-yellow-700 text-sm mt-1">
                        Please verify that all project information above is correct. Once you generate the BAST document, 
                        it will be created based on this information and should be reviewed carefully.
                    </p>
                </div>
            </div>
        </div>
        
        {{-- Action Buttons --}}
        <div class="flex space-x-3">
                <button id="cancelModalButton"
                    class="flex-1 px-4 py-2 bg-gray-100 hover:bg-gray-200 text-gray-700 font-medium text-sm rounded-lg transition-all duration-200">
                    Cancel
                </button>
                <button id="confirmGenerateButton"
                    class="flex-1 px-4 py-2 text-white font-medium text-sm rounded-lg transition-all duration-200 bg-purple-600 hover:bg-purple-700 shadow-lg hover:shadow-xl">
                <i class="ri-download-line mr-2"></i>
                Yes, Generate BAST
                </button>
        </div>
    </div>
</div>

<style>
    @keyframes fadeInUp {
        from {
            opacity: 0;
            transform: translateY(30px);
        }
        to {
            opacity: 1;
            transform: translateY(0);
        }
    }

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

    .animate-fadeInUp {
        animation: fadeInUp 0.6s ease-out;
    }

    .animate-slideInDown {
        animation: slideInDown 0.4s ease-out;
    }

    .animation-delay-200 {
        animation-delay: 0.2s;
        animation-fill-mode: both;
    }

    /* Enhanced Pagination Styles */
    .pagination-wrapper .pagination {
        display: flex;
        align-items: center;
        space-x: 1rem;
    }

    .pagination-wrapper .page-link {
        padding: 0.5rem 1rem;
        background: white;
        border: 1px solid #e5e7eb;
        border-radius: 0.5rem;
        color: #374151;
        font-weight: 500;
        transition: all 0.2s;
    }

    .pagination-wrapper .page-link:hover {
        background: #f3f4f6;
        border-color: #d1d5db;
        transform: translateY(-1px);
    }

    .pagination-wrapper .page-item.active .page-link {
        background: linear-gradient(135deg, #7c3aed, #5b21b6);
        color: white;
        border-color: #7c3aed;
    }
</style>
@endsection

@push('scripts')
<script>
    const confirmationModal = document.getElementById('confirmationModal');
    const modalIconContainer = document.getElementById('modalIconContainer');
    const modalIcon = document.getElementById('modalIcon');
    const modalTitle = document.getElementById('modalTitle');
    const modalDocTypeStrong = document.getElementById('modalDocTypeStrong');
    const modalProjectName = document.getElementById('modalProjectName');
    const modalProjectDescription = document.getElementById('modalProjectDescription');
    const modalProjectRequirements = document.getElementById('modalProjectRequirements');
    const modalClientName = document.getElementById('modalClientName');
    const modalTimeline = document.getElementById('modalTimeline');
    const confirmGenerateButton = document.getElementById('confirmGenerateButton');
    const cancelModalButton = document.getElementById('cancelModalButton');

    let generationUrl = '';

    function showConfirmModal(projectId, projectName, docType, actionUrl) {
        generationUrl = actionUrl;

        // Set basic info immediately
        modalProjectName.textContent = projectName;
        modalDocTypeStrong.textContent = docType;
        
        // Configure modal based on document type
        if (docType.toLowerCase() === 'bast') {
            modalTitle.textContent = 'Confirm BAST Generation';
            modalIcon.className = 'ri-file-shield-2-line text-purple-600 text-2xl';
        }

        // Show modal with loading state
        showModalWithAnimation();
        
        // Fetch project details
        fetchProjectDetails(projectId);
    }

    function fetchProjectDetails(projectId) {
        // Show loading states
        modalProjectDescription.innerHTML = '<div class="flex items-center"><i class="ri-loader-4-line animate-spin mr-2"></i>Loading description...</div>';
        modalProjectRequirements.innerHTML = '<div class="flex items-center"><i class="ri-loader-4-line animate-spin mr-2"></i>Loading requirements...</div>';
        modalClientName.innerHTML = '<div class="flex items-center"><i class="ri-loader-4-line animate-spin mr-2"></i>Loading...</div>';
        modalTimeline.innerHTML = '<div class="flex items-center"><i class="ri-loader-4-line animate-spin mr-2"></i>Loading...</div>';

        fetch(`/projects/${projectId}`, {
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
            console.log('Project details fetched:', project);
            populateModalWithProjectData(project);
        })
        .catch(error => {
            console.error('Error fetching project details:', error);
            showErrorInModal();
        });
    }

    function populateModalWithProjectData(project) {
        // Populate description
        if (project.description && project.description.trim() !== '' && project.description !== '-') {
            modalProjectDescription.innerHTML = `<p class="whitespace-pre-wrap">${project.description}</p>`;
        } else {
            modalProjectDescription.innerHTML = '<p class="text-gray-500 italic">No description provided</p>';
        }

        // Populate requirements
        if (project.requirements && project.requirements.length > 0) {
            const requirementsList = project.requirements.map((req, index) => {
                const description = typeof req === 'object' ? req.description : req;
                const isCompleted = typeof req === 'object' ? req.is_completed : false;
                const statusIcon = isCompleted ? 
                    '<i class="ri-checkbox-circle-fill text-green-500 mr-2"></i>' : 
                    '<i class="ri-checkbox-circle-line text-gray-400 mr-2"></i>';
                const statusClass = isCompleted ? 'line-through text-gray-500' : '';
                
                return `<div class="flex items-start mb-2">
                    ${statusIcon}
                    <span class="${statusClass}">${description}</span>
                    ${isCompleted ? '<span class="ml-auto text-xs bg-green-100 text-green-700 px-2 py-1 rounded-full">Completed</span>' : ''}
                </div>`;
            }).join('');
            
            modalProjectRequirements.innerHTML = requirementsList;
        } else {
            modalProjectRequirements.innerHTML = '<p class="text-gray-500 italic">No requirements specified</p>';
        }

        // Populate client info
        modalClientName.innerHTML = project.client_name || 'Not specified';

        // Populate timeline
        let timelineHtml = '';
        if (project.start_date) {
            const startDate = new Date(project.start_date).toLocaleDateString('en-US', {
                year: 'numeric', month: 'short', day: 'numeric'
            });
            timelineHtml += `<div class="mb-1"><strong>Started:</strong> ${startDate}</div>`;
        }
        if (project.end_date) {
            const endDate = new Date(project.end_date).toLocaleDateString('en-US', {
                year: 'numeric', month: 'short', day: 'numeric'
            });
            timelineHtml += `<div class="mb-1"><strong>Deadline:</strong> ${endDate}</div>`;
        }
        if (project.budget && project.budget > 0) {
            const budget = new Intl.NumberFormat('id-ID', {
                style: 'currency',
                currency: 'IDR',
                minimumFractionDigits: 0
            }).format(project.budget);
            timelineHtml += `<div><strong>Budget:</strong> ${budget}</div>`;
        }
        
        modalTimeline.innerHTML = timelineHtml || 'No timeline information';
    }

    function showErrorInModal() {
        modalProjectDescription.innerHTML = '<p class="text-red-500">Failed to load description</p>';
        modalProjectRequirements.innerHTML = '<p class="text-red-500">Failed to load requirements</p>';
        modalClientName.innerHTML = '<p class="text-red-500">Failed to load client info</p>';
        modalTimeline.innerHTML = '<p class="text-red-500">Failed to load timeline</p>';
    }

    function showModalWithAnimation() {
        confirmationModal.classList.remove('hidden');
        confirmationModal.style.opacity = '0';
        
        // Animate in
        requestAnimationFrame(() => {
            confirmationModal.style.transition = 'opacity 0.3s ease';
            confirmationModal.style.opacity = '1';
            
            const modalContent = confirmationModal.querySelector('.relative.mx-auto');
            if (modalContent) {
                modalContent.style.transform = 'scale(1)';
            }
        });
    }

    function hideModal() {
        confirmationModal.style.opacity = '0';
        
        const modalContent = confirmationModal.querySelector('.relative.mx-auto');
        if (modalContent) {
            modalContent.style.transform = 'scale(0.9)';
        }
        
        setTimeout(() => {
            confirmationModal.classList.add('hidden');
        }, 300);
    }

    // Event listeners
    confirmGenerateButton.addEventListener('click', () => {
        if (generationUrl) {
            // Show loading state
            const originalContent = confirmGenerateButton.innerHTML;
            confirmGenerateButton.innerHTML = '<i class="ri-loader-4-line animate-spin mr-2"></i>Generating...';
            confirmGenerateButton.disabled = true;
            
            // Open in new tab
            window.open(generationUrl, '_blank');
            
            // Reset button after delay
            setTimeout(() => {
                confirmGenerateButton.innerHTML = originalContent;
                confirmGenerateButton.disabled = false;
                hideModal();
            }, 1000);
        }
    });

    cancelModalButton.addEventListener('click', hideModal);

    // Keyboard shortcuts
    window.addEventListener('keydown', (event) => {
        if (event.key === 'Escape' && !confirmationModal.classList.contains('hidden')) {
            hideModal();
        }
    });

    // Click outside to close
    confirmationModal.addEventListener('click', (event) => {
        if (event.target === confirmationModal) {
            hideModal();
        }
    });

    // Auto-hide alerts
    document.addEventListener('DOMContentLoaded', () => {
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
    });
</script>
@endpush