@extends('layouts.app')

@section('title', 'Edit Profile - ' . $staffMember->name)

@section('content')
<div class="bg-white min-h-screen">
    {{-- Enhanced Header Section --}}
    <div class="bg-white shadow-lg border-b border-gray-200">
        <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex flex-col md:flex-row justify-between md:items-center py-8">
                <div class="flex items-center space-x-4">
                    <div class="w-12 h-12 bg-gradient-to-br from-primary to-primary-600 rounded-xl flex items-center justify-center shadow-lg">
                        <i class="ri-user-settings-line text-white text-xl"></i>
                    </div>
                <div>
                        <h1 class="text-2xl lg:text-3xl font-bold text-gray-900">Edit Profile</h1>
                        <p class="text-gray-600 text-sm mt-1">Update profile information for <strong class="font-semibold text-primary">{{ $staffMember->name }}</strong></p>
                    </div>
                </div>
                <div class="mt-4 md:mt-0">
                    <a href="{{ (Auth::user()->role === 'admin' && Auth::id() !== $staffMember->id) ? route('staff.index') : route('dashboard') }}" 
                       class="inline-flex items-center px-6 py-3 border-2 border-gray-300 text-sm font-medium rounded-xl text-gray-700 bg-white hover:bg-gray-50 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-200 shadow-sm hover:shadow-md">
                        <i class="ri-arrow-left-line mr-2"></i>
                        Back to {{ (Auth::user()->role === 'admin' && Auth::id() !== $staffMember->id) ? 'Staff List' : 'Dashboard' }}
                    </a>
                </div>
                </div>
            </div>
        </div>

    {{-- Main Content Area --}}
    <div class="max-w-4xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
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

        @if ($errors->any())
            <div class="mb-8 bg-gradient-to-r from-red-50 to-rose-50 border-l-4 border-red-400 rounded-xl shadow-lg p-6">
                <div class="flex items-center mb-3">
                    <i class="ri-error-warning-line text-red-500 text-xl mr-3"></i>
                    <p class="font-bold text-red-800">Please fix the following errors:</p>
                </div>
                <ul class="list-disc pl-6 text-sm text-red-700 space-y-1">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                    </div>
                @endif

        {{-- Enhanced Form Card --}}
        <div class="bg-white rounded-2xl shadow-xl overflow-hidden border border-gray-200">
            <form action="{{ route('staff.update', ['user' => $staffMember->id]) }}" method="POST" enctype="multipart/form-data" id="staff-edit-form">
                @csrf
                @method('PUT')

                {{-- Profile Photo Section --}}
                <div class="bg-gradient-to-r from-gray-50 to-gray-100 p-8 border-b border-gray-200">
                    <div class="flex flex-col sm:flex-row items-center space-y-6 sm:space-y-0 sm:space-x-8">
                        <div class="relative group">
                        <img id="profile_photo_preview" 
                             src="{{ $staffMember->profile_photo_url ?? asset('images/default-avatar.png') }}" 
                                 alt="Profile photo" 
                                 class="w-32 h-32 rounded-full object-cover ring-4 ring-white shadow-xl transition-all duration-300 ease-in-out group-hover:scale-105"
                             onerror="this.onerror=null; this.src='https://ui-avatars.com/api/?name={{ urlencode($staffMember->name) }}&color=FFFFFF&background=4F46E5&size=128&font-size=0.33&bold=true';">
                            <div class="absolute inset-0 bg-black bg-opacity-0 group-hover:bg-opacity-20 rounded-full transition-all duration-300 flex items-center justify-center">
                                <i class="ri-camera-line text-white text-2xl opacity-0 group-hover:opacity-100 transition-opacity duration-300"></i>
                            </div>
                        </div>
                        <div class="flex-1 text-center sm:text-left">
                            <h3 class="text-lg font-semibold text-gray-800 mb-2">Profile Picture</h3>
                            <p class="text-sm text-gray-600 mb-4">Update your profile photo to help colleagues recognize you</p>
                            <label for="profile_photo" class="cursor-pointer inline-flex items-center px-6 py-3 bg-white border-2 border-gray-300 rounded-xl shadow-sm text-sm font-medium text-gray-700 hover:bg-gray-50 hover:border-gray-400 focus-within:ring-2 focus-within:ring-offset-2 focus-within:ring-primary transition-all duration-200">
                                <i class="ri-upload-2-line mr-2"></i>
                                <span>Choose New Photo</span>
                                <input type="file" name="profile_photo" id="profile_photo" class="sr-only" accept="image/png, image/jpeg, image/jpg">
                            </label>
                            <p class="text-xs text-gray-500 mt-2">Recommended: Square JPG, PNG, or GIF, max 2MB</p>
                            @error('profile_photo')
                                <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>
                </div>

                {{-- Form Fields --}}
                <div class="p-8">
                    <div class="space-y-8">
                    {{-- Personal Information Section --}}
                        <div>
                            <div class="flex items-center mb-6">
                                <div class="w-8 h-8 bg-primary/10 rounded-lg flex items-center justify-center mr-3">
                                    <i class="ri-user-line text-primary"></i>
                                </div>
                                <h2 class="text-xl font-semibold text-gray-800">Personal Information</h2>
                    </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label for="name" class="block text-sm font-semibold text-gray-700">Full Name <span class="text-red-500">*</span></label>
                        <input type="text" name="name" id="name" value="{{ old('name', $staffMember->name) }}" required 
                                           class="w-full px-4 py-3 border-2 {{ $errors->has('name') ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' }} rounded-xl text-sm focus:ring-2 focus:ring-primary-300 focus:border-primary-500 transition-all duration-200 hover:border-gray-300">
                        @error('name')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                                <div class="space-y-2">
                                    <label for="email" class="block text-sm font-semibold text-gray-700">Email Address <span class="text-red-500">*</span></label>
                        <input type="email" name="email" id="email" value="{{ old('email', $staffMember->email) }}" required 
                                           class="w-full px-4 py-3 border-2 {{ $errors->has('email') ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' }} rounded-xl text-sm focus:ring-2 focus:ring-primary-300 focus:border-primary-500 transition-all duration-200 hover:border-gray-300">
                        @error('email')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                                </div>
                            </div>
                    </div>

                        {{-- Security Section --}}
                        <div class="border-t border-gray-200 pt-8">
                            <div class="flex items-center mb-6">
                                <div class="w-8 h-8 bg-red-50 rounded-lg flex items-center justify-center mr-3">
                                    <i class="ri-lock-line text-red-600"></i>
                                </div>
                                <h2 class="text-xl font-semibold text-gray-800">Security Settings</h2>
                            </div>
                            <div class="bg-yellow-50 border border-yellow-200 rounded-xl p-4 mb-6">
                                <div class="flex items-center">
                                    <i class="ri-information-line text-yellow-600 mr-2"></i>
                                    <p class="text-sm text-yellow-800">Leave password fields empty if you don't want to change your password</p>
                                </div>
                    </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label for="password" class="block text-sm font-semibold text-gray-700">New Password</label>
                        <input type="password" name="password" id="password" 
                                           class="w-full px-4 py-3 border-2 {{ $errors->has('password') ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' }} rounded-xl text-sm focus:ring-2 focus:ring-primary-300 focus:border-primary-500 transition-all duration-200 hover:border-gray-300">
                        @error('password')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                                <div class="space-y-2">
                                    <label for="password_confirmation" class="block text-sm font-semibold text-gray-700">Confirm New Password</label>
                        <input type="password" name="password_confirmation" id="password_confirmation" 
                                           class="w-full px-4 py-3 border-2 border-gray-200 rounded-xl text-sm focus:ring-2 focus:ring-primary-300 focus:border-primary-500 transition-all duration-200 hover:border-gray-300">
                                </div>
                    </div>
                    </div>
                    
                        {{-- Work Information Section --}}
                        <div class="border-t border-gray-200 pt-8">
                            <div class="flex items-center mb-6">
                                <div class="w-8 h-8 bg-blue-50 rounded-lg flex items-center justify-center mr-3">
                                    <i class="ri-briefcase-line text-blue-600"></i>
                                </div>
                                <h2 class="text-xl font-semibold text-gray-800">Work Information</h2>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label for="availability_status" class="block text-sm font-semibold text-gray-700">Availability Status <span class="text-red-500">*</span></label>
                        <select id="availability_status" name="availability_status" required 
                                            class="w-full px-4 py-3 border-2 {{ $errors->has('availability_status') ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' }} rounded-xl text-sm focus:ring-2 focus:ring-primary-300 focus:border-primary-500 transition-all duration-200 hover:border-gray-300">
                            <option value="Available" {{ old('availability_status', $staffMember->availability_status) == 'Available' ? 'selected' : '' }}>Available</option>
                            <option value="On Leave" {{ old('availability_status', $staffMember->availability_status) == 'On Leave' ? 'selected' : '' }}>On Leave</option>
                            <option value="Busy on Project" {{ old('availability_status', $staffMember->availability_status) == 'Busy on Project' ? 'selected' : '' }}>Busy on Project</option>
                        </select>
                        @error('availability_status')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                                <div class="space-y-2">
                                    <label for="phone_number" class="block text-sm font-semibold text-gray-700">Phone Number</label>
                        <input type="tel" name="phone_number" id="phone_number" value="{{ old('phone_number', $staffMember->phone_number) }}" 
                                           class="w-full px-4 py-3 border-2 {{ $errors->has('phone_number') ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' }} rounded-xl text-sm focus:ring-2 focus:ring-primary-300 focus:border-primary-500 transition-all duration-200 hover:border-gray-300">
                        @error('phone_number')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                                <div class="md:col-span-2 space-y-2">
                                    <label for="location" class="block text-sm font-semibold text-gray-700">Location</label>
                        <input type="text" name="location" id="location" value="{{ old('location', $staffMember->location) }}" 
                                           class="w-full px-4 py-3 border-2 {{ $errors->has('location') ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' }} rounded-xl text-sm focus:ring-2 focus:ring-primary-300 focus:border-primary-500 transition-all duration-200 hover:border-gray-300"
                                           placeholder="e.g., Jakarta, Indonesia">
                        @error('location')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                                </div>
                            </div>
                    </div>
                    
                    {{-- Social Links Section --}}
                        <div class="border-t border-gray-200 pt-8">
                            <div class="flex items-center mb-6">
                                <div class="w-8 h-8 bg-green-50 rounded-lg flex items-center justify-center mr-3">
                                    <i class="ri-links-line text-green-600"></i>
                    </div>
                                <h2 class="text-xl font-semibold text-gray-800">Social Links</h2>
                            </div>
                            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                                <div class="space-y-2">
                                    <label for="linkedin_url" class="block text-sm font-semibold text-gray-700">LinkedIn Profile</label>
                                    <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="ri-linkedin-box-fill text-blue-600"></i>
                            </div>
                            <input type="url" name="linkedin_url" id="linkedin_url" value="{{ old('linkedin_url', $staffMember->linkedin_url) }}" 
                                               class="w-full pl-10 pr-4 py-3 border-2 {{ $errors->has('linkedin_url') ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' }} rounded-xl text-sm focus:ring-2 focus:ring-primary-300 focus:border-primary-500 transition-all duration-200 hover:border-gray-300" 
                                               placeholder="https://linkedin.com/in/username">
                        </div>
                        @error('linkedin_url')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                    </div>

                                <div class="space-y-2">
                                    <label for="github_url" class="block text-sm font-semibold text-gray-700">GitHub Profile</label>
                                    <div class="relative">
                            <div class="absolute inset-y-0 left-0 pl-3 flex items-center pointer-events-none">
                                            <i class="ri-github-fill text-gray-800"></i>
                            </div>
                            <input type="url" name="github_url" id="github_url" value="{{ old('github_url', $staffMember->github_url) }}" 
                                               class="w-full pl-10 pr-4 py-3 border-2 {{ $errors->has('github_url') ? 'border-red-500 ring-1 ring-red-500' : 'border-gray-200' }} rounded-xl text-sm focus:ring-2 focus:ring-primary-300 focus:border-primary-500 transition-all duration-200 hover:border-gray-300" 
                                               placeholder="https://github.com/username">
                        </div>
                        @error('github_url')
                            <p class="mt-1 text-xs text-red-600">{{ $message }}</p>
                        @enderror
                                </div>
                            </div>
                    </div>
                </div>
            </div>

                {{-- Enhanced Footer --}}
                <div class="bg-gray-50 px-8 py-6 border-t border-gray-200 flex items-center justify-between rounded-b-2xl">
                    <div class="flex items-center text-sm text-gray-500">
                        <i class="ri-information-line mr-2"></i>
                        All fields marked with <span class="text-red-500 font-medium">*</span> are required
                    </div>
                    <div class="flex items-center space-x-4">
                <a href="{{ (Auth::user()->role === 'admin' && Auth::id() !== $staffMember->id) ? route('staff.index') : url()->previous(route('dashboard')) }}" 
                           class="px-6 py-3 border-2 border-gray-300 rounded-xl text-sm font-medium text-gray-700 hover:bg-gray-100 hover:border-gray-400 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-primary transition-all duration-200 bg-white">
                    Cancel
                </a>
                <button type="submit" id="save-profile-button"
                                class="btn-primary px-6 py-3 text-sm font-medium shadow-lg hover:shadow-xl transition-all duration-300 hover:scale-105 transform flex items-center space-x-2">
                    <span class="button-text">Save Changes</span>
                            <i class="ri-save-line"></i>
                    <i class="ri-loader-4-line animate-spin ml-2 hidden button-loader"></i>
                </button>
                    </div>
            </div>
        </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
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

    /* Form Enhancements */
    .form-section {
        padding: 2rem;
        border-bottom: 1px solid #e5e7eb;
    }
    .form-section:last-child {
        border-bottom: none;
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

    /* Enhanced Focus States */
    .focus-enhanced:focus {
        outline: none;
        ring: 2px;
        ring-color: #6366f1;
        ring-opacity: 0.5;
        border-color: #6366f1;
    }

    /* Card Hover Effects */
    .card-hover {
        transition: all 0.3s cubic-bezier(0.4, 0, 0.2, 1);
    }
    .card-hover:hover {
        transform: translateY(-2px);
        box-shadow: 0 10px 25px -3px rgba(0, 0, 0, 0.1), 0 4px 6px -2px rgba(0, 0, 0, 0.05);
    }
</style>
@endpush

@push('scripts')
<script>
document.addEventListener('DOMContentLoaded', function () {
    const profilePhotoInput = document.getElementById('profile_photo');
    const profilePhotoPreview = document.getElementById('profile_photo_preview');
    const defaultPhotoUrl = '{{ $staffMember->profile_photo_url ?? asset('images/default-avatar.png') }}';
    const avatarApiFallback = 'https://ui-avatars.com/api/?name={{ urlencode($staffMember->name) }}&color=FFFFFF&background=4F46E5&size=128&font-size=0.33&bold=true';

    // Enhanced Photo Preview
    if (profilePhotoInput && profilePhotoPreview) {
        profilePhotoInput.addEventListener('change', function (event) {
            const file = event.target.files[0];
            if (file) {
                // Validate file type
                if (!file.type.startsWith('image/')) {
                    alert('Please select a valid image file.');
                    return;
                }
                
                // Validate file size (2MB)
                if (file.size > 2 * 1024 * 1024) {
                    alert('File size must be less than 2MB.');
                    return;
                }

                const reader = new FileReader();
                reader.onload = function (e) {
                    profilePhotoPreview.style.transition = 'opacity 0.3s ease';
                    profilePhotoPreview.style.opacity = '0.5';
                    
                    setTimeout(() => {
                    profilePhotoPreview.src = e.target.result;
                        profilePhotoPreview.style.opacity = '1';
                    }, 150);
                }
                reader.readAsDataURL(file);
            } else {
                profilePhotoPreview.src = defaultPhotoUrl || avatarApiFallback;
            }
        });
    }

    // Enhanced Form Submission
    const saveButton = document.getElementById('save-profile-button');
    const staffEditForm = document.getElementById('staff-edit-form');
    
    if (saveButton && staffEditForm) {
        const buttonText = saveButton.querySelector('.button-text');
        const buttonLoader = saveButton.querySelector('.button-loader');
        const saveIcon = saveButton.querySelector('.ri-save-line');
        
        staffEditForm.addEventListener('submit', function() {
            // Show loading state
            if (buttonText && buttonLoader && saveIcon) {
                buttonText.textContent = 'Saving...';
                buttonLoader.classList.remove('hidden');
                saveIcon.classList.add('hidden');
            }
            saveButton.disabled = true;
            saveButton.classList.add('loading');
        });
    }

    // Enhanced Field Validation
    const requiredFields = staffEditForm.querySelectorAll('input[required], select[required]');
    requiredFields.forEach(field => {
        field.addEventListener('blur', function() {
            validateField(this);
        });
        
        field.addEventListener('input', function() {
            clearFieldError(this);
        });
    });

    function validateField(field) {
        const value = field.value.trim();
        
        if (field.hasAttribute('required') && !value) {
            showFieldError(field, 'This field is required.');
            return false;
        }
        
        if (field.type === 'email' && value && !isValidEmail(value)) {
            showFieldError(field, 'Please enter a valid email address.');
            return false;
        }
        
        clearFieldError(field);
        return true;
    }

    function showFieldError(field, message) {
        field.classList.add('border-red-500', 'ring-1', 'ring-red-500');
        field.classList.remove('border-gray-200');
        
        let errorEl = field.parentNode.querySelector('.field-error');
        if (!errorEl) {
            errorEl = document.createElement('p');
            errorEl.className = 'field-error mt-1 text-xs text-red-600';
            field.parentNode.appendChild(errorEl);
        }
        errorEl.textContent = message;
    }

    function clearFieldError(field) {
        field.classList.remove('border-red-500', 'ring-1', 'ring-red-500');
        field.classList.add('border-gray-200');
        
        const errorEl = field.parentNode.querySelector('.field-error');
        if (errorEl) {
            errorEl.remove();
        }
    }

    function isValidEmail(email) {
        return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
    }

    // Enhanced Error Handling
    if (profilePhotoPreview) {
        profilePhotoPreview.onerror = function() {
            this.onerror = null;
            this.src = avatarApiFallback;
        };
        
        if (!profilePhotoPreview.getAttribute('src') || profilePhotoPreview.getAttribute('src') === '{{ asset('images/default-avatar.png') }}') {
             fetch('{{ asset('images/default-avatar.png') }}')
                .then(res => {
                    if (!res.ok) profilePhotoPreview.src = avatarApiFallback;
                }).catch(() => profilePhotoPreview.src = avatarApiFallback);
        }
    }
    
    // Auto-hide success alerts
    setTimeout(() => {
        const alert = document.getElementById('alert-success');
        if (alert) {
            alert.style.transition = 'all 0.5s ease';
            alert.style.opacity = '0';
            alert.style.transform = 'translateY(-20px)';
            setTimeout(() => {
                alert.style.display = 'none';
            }, 500);
        }
    }, 5000);

    console.log('Staff Edit Page Enhanced JavaScript Initialized');
});
</script>
@endpush
