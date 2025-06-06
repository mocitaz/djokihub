@php
    $currentUser = Auth::user();
    $isAdmin = $currentUser && $currentUser->role === 'admin';
@endphp

@extends('layouts.app')

@section('title', 'Meeting Notes - DjokiHub')
@section('page_title', 'Meeting Notes')

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
<div class="bg-slate-50 min-h-screen">
    {{-- Enhanced Header Section --}}
    <div class="bg-white shadow-lg border-b border-slate-200">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between md:items-center py-8 border-b border-slate-200">
                <div class="mb-4 md:mb-0">
                    <h1 class="text-2xl lg:text-3xl font-bold text-slate-800 mb-2">Meeting Notes</h1>
                    <p class="text-slate-600 text-base lg:text-lg">All your meeting notes in one place. Track, review, and collaborate on meetings easily.</p>
                    {{-- Statistik ringkas jika ada --}}
                    {{-- <div class="flex items-center mt-3 space-x-6 text-sm text-slate-500">
                        <div class="flex items-center">
                            <i class="ri-calendar-event-line mr-2 text-blue-500"></i>
                            <span>12 Meetings This Month</span>
                        </div>
                        <div class="flex items-center">
                            <i class="ri-checkbox-circle-line mr-2 text-green-500"></i>
                            <span>5 Completed</span>
                        </div>
                    </div> --}}
                </div>
                <div class="flex flex-col sm:flex-row items-stretch sm:items-center gap-4 w-full sm:w-auto">
                    {{-- Header Actions --}}
                    <div class="relative w-full sm:w-auto flex-grow sm:flex-grow-0">
                        <div class="absolute inset-y-0 left-0 flex items-center pl-4 pointer-events-none">
                            <i class="ri-search-line text-slate-400 text-lg" aria-hidden="true"></i>
                        </div>
                        <input type="text" id="meeting-search-input" class="w-full md:w-72 pl-12 pr-4 py-3 text-sm border border-slate-300 rounded-xl shadow-sm focus:ring-2 focus:ring-primary-300 focus:border-primary-500 transition-all duration-200 placeholder-slate-400 bg-white" placeholder="Search by topic...">
                    </div>
                    <button id="add-meeting-note-button" class="btn-primary w-full sm:w-auto px-4 py-2 flex items-center justify-center space-x-2 text-sm font-medium rounded-lg shadow hover:shadow-md transition-all duration-200 transform hover:scale-105">
                        <i class="ri-add-circle-line text-base" aria-hidden="true"></i>
                        <span>New Meeting Note</span>
                    </button>
                </div>
            </div>
        </div>
    </div>

    {{-- Main Content --}}
    <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
        {{-- Notifikasi/Alert --}}
        <div id="page-notification-area" class="mb-6 empty:hidden"></div>

        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-slate-200">
            <div class="overflow-x-auto styled-scrollbar">
                <table class="min-w-full divide-y divide-slate-200">
                    <thead class="bg-slate-50">
                        <tr>
                            <th scope="col" class="px-4 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Topic</th>
                            <th scope="col" class="px-4 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Date & Time</th>
                            <th scope="col" class="px-4 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Creator</th>
                            <th scope="col" class="px-4 py-4 text-center text-xs font-bold text-slate-700 uppercase tracking-wider">Participants</th>
                            <th scope="col" class="px-4 py-4 text-left text-xs font-bold text-slate-700 uppercase tracking-wider">Status</th>
                            <th scope="col" class="px-4 py-4 text-right text-xs font-bold text-slate-700 uppercase tracking-wider">Actions</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-slate-100" id="meeting-notes-table-body">
                        @forelse ($meetingNotes as $note)
                        <tr class="meeting-note-row hover:bg-primary-50/40 transition-colors duration-150" data-note-id="{{ $note->id }}">
                            <td class="px-4 py-4">
                                <div class="text-sm font-semibold text-slate-800 truncate">{{ Str::limit($note->topic, 40) }}</div>
                                <div class="text-xs text-slate-500 mt-1.5">{{ Str::limit(strip_tags($note->notes_content ?? ($note->notes_html ?? '')), 50) }}</div>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-slate-600">
                                {{ \Carbon\Carbon::parse($note->meeting_datetime)->format('d M Y, H:i') }}
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-sm text-slate-600">{{ $note->creator->name ?? 'N/A' }}</td>
                            <td class="px-4 py-4 whitespace-nowrap text-center">
                                @if($note->participants && $note->participants->count() > 0)
                                <div class="flex items-center justify-center -space-x-2 overflow-hidden">
                                    @foreach($note->participants->take(3) as $participant)
                                        <img src="{{ $participant->profile_photo_url ?? 'https://ui-avatars.com/api/?name='.urlencode($participant->name).'&color=FFFFFF&background=4F46E5&size=32&font-size=0.4&bold=true' }}" class="w-7 h-7 rounded-full border-2 border-white object-cover shadow-sm" alt="{{ $participant->name }}" title="{{ $participant->name }}">
                                    @endforeach
                                    @if($note->participants->count() > 3)
                                    <div class="w-7 h-7 rounded-full border-2 border-white bg-slate-200 flex items-center justify-center text-xxs text-slate-600 font-semibold shadow-sm">+{{ $note->participants->count() - 3 }}</div>
                                    @endif
                                </div>
                                @else
                                <span class="text-xs text-slate-400 italic">-</span>
                                @endif
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap">
                                @php
                                    $statusClasses = [
                                        'Upcoming' => 'bg-blue-100 text-blue-700 ring-blue-600/20',
                                        'In Progress' => 'bg-yellow-100 text-yellow-700 ring-yellow-600/20',
                                        'Completed' => 'bg-green-100 text-green-700 ring-green-600/20',
                                        'Cancelled' => 'bg-red-100 text-red-700 ring-red-600/20',
                                    ];
                                    $statusClass = $statusClasses[$note->status] ?? 'bg-slate-100 text-slate-700 ring-slate-600/20';
                                @endphp
                                <span class="px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full {{ $statusClass }} ring-1">
                                    {{ $note->status }}
                                </span>
                            </td>
                            <td class="px-4 py-4 whitespace-nowrap text-right text-sm font-medium">
                                <div class="flex justify-end items-center space-x-1">
                                    <button type="button" class="text-slate-400 hover:text-blue-600 p-1.5 rounded-md hover:bg-blue-100 transition-colors view-meeting-note-button" data-note-id="{{ $note->id }}" title="View Details">
                                        <i class="ri-eye-fill text-lg" aria-hidden="true"></i>
                                        <span class="sr-only">View</span>
                                    </button>
                                    @if(Auth::id() == $note->created_by_user_id || $isAdmin)
                                    <button type="button" class="text-slate-400 hover:text-primary p-1.5 rounded-md hover:bg-primary-50 transition-colors edit-meeting-note-button" data-note-id="{{ $note->id }}" title="Edit Note">
                                        <i class="ri-pencil-fill text-lg" aria-hidden="true"></i>
                                        <span class="sr-only">Edit</span>
                                    </button>
                                    <button type="button" class="text-slate-400 hover:text-red-600 p-1.5 rounded-md hover:bg-red-100 transition-colors delete-meeting-note-button" data-note-id="{{ $note->id }}" data-note-topic="{{ $note->topic }}" title="Delete Note">
                                        <i class="ri-delete-bin-5-fill text-lg" aria-hidden="true"></i>
                                        <span class="sr-only">Delete</span>
                                    </button>
                                    @endif
                                </div>
                            </td>
                        </tr>
                        @empty
                        <tr>
                            <td colspan="6" class="px-6 py-16 text-center">
                                <div class="flex flex-col items-center">
                                    <div class="w-20 h-20 bg-slate-100 rounded-full flex items-center justify-center mb-4">
                                        <i class="ri-chat-off-line text-4xl text-slate-300" aria-hidden="true"></i>
                                    </div>
                                    <h3 class="text-lg font-medium text-slate-600 mb-2">No Meeting Notes Found</h3>
                                    <p class="text-sm text-slate-400 mb-4">Get started by adding a new meeting note.</p>
                                    <button id="add-meeting-note-from-empty-button" class="btn-primary text-sm px-4 py-2 flex items-center space-x-2">
                                        <i class="ri-add-circle-line" aria-hidden="true"></i><span>Create New Note</span>
                                    </button>
                                </div>
                            </td>
                        </tr>
                        @endforelse
                    </tbody>
                </table>
            </div>
            @if ($meetingNotes->hasPages())
            <div class="bg-slate-50 px-6 py-4 border-t border-slate-200 rounded-b-2xl">
                {{ $meetingNotes->links() }}
            </div>
            @endif
        </div>
    </div>
</div>

{{-- Modals --}}
<div id="add-edit-meeting-note-modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 hidden items-center justify-center z-[100] p-4 animate-fadeIn" style="--tw-bg-opacity: 0.75;">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full mx-auto my-4 max-h-[95vh] flex flex-col transform transition-all duration-300 ease-out scale-95 opacity-0" id="add-edit-meeting-note-modal-content">
        <div class="flex justify-between items-center p-4 md:p-5 border-b border-gray-200 sticky top-0 bg-white z-10 rounded-t-xl">
            <h3 class="text-lg font-semibold text-gray-800" id="modal-title">Add New Meeting Note</h3>
            <button id="close-modal-button" class="text-gray-400 hover:text-red-600 p-2 rounded-full hover:bg-red-100 transition-colors">
                <span class="sr-only">Close</span><i class="ri-close-line text-xl" aria-hidden="true"></i>
            </button>
        </div>
        <form id="meeting-note-form" enctype="multipart/form-data" class="overflow-y-auto flex-grow styled-scrollbar">
            @csrf
            <input type="hidden" name="_method" id="form-method-input" value="POST">
            <input type="hidden" name="meeting_note_id" id="meeting-note-id-input">

            <div class="p-4 md:p-5 space-y-4">
                <div>
                    <label for="topic" class="block text-sm font-medium text-gray-700 mb-1">Topic <span class="text-red-500">*</span></label>
                    <input type="text" id="topic" name="topic" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary shadow-sm placeholder-gray-400" required>
                    <p class="text-xs text-red-500 mt-1 hidden" data-error-for="topic"></p>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="meeting_datetime" class="block text-sm font-medium text-gray-700 mb-1">Date and Time <span class="text-red-500">*</span></label>
                        <input type="datetime-local" id="meeting_datetime" name="meeting_datetime" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary shadow-sm" required>
                        <p class="text-xs text-red-500 mt-1 hidden" data-error-for="meeting_datetime"></p>
                    </div>
                    <div>
                        <label for="location" class="block text-sm font-medium text-gray-700 mb-1">Location / Link</label>
                        <input type="text" id="location" name="location" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary shadow-sm placeholder-gray-400" placeholder="e.g., Office Room A or https://zoom.us/j/...">
                        <p class="text-xs text-red-500 mt-1 hidden" data-error-for="location"></p>
                    </div>
                </div>
                <div class="grid grid-cols-1 md:grid-cols-2 gap-4">
                    <div>
                        <label for="notes_content" class="block text-sm font-medium text-gray-700 mb-1">Notes <span class="text-red-500">*</span></label>
                        <textarea id="notes_content" name="notes_content" rows="5" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary shadow-sm placeholder-gray-400" required></textarea>
                        <p class="text-xs text-red-500 mt-1 hidden" data-error-for="notes_content"></p>
                    </div>
                    <div class="space-y-4">
                        <div>
                            <label for="status" class="block text-sm font-medium text-gray-700 mb-1">Status <span class="text-red-500">*</span></label>
                            <select id="status" name="status" class="w-full px-3 py-2 border border-gray-300 rounded-lg text-sm focus:ring-primary focus:border-primary shadow-sm appearance-none pr-8 bg-white" required>
                                <option value="Upcoming">Upcoming</option>
                                <option value="In Progress">In Progress</option>
                                <option value="Completed">Completed</option>
                                <option value="Cancelled">Cancelled</option>
                            </select>
                            <p class="text-xs text-red-500 mt-1 hidden" data-error-for="status"></p>
                        </div>
                        <div>
                            <label for="participants" class="block text-sm font-medium text-gray-700 mb-1">Participants</label>
                            <select id="participants" name="participants[]" multiple class="w-full text-sm border-gray-300 rounded-lg focus:ring-primary focus:border-primary shadow-sm min-h-[120px]">
                                @foreach($allUsers as $user)
                                    <option value="{{ $user->id }}">{{ $user->name }} ({{ $user->email }})</option>
                                @endforeach
                            </select>
                            <p class="text-xs text-gray-500 mt-1">Hold Ctrl/Cmd to select multiple</p>
                            <p class="text-xs text-red-500 mt-1 hidden" data-error-for="participants"></p>
                        </div>
                    </div>
                </div>
                <div>
                    <label for="attachments" class="block text-sm font-medium text-gray-700 mb-1">Attachments</label>
                    <input type="file" id="attachments" name="attachments[]" multiple class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-primary/10 file:text-primary hover:file:bg-primary/20 cursor-pointer">
                    <p class="text-xs text-gray-500 mt-1">Max 10MB per file. Allowed: pdf, doc, xls, ppt, txt, images</p>
                    <div id="existing-attachments-list" class="mt-2 text-xs space-y-1"></div>
                    <p class="text-xs text-red-500 mt-1 hidden" data-error-for="attachments"></p>
                </div>
            </div>

            <div class="flex justify-end space-x-3 px-4 md:px-5 py-3 bg-gray-50 rounded-b-xl border-t border-gray-200 sticky bottom-0 z-[5]">
                <button type="button" id="cancel-modal-button-form" class="px-4 py-2 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">Cancel</button>
                <button type="submit" id="save-button" class="btn-primary px-4 py-2 text-sm">Save Changes</button>
            </div>
        </form>
    </div>
</div>

<div id="view-meeting-note-modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 hidden items-center justify-center z-[101] p-4 animate-fadeIn" style="--tw-bg-opacity: 0.75;">
    <div class="bg-white rounded-xl shadow-2xl max-w-2xl w-full mx-auto my-8 max-h-[90vh] flex flex-col transform transition-all duration-300 ease-out scale-95 opacity-0" id="view-meeting-note-modal-content">
        <div class="flex justify-between items-center p-5 md:p-6 border-b border-gray-200 sticky top-0 bg-white z-10 rounded-t-xl">
            <div id="view-modal-main-title-container" class="flex items-center min-w-0">
                <i class="ri-slideshow-3-line mr-2.5 text-primary text-2xl shrink-0" aria-hidden="true"></i>
                <h3 class="text-xl font-semibold text-gray-800 truncate" id="view-modal-main-title">Meeting Details</h3>
            </div>
            <button id="close-view-modal-button" class="text-gray-400 hover:text-red-600 p-2 rounded-full hover:bg-red-100 transition-colors">
                <span class="sr-only">Close</span><i class="ri-close-line text-xl" aria-hidden="true"></i>
            </button>
        </div>
        <div class="p-5 md:p-6 space-y-5 overflow-y-auto styled-scrollbar flex-grow">
            <div id="view-modal-body-content" class="space-y-4">
                <p class="text-center text-gray-500 py-8">Loading details...</p>
            </div>
        </div>
        <div class="flex justify-end space-x-3 px-5 md:px-6 py-4 bg-gray-50 rounded-b-xl border-t border-gray-200 sticky bottom-0 z-[5]">
            <button type="button" id="secondary-close-view-modal-button" class="px-5 py-2.5 border border-gray-300 rounded-lg text-sm font-medium text-gray-700 hover:bg-gray-100 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors">Close</button>
            <button type="button" id="edit-from-view-modal-button" class="hidden btn-primary px-5 py-2.5 text-sm">
                <i class="ri-pencil-line mr-1" aria-hidden="true"></i> Edit Note
            </button>
        </div>
    </div>
</div>

<div id="delete-meeting-note-confirm-modal" class="fixed inset-0 bg-gray-800 bg-opacity-75 hidden items-center justify-center z-[102] p-4 animate-fadeIn" style="--tw-bg-opacity: 0.75;">
    <div class="bg-white rounded-lg shadow-xl max-w-md w-full mx-auto transform transition-all duration-300 ease-out scale-95 opacity-0" id="delete-meeting-note-modal-inner-content">
        <div class="p-6">
            <div class="flex items-center justify-center w-12 h-12 mx-auto bg-red-100 rounded-full">
                <i class="ri-error-warning-line text-2xl text-red-600" aria-hidden="true"></i>
            </div>
            <div class="mt-4 text-center">
                <h3 class="text-lg font-medium text-gray-900">Delete Meeting Note</h3>
                <p class="mt-2 text-sm text-gray-500">Are you sure you want to delete the meeting note "<strong id="note-to-delete-topic" class="font-semibold"></strong>"? This action cannot be undone.</p>
            </div>
        </div>
        <form id="delete-meeting-note-form" method="POST" action="">
            @csrf
            @method('DELETE')
            <div class="flex justify-end space-x-3 px-6 py-4 bg-gray-50 rounded-b-lg">
                <button type="button" id="cancel-delete-button" class="px-4 py-2 border rounded-button text-sm text-gray-700 hover:bg-gray-100">Cancel</button>
                <button type="submit" class="px-4 py-2 bg-red-600 text-white rounded-button text-sm hover:bg-red-700">Delete</button>
            </div>
        </form>
    </div>
</div>

<style>
    .animate-fadeIn { animation: fadeInGlobal 0.3s ease-out forwards; }
    @keyframes fadeInGlobal { from { opacity: 0; } to { opacity: 1; } }
    #add-edit-meeting-note-modal-content.opacity-0,
    #view-meeting-note-modal-content.opacity-0,
    #delete-meeting-note-modal-inner-content.opacity-0 { 
        opacity: 0; 
        transform: scale(0.95) translateY(10px); 
    }
    #add-edit-meeting-note-modal-content.opacity-100,
    #view-meeting-note-modal-content.opacity-100,
    #delete-meeting-note-modal-inner-content.opacity-100 { 
        opacity: 1; 
        transform: scale(1) translateY(0px); 
    }
    .styled-scrollbar::-webkit-scrollbar { width: 6px; height: 6px; }
    .styled-scrollbar::-webkit-scrollbar-track { background: transparent; }
    .styled-scrollbar::-webkit-scrollbar-thumb { 
        background: #cbd5e1; 
        border-radius: 3px;
        transition: background-color 0.2s ease;
    }
    .styled-scrollbar::-webkit-scrollbar-thumb:hover { background: #94a3b8; }
    
    .meeting-note-row {
        position: relative;
        overflow: hidden;
    }
    
    .meeting-note-row::after {
        content: '';
        position: absolute;
        bottom: 0;
        left: 0;
        width: 100%;
        height: 1px;
        background: linear-gradient(to right, transparent, #e2e8f0, transparent);
    }
    
    .meeting-note-row:last-child::after {
        display: none;
    }
    
    .view-meeting-note-button {
        position: relative;
        transition: all 0.15s ease;
    }
    
    .view-meeting-note-button:hover {
        transform: scale(1.1);
    }
    
    .btn-primary {
        @apply bg-primary text-white rounded-lg hover:bg-primary-600 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-colors font-medium;
    }
    
    @media (max-width: 640px) {
        .meeting-note-row td:first-child {
            max-width: 200px;
        }
    }
</style>
@endsection

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const csrfToken = document.querySelector('meta[name="csrf-token"]').getAttribute('content');
    const addMeetingNoteButton = document.getElementById('add-meeting-note-button');
    const addMeetingNoteFromEmptyButton = document.getElementById('add-meeting-note-from-empty-button');
    const modal = document.getElementById('add-edit-meeting-note-modal');
    const modalContent = document.getElementById('add-edit-meeting-note-modal-content');
    const closeModalButton = document.getElementById('close-modal-button');
    const cancelModalButtonForm = document.getElementById('cancel-modal-button-form');
    const modalTitle = document.getElementById('modal-title');
    const meetingNoteForm = document.getElementById('meeting-note-form');
    const formMethodInput = document.getElementById('form-method-input');
    const meetingNoteIdInput = document.getElementById('meeting-note-id-input');
    const saveButton = document.getElementById('save-button');
    const existingAttachmentsList = document.getElementById('existing-attachments-list');
    const searchInput = document.getElementById('meeting-search-input');
    const tableBody = document.getElementById('meeting-notes-table-body');

    const viewModal = document.getElementById('view-meeting-note-modal');
    const viewModalContentElement = document.getElementById('view-meeting-note-modal-content');
    const closeViewModalButton = document.getElementById('close-view-modal-button');
    const secondaryCloseViewModalButton = document.getElementById('secondary-close-view-modal-button');
    const viewModalBodyContent = document.getElementById('view-modal-body-content');
    const viewModalMainTitle = document.getElementById('view-modal-main-title');
    const editFromViewModalButton = document.getElementById('edit-from-view-modal-button');
    let currentViewingNoteId = null;

    const deleteConfirmModal = document.getElementById('delete-meeting-note-confirm-modal');
    const deleteModalInnerContent = deleteConfirmModal ? deleteConfirmModal.querySelector('#delete-meeting-note-modal-inner-content') : null;
    const cancelDeleteButton = document.getElementById('cancel-delete-button');
    const noteToDeleteTopicEl = document.getElementById('note-to-delete-topic');
    const deleteMeetingNoteForm = document.getElementById('delete-meeting-note-form');

    const pageNotificationArea = document.getElementById('page-notification-area');

    function escapeHtml(unsafe) {
        if (unsafe === null || unsafe === undefined) return '';
        return String(unsafe).replace(/&/g, "&amp;").replace(/</g, "&lt;").replace(/>/g, "&gt;").replace(/"/g, "&quot;").replace(/'/g, "&#039;");
    }

    const Str = {
        limit: function (value, limit = 100, end = '...') {
            if (!value) return '';
            value = String(value);
            return value.length <= limit ? value : value.substring(0, limit) + end;
        }
    };

    function openGenericModal(modalElement, contentElement) {
        if (!modalElement || !contentElement) {
            console.error("Modal or content element not found for openGenericModal", modalElement, contentElement);
            return;
        }
        modalElement.classList.remove('hidden');
        modalElement.classList.add('flex');
        setTimeout(() => {
            contentElement.classList.remove('scale-95', 'opacity-0');
            contentElement.classList.add('scale-100', 'opacity-100');
        }, 10);
    }

    function closeGenericModal(modalElement, contentElement) {
        if (!modalElement || !contentElement) {
            console.error("Modal or content element not found for closeGenericModal", modalElement, contentElement);
            return;
        }
        contentElement.classList.remove('scale-100', 'opacity-100');
        contentElement.classList.add('scale-95', 'opacity-0');
        setTimeout(() => {
            modalElement.classList.add('hidden');
            modalElement.classList.remove('flex');
        }, 200);
    }

    function showPageNotification(message, type = 'success') {
        const alertClass = type === 'success' ? 'bg-green-50 border-green-400 text-green-700' : 'bg-red-50 border-red-400 text-red-700';
        const iconClass = type === 'success' ? 'ri-checkbox-circle-fill text-green-500' : 'ri-error-warning-fill text-red-500';
        const strongText = type === 'success' ? 'Success!' : 'Error!';

        if (!pageNotificationArea) {
            console.warn("pageNotificationArea not found. Cannot display notification:", message);
            return;
        }
        const notificationId = 'alert-' + Date.now();
        const notificationHTML = `
            <div id="${notificationId}" class="p-4 ${alertClass} border-l-4 rounded-r-lg relative shadow-md animate-fadeIn" role="alert" style="transition: opacity 0.5s ease, transform 0.5s ease, margin-bottom 0.5s ease; opacity: 1; transform: scale(1); margin-bottom: 1rem;">
                <div class="flex items-center">
                    <div class="py-1"><i class="${iconClass} mr-3 text-xl"></i></div>
                    <div>
                        <strong class="font-bold">${strongText}</strong>
                        <span class="block sm:inline text-sm">${escapeHtml(message)}</span>
                    </div>
                    <button type="button" class="absolute top-1/2 -translate-y-1/2 right-3 p-1.5 text-current hover:bg-current/10 rounded-md" onclick="this.closest('[role=alert]').remove()"><i class="ri-close-line text-lg"></i></button>
                </div>
            </div>`;
        pageNotificationArea.innerHTML = '';
        pageNotificationArea.insertAdjacentHTML('beforeend', notificationHTML);

        const newAlert = document.getElementById(notificationId);
        if (newAlert) {
            setTimeout(() => {
                if (newAlert && newAlert.parentElement) {
                    newAlert.style.opacity = '0';
                    newAlert.style.transform = 'scale(0.95) translateY(-10px)';
                    newAlert.style.marginBottom = '0';
                    setTimeout(() => {
                        if (newAlert.parentElement) newAlert.remove();
                    }, 500);
                }
            }, 7000);
        }
    }

    function clearFormErrors(formElement) {
        if (!formElement) return;
        formElement.querySelectorAll('[data-error-for]').forEach(el => {
            el.classList.add('hidden');
            el.textContent = '';
        });
        formElement.querySelectorAll('.border-red-500').forEach(el => {
            el.classList.remove('border-red-500');
            el.classList.add('border-gray-300');
        });
    }

    function displayFormErrors(formElement, errors) {
        if (!formElement || !errors) return;
        clearFormErrors(formElement);
        let firstErrorField = null;

        for (const field in errors) {
            let fieldNameToQuery = field;
            if (field.startsWith('attachments.')) {
                fieldNameToQuery = 'attachments';
            } else if (field.includes('.')) {
                fieldNameToQuery = field.split('.')[0] + '[]';
            }

            if (field === 'meeting_datetime') {
                const dateErrorEl = formElement.querySelector(`[data-error-for="meeting_datetime_date"]`);
                const timeErrorEl = formElement.querySelector(`[data-error-for="meeting_datetime_time"]`);
                const commonErrorEl = formElement.querySelector(`[data-error-for="meeting_datetime"]`);
                const errorMessage = errors[field][0];

                const dateInput = formElement.querySelector(`[name="meeting_datetime_date"]`);
                const timeInput = formElement.querySelector(`[name="meeting_datetime_time"]`);

                if (dateErrorEl) {
                    dateErrorEl.textContent = errorMessage;
                    dateErrorEl.classList.remove('hidden');
                }
                if (timeErrorEl) {
                    timeErrorEl.textContent = errorMessage;
                    timeErrorEl.classList.remove('hidden');
                }
                if (commonErrorEl && (!dateErrorEl || !timeErrorEl)) {
                    commonErrorEl.textContent = errorMessage;
                    commonErrorEl.classList.remove('hidden');
                }

                if (dateInput) {
                    dateInput.classList.remove('border-gray-300', 'border-slate-300');
                    dateInput.classList.add('border-red-500');
                    if (!firstErrorField) firstErrorField = dateInput;
                }
                if (timeInput) {
                    timeInput.classList.remove('border-gray-300', 'border-slate-300');
                    timeInput.classList.add('border-red-500');
                    if (!firstErrorField) firstErrorField = timeInput;
                }
                continue;
            }

            const inputField = formElement.querySelector(`[name="${fieldNameToQuery}"]`);
            if (inputField) {
                inputField.classList.remove('border-gray-300', 'border-slate-300');
                inputField.classList.add('border-red-500');
                if (!firstErrorField) firstErrorField = inputField;
            }

            let errorElement = formElement.querySelector(`[data-error-for="${field}"]`);
            if (!errorElement) {
                errorElement = formElement.querySelector(`[data-error-for="${field.split('.')[0]}"]`);
            }

            if (errorElement) {
                errorElement.textContent = errors[field][0];
                errorElement.classList.remove('hidden');
            } else {
                console.warn(`No error placeholder found for field: ${field}`);
            }
        }
        if (firstErrorField) firstErrorField.focus();
    }

    function openModalForCreate() {
        if (!modalTitle || !meetingNoteForm || !formMethodInput || !meetingNoteIdInput || !existingAttachmentsList || !modal || !modalContent) return;
        modalTitle.textContent = 'Add New Meeting Note';
        meetingNoteForm.reset();
        const participantsSelect = document.getElementById('participants');
        if (participantsSelect) {
            Array.from(participantsSelect.options).forEach(option => option.selected = false);
        }
        formMethodInput.value = 'POST';
        meetingNoteIdInput.value = '';
        meetingNoteForm.setAttribute('action', '{{ route("meeting-notes.store") }}');
        existingAttachmentsList.innerHTML = '';
        clearFormErrors(meetingNoteForm);
        openGenericModal(modal, modalContent);
    }

    async function openModalForEdit(noteId) {
        if (!modalTitle || !meetingNoteForm || !formMethodInput || !meetingNoteIdInput || !existingAttachmentsList || !modal || !modalContent) return;
        modalTitle.textContent = 'Edit Meeting Note';
        meetingNoteForm.reset();
        formMethodInput.value = 'PUT';
        meetingNoteIdInput.value = noteId;
        meetingNoteForm.setAttribute('action', `{{ url('meeting-notes') }}/${noteId}`);
        existingAttachmentsList.innerHTML = '<p class="text-xs text-gray-500 italic">Loading attachments...</p>';
        clearFormErrors(meetingNoteForm);

        try {
            const response = await fetch(`{{ url('meeting-notes') }}/${noteId}/edit`, {
                headers: { 
                    'Accept': 'application/json', 
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            });
            
            if (!response.ok) {
                const errData = await response.json().catch(() => ({ message: 'Server returned an error, but response was not valid JSON.' }));
                throw new Error(errData.message || 'Failed to fetch meeting details for edit. Status: ' + response.status);
            }
            
            const note = await response.json();
            console.log('Received note data:', note); // Debug log

            // Populate form fields
            document.getElementById('topic').value = note.topic || '';
            document.getElementById('notes_content').value = note.notes_content || '';
            document.getElementById('status').value = note.status || 'Upcoming';
            document.getElementById('location').value = note.location || '';

            // Handle datetime
            if (note.meeting_datetime) {
                try {
                    const dateTime = new Date(note.meeting_datetime);
                    if (!isNaN(dateTime.getTime())) {
                        // Format: YYYY-MM-DDTHH:mm
                        const formattedDateTime = dateTime.toISOString().slice(0, 16);
                        document.getElementById('meeting_datetime').value = formattedDateTime;
                        console.log('Setting datetime to:', formattedDateTime); // Debug log
                    }
                } catch (e) {
                    console.error('Error formatting datetime:', e);
                    document.getElementById('meeting_datetime').value = '';
                }
            }

            // Handle participants
            const participantsSelect = document.getElementById('participants');
            if (participantsSelect && note.participants) {
                const participantIds = note.participants.map(p => p.id);
                Array.from(participantsSelect.options).forEach(option => {
                    option.selected = participantIds.includes(parseInt(option.value));
                });
            }

            // Handle attachments
            let attachmentsHtml = '';
            if (note.attachments && note.attachments.length > 0) {
                attachmentsHtml += '<p class="font-medium text-slate-700 mb-1.5 text-xs">Current attachments:</p>';
                note.attachments.forEach(att => {
                    const fileUrl = att.file_url || `{{ Storage::url('') }}${(att.file_path || '').replace(/^public\//, '')}`;
                    attachmentsHtml += `
                        <div class="flex items-center justify-between text-slate-600 py-1.5 text-xs border-b border-slate-100 last:border-b-0">
                            <a href="${fileUrl}" target="_blank" class="hover:underline hover:text-primary truncate" title="${escapeHtml(att.file_name)}">${escapeHtml(Str.limit(att.file_name, 35))}</a>
                            <label class="flex items-center space-x-1.5 cursor-pointer ml-2 flex-shrink-0">
                                <input type="checkbox" name="remove_attachments[]" value="${att.id}" class="h-4 w-4 text-primary focus:ring-primary border-slate-300 rounded-sm">
                                <span class="text-xs text-red-600 hover:text-red-800">Remove</span>
                            </label>
                        </div>`;
                });
            } else {
                attachmentsHtml = '<p class="text-xs text-slate-500 italic">No attachments yet.</p>';
            }
            existingAttachmentsList.innerHTML = attachmentsHtml;

            openGenericModal(modal, modalContent);
            console.log('Form populated and ready for edit'); // Debug log

        } catch (error) {
            console.error('Error fetching meeting details for edit:', error);
            showPageNotification(error.message || 'Could not load meeting details for editing.', 'error');
        }
    }

    if (addMeetingNoteButton) addMeetingNoteButton.addEventListener('click', openModalForCreate);
    if (addMeetingNoteFromEmptyButton) addMeetingNoteFromEmptyButton.addEventListener('click', openModalForCreate);
    if (closeModalButton) closeModalButton.addEventListener('click', () => closeGenericModal(modal, modalContent));
    if (cancelModalButtonForm) cancelModalButtonForm.addEventListener('click', () => closeGenericModal(modal, modalContent));
    if (modal) modal.addEventListener('click', (event) => { if (event.target === modal) closeGenericModal(modal, modalContent); });

    if (meetingNoteForm) {
        meetingNoteForm.addEventListener('submit', async function (event) {
            event.preventDefault();
            if (saveButton) {
                saveButton.disabled = true;
                saveButton.innerHTML = `<i class="ri-loader-4-line animate-spin mr-2"></i> Saving...`;
            }
            clearFormErrors(this);

            const formData = new FormData(this);
            const url = this.getAttribute('action');
            const method = formMethodInput.value;

            // Debug log the form data before submission
            console.log('Form data before submission:');
            for (let [key, value] of formData.entries()) {
                console.log(`${key}:`, value);
            }

            // Ensure all required fields are present
            const requiredFields = {
                'topic': formData.get('topic')?.trim(),
                'meeting_datetime': formData.get('meeting_datetime')?.trim(),
                'notes_content': formData.get('notes_content')?.trim(),
                'status': formData.get('status')?.trim()
            };

            // Check for missing required fields
            const missingFields = Object.entries(requiredFields)
                .filter(([_, value]) => !value)
                .map(([field]) => field);

            if (missingFields.length > 0) {
                console.error('Missing required fields:', missingFields);
                showPageNotification(`Missing required fields: ${missingFields.join(', ')}`, 'error');
                if (saveButton) {
                    saveButton.disabled = false;
                    saveButton.innerHTML = 'Save Note';
                }
                return;
            }

            // Format datetime
            try {
                const dt = new Date(requiredFields.meeting_datetime);
                if (isNaN(dt.getTime())) {
                    throw new Error('Invalid date');
                }
                formData.set('meeting_datetime', dt.toISOString().slice(0, 16));
            } catch (e) {
                console.error('Date parsing error:', e);
                showPageNotification('Invalid date and time format.', 'error');
                if (saveButton) {
                    saveButton.disabled = false;
                    saveButton.innerHTML = 'Save Note';
                }
                return;
            }

            try {
                const response = await fetch(url, {
                    method: 'POST', // Always use POST, let the _method field handle the actual method
                    headers: {
                        'X-CSRF-TOKEN': csrfToken,
                        'Accept': 'application/json',
                        'X-HTTP-Method-Override': method
                    },
                    body: formData
                });

                const result = await response.json();
                console.log('Server response:', result);

                if (!response.ok) {
                    if (response.status === 422 && result.errors) {
                        console.error('Validation errors:', result.errors);
                        displayFormErrors(this, result.errors);
                    } else {
                        throw new Error(result.message || `HTTP error! status: ${response.status}`);
                    }
                } else {
                    showPageNotification(result.message || 'Operation successful!', 'success');
                    closeGenericModal(modal, modalContent);
                    setTimeout(() => window.location.reload(), 1200);
                }
            } catch (error) {
                console.error('Error submitting form:', error);
                showPageNotification(error.message || 'An unexpected error occurred.', 'error');
            } finally {
                if (saveButton) {
                    saveButton.disabled = false;
                    saveButton.innerHTML = 'Save Note';
                }
            }
        });
    }

    function createMeetingNoteStatusBadge(statusText) {
        let baseClasses = 'px-2.5 py-1 inline-flex text-xs leading-5 font-semibold rounded-full ring-1';
        const statusClassesMap = {
            'Upcoming': 'bg-blue-100 text-blue-700 ring-blue-600/20',
            'In Progress': 'bg-yellow-100 text-yellow-700 ring-yellow-600/20',
            'Completed': 'bg-green-100 text-green-700 ring-green-600/20',
            'Cancelled': 'bg-red-100 text-red-700 ring-red-600/20',
        };
        const specificClass = statusClassesMap[statusText] || 'bg-slate-100 text-slate-700 ring-slate-600/20';
        return `<span class="${baseClasses} ${specificClass}">${escapeHtml(statusText) || 'N/A'}</span>`;
    }

    if (tableBody) {
        tableBody.addEventListener('click', function (event) {
            const button = event.target.closest('button');
            if (!button) {
                const viewTrigger = event.target.closest('.view-meeting-note-button');
                if (viewTrigger) {
                    const noteId = viewTrigger.dataset.noteId;
                    if (noteId) openViewModal(noteId);
                }
                return;
            }

            const noteId = button.dataset.noteId;

            if (button.classList.contains('edit-meeting-note-button')) {
                if (noteId) openModalForEdit(noteId);
            } else if (button.classList.contains('delete-meeting-note-button')) {
                const noteTopic = button.dataset.noteTopic;
                if (noteId && noteTopic) openDeleteConfirmModal(noteId, noteTopic);
            } else if (button.classList.contains('view-meeting-note-button')) {
                if (noteId) openViewModal(noteId);
            }
        });
    }

    async function openViewModal(noteId) {
        console.log('Opening view modal for noteId:', noteId);
        currentViewingNoteId = noteId;
        if (!viewModalBodyContent || !viewModal || !viewModalContentElement) {
            console.error('Required modal elements not found');
            return;
        }

        viewModalBodyContent.innerHTML = `
            <div class="flex items-center justify-center py-10">
                <div class="animate-spin rounded-full h-12 w-12 border-4 border-primary border-t-transparent"></div>
            </div>`;
        openGenericModal(viewModal, viewModalContentElement);

        try {
            const response = await fetch(`{{ url('meeting-notes') }}/${noteId}`, {
                headers: { 
                    'Accept': 'application/json', 
                    'X-Requested-With': 'XMLHttpRequest',
                    'X-CSRF-TOKEN': csrfToken
                }
            });

            if (!response.ok) {
                throw new Error('Failed to fetch meeting details. Status: ' + response.status);
            }

            const note = await response.json();
            console.log('Received note data for view:', note);

            if (viewModalMainTitle) {
                viewModalMainTitle.textContent = note.topic ? escapeHtml(Str.limit(note.topic, 45)) : 'Meeting Details';
            }

            let participantsHtml = '<p class="text-sm text-slate-500 italic">No participants listed.</p>';
            if (note.participants && note.participants.length > 0) {
                participantsHtml = '<div class="flex flex-wrap gap-2.5">';
                note.participants.forEach(p => {
                    participantsHtml += `
                        <span class="flex items-center space-x-2 bg-slate-100 px-3 py-1.5 rounded-full text-xs shadow-sm border border-slate-200/80">
                            <img src="${p.profile_photo_url || 'https://ui-avatars.com/api/?name='+encodeURIComponent(p.name)+'&color=FFFFFF&background=4F46E5&size=24&font-size=0.45&bold=true&rounded=true'}" alt="${escapeHtml(p.name)}" class="w-5 h-5 rounded-full object-cover ring-1 ring-white">
                            <span class="text-slate-700 font-medium">${escapeHtml(p.name)}</span>
                        </span>`;
                });
                participantsHtml += '</div>';
            }

            let attachmentsHtml = '<p class="text-sm text-slate-500 italic">No attachments.</p>';
            if (note.attachments && note.attachments.length > 0) {
                attachmentsHtml = '<ul class="space-y-2.5">';
                note.attachments.forEach(att => {
                    let icon = 'ri-file-unknow-line';
                    let iconColor = 'text-slate-500';
                    if (att.file_type) {
                        if (att.file_type.includes('pdf')) {
                            icon = 'ri-file-pdf-2-line';
                            iconColor = 'text-red-500';
                        } else if (att.file_type.includes('word')) {
                            icon = 'ri-file-word-2-line';
                            iconColor = 'text-blue-500';
                        } else if (att.file_type.includes('excel')) {
                            icon = 'ri-file-excel-2-line';
                            iconColor = 'text-green-500';
                        } else if (att.file_type.includes('image')) {
                            icon = 'ri-image-2-line';
                            iconColor = 'text-purple-500';
                        } else if (att.file_type.includes('zip') || att.file_type.includes('rar')) {
                            icon = 'ri-folder-zip-line';
                            iconColor = 'text-yellow-600';
                        }
                    }

                    attachmentsHtml += `
                        <li class="flex items-center justify-between text-sm p-3 bg-slate-50 hover:bg-slate-100 rounded-lg border border-slate-200/80 transition-colors group">
                            <div class="flex items-center space-x-3 min-w-0">
                                <i class="${icon} ${iconColor} text-2xl shrink-0"></i>
                                <span class="text-slate-700 truncate group-hover:text-primary" title="${escapeHtml(att.file_name)}">${escapeHtml(Str.limit(att.file_name, 35))}</span>
                            </div>
                            <a href="${att.file_url || '{{ Storage::url("/") }}' + (att.file_path || '').replace(/^public\//, '')}" target="_blank" class="text-primary-600 hover:text-primary-700 ml-3 shrink-0 p-1.5 hover:bg-primary-100 rounded-md transition-colors" title="Download/View">
                                <i class="ri-download-cloud-2-line text-lg"></i>
                            </a>
                        </li>`;
                });
                attachmentsHtml += '</ul>';
            }

            let locationHtml = '<span class="italic text-slate-500">Not specified</span>';
            let locationIcon = 'ri-map-pin-line';
            let locationColor = 'text-slate-400';

            if (note.location) {
                locationHtml = `<a href="${escapeHtml(note.location)}" target="_blank" class="text-primary-600 hover:text-primary-700 hover:underline">${escapeHtml(note.location)}</a>`;
                locationIcon = 'ri-link';
                locationColor = 'text-primary-500';
            }

            const notesContentFormatted = note.notes_html || (note.notes_content ? escapeHtml(note.notes_content).replace(/\n/g, '<br>') : '<p class="italic text-slate-500">No notes content.</p>');
            const createdDateFormatted = note.created_at ? new Date(note.created_at).toLocaleString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: false }) : 'N/A';
            const updatedDateFormatted = (note.updated_at && new Date(note.updated_at).getTime() !== new Date(note.created_at).getTime())
                ? new Date(note.updated_at).toLocaleString('id-ID', { day: 'numeric', month: 'short', year: 'numeric', hour: '2-digit', minute: '2-digit', hour12: false })
                : null;

            const statusBadgeHtml = createMeetingNoteStatusBadge(note.status);

            viewModalBodyContent.innerHTML = `
            <div class="space-y-6 md:space-y-8">
                <div class="pb-5 border-b border-slate-200">
                    <h2 class="text-2xl md:text-3xl font-bold text-slate-800 mb-2">${escapeHtml(note.topic)}</h2>
                    <div class="text-sm text-slate-500 flex items-center flex-wrap gap-x-4 gap-y-1">
                        <span class="flex items-center"><i class="ri-calendar-event-line mr-1.5 text-base text-slate-400" aria-hidden="true"></i> <span class="font-medium">${note.meeting_datetime ? new Date(note.meeting_datetime).toLocaleDateString('id-ID', { day: 'numeric', month: 'long', year: 'numeric' }) : 'N/A'}</span></span>
                        <span class="flex items-center"><i class="ri-time-line mr-1.5 text-base text-slate-400" aria-hidden="true"></i> <span class="font-medium">${note.meeting_datetime ? new Date(note.meeting_datetime).toLocaleTimeString('id-ID', { hour: '2-digit', minute: '2-digit' }) : 'N/A'}</span></span>
                    </div>
                </div>
                <dl class="grid grid-cols-1 md:grid-cols-2 gap-x-6 gap-y-5 pt-2">
                    <div> <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider flex items-center mb-1"><i class="ri-flag-line mr-2 text-sm text-slate-400" aria-hidden="true"></i>Status</dt> <dd class="text-sm text-slate-700 font-semibold">${statusBadgeHtml}</dd> </div>
                    <div> <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider flex items-center mb-1"><i class="ri-user-settings-line mr-2 text-sm text-slate-400" aria-hidden="true"></i>Created By</dt> <dd class="text-sm text-slate-700">${note.creator ? escapeHtml(note.creator.name) : 'N/A'}</dd> </div>
                    <div class="md:col-span-2"> <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider flex items-center mb-1"><i class="${locationIcon} mr-2 text-sm ${locationColor}" aria-hidden="true"></i>Location / Link</dt> <dd class="text-sm text-slate-700">${locationHtml}</dd> </div>
                    <div> <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider flex items-center mb-1"><i class="ri-add-box-line mr-2 text-sm text-slate-400" aria-hidden="true"></i>Created On</dt> <dd class="text-sm text-slate-700">${createdDateFormatted}</dd> </div>
                    ${updatedDateFormatted ? `<div> <dt class="text-xs font-medium text-slate-500 uppercase tracking-wider flex items-center mb-1"><i class="ri-edit-2-line mr-2 text-sm text-slate-400" aria-hidden="true"></i>Last Updated</dt> <dd class="text-sm text-slate-700">${updatedDateFormatted}</dd> </div>` : ''}
                </dl>
                <div class="pt-4 border-t border-slate-200"> <h4 class="text-base font-semibold text-slate-700 mb-3 flex items-center"><i class="ri-group-line mr-2 text-lg text-slate-500" aria-hidden="true"></i>Participants <span class="text-slate-500 font-normal ml-1.5 text-sm">(${note.participants ? note.participants.length : 0})</span></h4> ${participantsHtml}</div>
                <div class="pt-4 border-t border-slate-200"> <h4 class="text-base font-semibold text-slate-700 mb-3 flex items-center"><i class="ri-sticky-note-line mr-2 text-lg text-slate-500" aria-hidden="true"></i>Meeting Notes</h4> <div class="prose prose-sm max-w-none text-slate-700 leading-relaxed bg-slate-50 p-4 rounded-lg border border-slate-200 min-h-[150px] styled-scrollbar overflow-y-auto max-h-[40vh]">${notesContentFormatted}</div> </div>
                <div class="pt-4 border-t border-slate-200"> <h4 class="text-base font-semibold text-slate-700 mb-3 flex items-center"><i class="ri-attachment-2 mr-2 text-lg text-slate-500" aria-hidden="true"></i>Attachments <span class="text-slate-500 font-normal ml-1.5 text-sm">(${note.attachments ? note.attachments.length : 0})</span></h4> ${attachmentsHtml}</div>
            </div>`;

            const loggedInUserId = {{ Auth::id() ?? 'null' }};
            const isAdmin = {{ $isAdmin ? 'true' : 'false' }};
            if (editFromViewModalButton) {
                if (isAdmin || (note.created_by_user_id === loggedInUserId)) {
                    editFromViewModalButton.classList.remove('hidden');
                    editFromViewModalButton.onclick = () => {
                        closeGenericModal(viewModal, viewModalContentElement);
                        openModalForEdit(note.id);
                    };
                } else {
                    editFromViewModalButton.classList.add('hidden');
                }
            }
        } catch (error) {
            console.error('Error fetching meeting details for view:', error);
            viewModalBodyContent.innerHTML = `<p class="text-center text-red-500 py-8">Error: ${escapeHtml(error.message)}</p>`;
            if (editFromViewModalButton) editFromViewModalButton.classList.add('hidden');
        }
    }

    if (closeViewModalButton) closeViewModalButton.addEventListener('click', () => closeGenericModal(viewModal, viewModalContentElement));
    if (secondaryCloseViewModalButton) secondaryCloseViewModalButton.addEventListener('click', () => closeGenericModal(viewModal, viewModalContentElement));
    if (viewModal) viewModal.addEventListener('click', (event) => { if (event.target === viewModal) closeGenericModal(viewModal, viewModalContentElement); });

    function openDeleteConfirmModal(noteId, noteTopic) {
        if (!noteToDeleteTopicEl || !deleteMeetingNoteForm || !deleteConfirmModal || !deleteModalInnerContent) return;
        noteToDeleteTopicEl.textContent = noteTopic;
        deleteMeetingNoteForm.setAttribute('action', `{{ url('meeting-notes') }}/${noteId}`);
        openGenericModal(deleteConfirmModal, deleteModalInnerContent);
    }

    if (cancelDeleteButton) cancelDeleteButton.addEventListener('click', () => closeGenericModal(deleteConfirmModal, deleteModalInnerContent));
    if (deleteConfirmModal) deleteConfirmModal.addEventListener('click', (event) => {
        if (event.target === deleteConfirmModal) closeGenericModal(deleteConfirmModal, deleteModalInnerContent);
    });

    if (deleteMeetingNoteForm) {
        deleteMeetingNoteForm.addEventListener('submit', async function (event) {
            event.preventDefault();
            const deleteButtonSubmit = this.querySelector('button[type="submit"]');
            if (deleteButtonSubmit) {
                deleteButtonSubmit.disabled = true;
                deleteButtonSubmit.innerHTML = `<i class="ri-loader-4-line animate-spin mr-2"></i> Deleting...`;
            }

            try {
                const response = await fetch(this.action, {
                    method: 'DELETE',
                    headers: { 'X-CSRF-TOKEN': csrfToken, 'Accept': 'application/json' }
                });
                const result = await response.json();
                if (!response.ok) throw new Error(result.message || 'Failed to delete meeting note.');

                showPageNotification(result.message || "Meeting note deleted successfully!", 'success');
                closeGenericModal(deleteConfirmModal, deleteModalInnerContent);

                const rowToRemove = document.querySelector(`tr.meeting-note-row[data-note-id="${this.action.split('/').pop()}"]`);
                if (rowToRemove) {
                    rowToRemove.style.transition = 'opacity 0.3s ease, transform 0.3s ease';
                    rowToRemove.style.opacity = '0';
                    rowToRemove.style.transform = 'scale(0.95)';
                    setTimeout(() => {
                        if (rowToRemove.parentElement) rowToRemove.remove();
                        if (tableBody && tableBody.querySelectorAll('tr.meeting-note-row').length === 0) {
                            window.location.reload();
                        }
                    }, 300);
                } else {
                    window.location.reload();
                }
            } catch (error) {
                console.error('Error deleting meeting note:', error);
                showPageNotification(error.message || "Error deleting note.", 'error');
            } finally {
                if (deleteButtonSubmit) {
                    deleteButtonSubmit.disabled = false;
                    deleteButtonSubmit.innerHTML = 'Delete';
                }
            }
        });
    }

    if (searchInput && tableBody) {
        searchInput.addEventListener('input', function () {
            const searchTerm = this.value.toLowerCase().trim();
            const rows = tableBody.querySelectorAll('tr.meeting-note-row');
            let visibleRows = 0;
            rows.forEach(row => {
                const topicEl = row.querySelector('td:first-child .font-semibold');
                const notesContentEl = row.querySelector('td:first-child .text-xs');
                const creatorEl = row.querySelector('td:nth-child(3)');

                const topic = topicEl ? topicEl.textContent.toLowerCase() : '';
                const notesContent = notesContentEl ? notesContentEl.textContent.toLowerCase() : '';
                const creator = creatorEl ? creatorEl.textContent.toLowerCase() : '';

                if (topic.includes(searchTerm) || notesContent.includes(searchTerm) || creator.includes(searchTerm)) {
                    row.style.display = '';
                    visibleRows++;
                } else {
                    row.style.display = 'none';
                }
            });

            const noResultsRow = tableBody.querySelector('.no-search-results');
            const originalEmptyRowTr = tableBody.querySelector('td[colspan="6"]')?.closest('tr');

            if (visibleRows === 0 && searchTerm !== '') {
                if (originalEmptyRowTr) originalEmptyRowTr.style.display = 'none';
                if (!noResultsRow) {
                    const tr = document.createElement('tr');
                    tr.classList.add('no-search-results');
                    tr.innerHTML = `<td colspan="6" class="px-6 py-10 text-center text-sm text-gray-500">No meeting notes match your search for "${escapeHtml(searchTerm)}".</td>`;
                    tableBody.appendChild(tr);
                } else {
                    noResultsRow.querySelector('td').innerHTML = `No meeting notes match your search for "${escapeHtml(searchTerm)}".`;
                    noResultsRow.style.display = '';
                }
            } else {
                if (noResultsRow) noResultsRow.remove();
                if (originalEmptyRowTr && searchTerm === '' && {{ $meetingNotes->isEmpty() ? 'true' : 'false' }}) {
                    originalEmptyRowTr.style.display = '';
                } else if (originalEmptyRowTr && searchTerm === '') {
                    originalEmptyRowTr.style.display = 'none';
                }
            }
        });
    }

    ['alert-success', 'alert-error'].forEach(alertId => {
        const alertElement = document.getElementById(alertId);
        if (alertElement && !alertElement.classList.contains('static-alert')) {
            setTimeout(() => {
                if (alertElement && alertElement.parentElement) {
                    alertElement.style.transition = 'opacity 0.5s ease, transform 0.5s ease, margin-bottom 0.5s ease';
                    alertElement.style.opacity = '0';
                    alertElement.style.transform = 'scale(0.95) translateY(-10px)';
                    alertElement.style.marginBottom = '0';
                    setTimeout(() => {
                        if (alertElement.parentElement) alertElement.remove();
                    }, 500);
                }
            }, 7000);
        }
    });
});
</script>
@endpush
