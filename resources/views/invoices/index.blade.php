@extends('layouts.app')

@section('title', 'Invoice Generator - DjokiHub')

{{-- Bagian Navigasi Utama --}}
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
            <div class="mx-auto w-20 h-20 bg-sky-100 rounded-2xl flex items-center justify-center mb-6">
                <i class="ri-bill-line text-sky-600 text-3xl"></i>
                        </div>
            
            {{-- Title --}}
            <h1 class="text-4xl md:text-5xl lg:text-6xl font-bold text-gray-900 mb-4">
                Invoice Generation
            </h1>
            <p class="text-xl text-gray-600 mb-8 max-w-2xl mx-auto">
                Generate professional Invoice documents for your completed projects
            </p>
            
            {{-- Stats Cards --}}
            <div class="grid grid-cols-1 md:grid-cols-3 gap-6 max-w-4xl mx-auto mt-12">
                <div class="bg-white border border-gray-200 rounded-2xl p-6 text-center shadow-sm hover:shadow-md transition-all duration-300">
                    <div class="text-3xl font-bold text-gray-900 mb-2">{{ $projects->total() }}</div>
                    <div class="text-gray-600 text-sm">Total Projects</div>
                    </div>
                <div class="bg-white border border-gray-200 rounded-2xl p-6 text-center shadow-sm hover:shadow-md transition-all duration-300">
                    <div class="text-3xl font-bold text-sky-600 mb-2">{{ $projects->filter(fn($p) => $p->budget && $p->client_name)->count() }}</div>
                    <div class="text-gray-600 text-sm">Ready for Invoice</div>
                </div>
                <div class="bg-white border border-gray-200 rounded-2xl p-6 text-center shadow-sm hover:shadow-md transition-all duration-300">
                    <div class="text-3xl font-bold text-blue-600 mb-2">{{ $projects->whereIn('status', ['Active', 'On-going', 'Completed'])->count() }}</div>
                    <div class="text-gray-600 text-sm">Available Projects</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="bg-gray-50 py-12">
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">

        @if(session('success'))
            <div id="alert-success" class="mb-8 relative transform transition-opacity duration-300 ease-in-out">
                <div class="bg-white border-l-4 border-green-500 rounded-xl shadow-lg overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-green-100 rounded-full flex items-center justify-center">
                                    <i class="ri-checkbox-circle-line text-green-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-lg font-semibold text-green-800">Sukses!</p>
                                <p class="text-sm text-green-700 mt-1">{{ session('success') }}</p>
                            </div>
                            <div class="ml-auto">
                                <button type="button" class="text-green-400 hover:text-green-600 transition-colors p-2 rounded-full hover:bg-green-50" onclick="document.getElementById('alert-success').style.opacity='0'; setTimeout(() => document.getElementById('alert-success').style.display='none', 300);">
                                    <i class="ri-close-line text-lg"></i>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        @endif

        @if(session('error'))
            <div id="alert-error" class="mb-8 relative transform transition-opacity duration-300 ease-in-out">
                <div class="bg-white border-l-4 border-red-500 rounded-xl shadow-lg overflow-hidden">
                    <div class="p-6">
                        <div class="flex items-center">
                            <div class="flex-shrink-0">
                                <div class="w-10 h-10 bg-red-100 rounded-full flex items-center justify-center">
                                    <i class="ri-error-warning-line text-red-600 text-xl"></i>
                                </div>
                            </div>
                            <div class="ml-4">
                                <p class="text-lg font-semibold text-red-800">Error!</p>
                                <p class="text-sm text-red-700 mt-1">{{ session('error') }}</p>
                            </div>
                            <div class="ml-auto">
                                 <button type="button" class="text-red-400 hover:text-red-600 transition-colors p-2 rounded-full hover:bg-red-50" onclick="document.getElementById('alert-error').style.opacity='0'; setTimeout(() => document.getElementById('alert-error').style.display='none', 300);">
                                    <i class="ri-close-line text-lg"></i>
                                </button>
                            </div>
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
                        <div class="w-12 h-12 bg-sky-100 rounded-xl flex items-center justify-center">
                            <i class="ri-bill-line text-sky-600 text-xl"></i>
                        </div>
                    <div>
                            <h2 class="text-2xl font-bold text-gray-900">Project Selection for Invoice</h2>
                            <p class="text-gray-600 text-sm mt-1">Choose a project with complete budget and client data to generate an Invoice</p>
                        </div>
                    </div>
                    <div class="hidden lg:flex items-center space-x-2 bg-gray-50 rounded-xl px-4 py-2">
                        <div class="w-3 h-3 bg-sky-500 rounded-full"></div>
                        <span class="text-gray-700 text-sm font-medium">Ready for Invoice</span>
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
                            <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Budget</th>
                            <th scope="col" class="px-4 py-3 text-left text-xs font-bold text-gray-700 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-4 py-3 text-center text-xs font-bold text-gray-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="divide-y divide-gray-100">
                        @forelse ($projects as $project)
                        <tr class="hover:bg-gray-50 transition-all duration-300 group">
                            <td class="px-4 py-4">
                                <div class="flex items-center space-x-3">
                                    <div class="w-8 h-8 bg-sky-100 rounded-lg flex items-center justify-center shadow-sm">
                                        <i class="ri-briefcase-4-line text-sky-600 text-sm"></i>
                                    </div>
                                    <div>
                                        <div class="text-sm font-bold text-gray-900 group-hover:text-sky-700 transition-colors">
                                            {{ Str::limit($project->project_name, 30) }}
                                        </div>
                                        <div class="text-xs font-medium text-sky-600 bg-sky-100 px-2 py-1 rounded-full inline-block mt-1">
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
                                <div class="text-sm font-bold text-sky-600">
                                    @if(!is_null($project->budget) && is_numeric($project->budget))
                                        Rp {{ number_format(floatval($project->budget), 0, ',', '.') }}
                                    @else 
                                        <span class="text-gray-400 text-xs">Not specified</span>
                                    @endif
                                </div>
                                @if($project->end_date)
                                <div class="text-xs text-gray-500 mt-1 flex items-center">
                                    <i class="ri-calendar-line mr-1 text-xs"></i>
                                    Due: {{ \Carbon\Carbon::parse($project->end_date)->format('M d, Y') }}
                                </div>
                                @endif
                            </td>
                            <td class="px-4 py-4">
                                @php
                                    $statusClasses = [
                                        'Active' => 'bg-green-100 text-green-800 border-green-200',
                                        'On-going' => 'bg-blue-100 text-blue-800 border-blue-200',
                                        'Pending' => 'bg-yellow-100 text-yellow-800 border-yellow-200',
                                        'Draft' => 'bg-gray-100 text-gray-800 border-gray-200',
                                        'Completed' => 'bg-sky-100 text-sky-800 border-sky-200',
                                        'Cancelled' => 'bg-red-100 text-red-800 border-red-200'
                                    ];
                                    $statusClass = $statusClasses[$project->status] ?? 'bg-gray-100 text-gray-800 border-gray-200';
                                @endphp
                                <div class="flex items-center space-x-2">
                                    <span class="px-3 py-1 text-xs font-bold rounded-lg border {{ $statusClass }} shadow-sm">
                                    {{ $project->status }}
                                </span>
                                    @if($project->budget && $project->client_name)
                                        <div class="w-2 h-2 bg-sky-400 rounded-full" title="Ready for Invoice"></div>
                                    @endif
                                </div>
                            </td>
                            <td class="px-4 py-4">
                                <div class="flex justify-center">
                                @if($project->budget && $project->client_name)
                                    <button onclick="showConfirmModal('{{ $project->id }}', '{{ addslashes($project->project_name) }}', '{{ addslashes($project->client_name ?? 'N/A') }}', '{{ $project->budget ? 'Rp ' . number_format($project->budget, 0, ',', '.') : 'Not specified' }}', '{{ $project->status }}', '{{ $project->order_id ?? ('#PRJ-' . str_pad($project->id, 4, '0', STR_PAD_LEFT)) }}', '{{ addslashes($project->description ?? 'No description provided') }}', 'Invoice', '{{ route('invoices.generate', $project->id) }}')"
                                       class="group relative inline-flex items-center px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white font-medium text-sm rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105">
                                        <i class="ri-bill-line mr-1 text-sm"></i>
                                        <span>Generate Invoice</span>
                                </button>
                                @else
                                    <div class="inline-flex items-center px-4 py-2 bg-gray-100 text-gray-500 font-medium text-sm rounded-lg cursor-not-allowed">
                                        <i class="ri-lock-line mr-1 text-sm"></i>
                                        <span>Data Incomplete</span>
                                    </div>
                                @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="5" class="px-4 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-16 h-16 bg-sky-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="ri-folder-open-line text-3xl text-sky-400"></i>
                                    </div>
                                    <h3 class="text-lg font-bold text-gray-900 mb-2">No Projects Available</h3>
                                    <p class="text-gray-500 mb-4 max-w-md text-sm">There are no projects available for Invoice generation at this time.</p>
                                    <a href="{{ route('projects.index') }}" class="inline-flex items-center px-4 py-2 bg-sky-600 hover:bg-sky-700 text-white font-medium text-sm rounded-lg shadow-md hover:shadow-lg transition-all duration-300 transform hover:scale-105">
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
<div id="confirmModal" class="fixed inset-0 z-50 hidden overflow-y-auto">
    <div class="flex items-end justify-center min-h-screen pt-4 px-4 pb-20 text-center sm:block sm:p-0">
        <div class="fixed inset-0 bg-black bg-opacity-75 transition-opacity" aria-hidden="true"></div>
        
        <span class="hidden sm:inline-block sm:align-middle sm:h-screen" aria-hidden="true">&#8203;</span>
        
        <div class="inline-block align-bottom bg-white rounded-3xl text-left overflow-hidden shadow-2xl transform transition-all sm:my-8 sm:align-middle sm:max-w-3xl sm:w-full">
            <div class="bg-white px-8 pt-8 pb-4 sm:p-8 sm:pb-4 max-h-[90vh] overflow-y-auto">
                <div class="sm:flex sm:items-start">
                    <div class="mx-auto flex-shrink-0 flex items-center justify-center h-16 w-16 rounded-2xl bg-sky-100 sm:mx-0 sm:h-16 sm:w-16 mb-6">
                        <i class="ri-bill-line text-sky-600 text-2xl"></i>
                    </div>
                    <div class="mt-3 text-center sm:mt-0 sm:ml-6 sm:text-left flex-1">
                        <h3 class="text-2xl leading-6 font-bold text-gray-900 mb-6" id="modal-title">
                            Generate Invoice
                        </h3>
                        
                        <!-- Project Details -->
                        <div id="modalContent">
                            <div class="bg-gray-50 rounded-2xl p-6 mb-6">
                                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-500 uppercase mb-2">Project Information</h4>
                                        <div class="space-y-3">
                                            <div>
                                                <label class="text-xs font-medium text-gray-500">Project Name</label>
                                                <div id="projectName" class="text-sm font-bold text-gray-900"></div>
                                            </div>
                                            <div>
                                                <label class="text-xs font-medium text-gray-500">Order ID</label>
                                                <div id="orderId" class="text-sm font-medium text-sky-600"></div>
                                            </div>
                                            <div>
                                                <label class="text-xs font-medium text-gray-500">Client</label>
                                                <div id="clientName" class="text-sm font-medium text-gray-900"></div>
                                            </div>
                                            <div>
                                                <label class="text-xs font-medium text-gray-500">Budget</label>
                                                <div id="projectBudget" class="text-sm font-bold text-sky-600"></div>
                                            </div>
                                        </div>
                                    </div>
                                    <div>
                                        <h4 class="text-sm font-bold text-gray-500 uppercase mb-2">Project Details</h4>
                                        <div class="space-y-3">
                                            <div>
                                                <label class="text-xs font-medium text-gray-500">Status</label>
                                                <div id="projectStatus" class="text-sm"></div>
                                            </div>
                                            <div>
                                                <label class="text-xs font-medium text-gray-500">Description</label>
                                                <div id="projectDescription" class="text-sm text-gray-700 bg-white border border-gray-200 rounded-lg p-3 mt-2"></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Confirmation Message -->
                        <div id="confirmationMessage">
                            <div class="bg-sky-50 border border-sky-200 rounded-xl p-6 mb-6">
                                <div class="flex items-center">
                                    <div class="flex-shrink-0">
                                        <i class="ri-information-line text-sky-600 text-xl"></i>
            </div>
                                    <div class="ml-3">
                                        <h4 class="text-sm font-bold text-sky-800">Ready to Generate Invoice</h4>
                                        <p class="text-sm text-sky-700 mt-1">
                                            This will generate a professional Invoice PDF document for this project. 
                                            The document will include all project details, budget information, and client data.
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <div id="modalFooter" class="bg-gray-50 px-8 py-6 sm:flex sm:flex-row-reverse border-t border-gray-200">
                <button id="confirmButton" 
                        class="w-full inline-flex justify-center items-center rounded-xl border border-transparent shadow-lg px-6 py-3 bg-sky-600 text-base font-bold text-white hover:bg-sky-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 sm:ml-3 sm:w-auto sm:text-sm transition-all duration-300 transform hover:scale-105">
                    <i class="ri-bill-line mr-2"></i>
                    <span>Yes, Generate Invoice</span>
                </button>
                <button onclick="hideConfirmModal()" 
                        class="mt-3 w-full inline-flex justify-center rounded-xl border border-gray-300 shadow-sm px-6 py-3 bg-white text-base font-medium text-gray-700 hover:bg-gray-50 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-sky-500 sm:mt-0 sm:ml-3 sm:w-auto sm:text-sm transition-all duration-300">
                    Cancel
                </button>
            </div>
        </div>
    </div>
</div>

<script>
let currentActionUrl = '';

function showConfirmModal(projectId, projectName, clientName, budget, status, orderId, description, documentType, actionUrl) {
    currentActionUrl = actionUrl;
    
    // Show modal
    document.getElementById('confirmModal').classList.remove('hidden');
    
    // Update modal title
    document.getElementById('modal-title').textContent = `Generate ${documentType}`;
    
    // Populate project details directly from parameters
    document.getElementById('projectName').textContent = projectName || 'N/A';
    document.getElementById('orderId').textContent = orderId || `#PRJ-${String(projectId).padStart(4, '0')}`;
    document.getElementById('clientName').textContent = clientName || 'N/A';
    document.getElementById('projectBudget').textContent = budget || 'Not specified';
    document.getElementById('projectDescription').textContent = description || 'No description provided';
    
    // Status with styling
    const statusElement = document.getElementById('projectStatus');
    const statusClasses = {
        'Active': 'bg-green-100 text-green-800 border-green-200',
        'On-going': 'bg-blue-100 text-blue-800 border-blue-200',
        'Pending': 'bg-yellow-100 text-yellow-800 border-yellow-200',
        'Draft': 'bg-gray-100 text-gray-800 border-gray-200',
        'Completed': 'bg-sky-100 text-sky-800 border-sky-200',
        'Cancelled': 'bg-red-100 text-red-800 border-red-200'
    };
    const statusClass = statusClasses[status] || 'bg-gray-100 text-gray-800 border-gray-200';
    statusElement.innerHTML = `<span class="px-3 py-1 text-xs font-bold rounded-lg border ${statusClass}">${status || 'Unknown'}</span>`;
    
    // Setup confirm button
    const confirmButton = document.getElementById('confirmButton');
    confirmButton.onclick = function() {
        window.location.href = currentActionUrl;
    };
}

function hideConfirmModal() {
    document.getElementById('confirmModal').classList.add('hidden');
    currentActionUrl = '';
}

// Close modal when clicking outside
document.getElementById('confirmModal').addEventListener('click', function(e) {
    if (e.target === this) {
        hideConfirmModal();
    }
});

// Close modal with ESC key
document.addEventListener('keydown', function(e) {
    if (e.key === 'Escape') {
        const modal = document.getElementById('confirmModal');
        if (!modal.classList.contains('hidden')) {
            hideConfirmModal();
        }
    }
});
</script>
@endsection