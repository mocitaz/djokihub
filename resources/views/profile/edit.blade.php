<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Profile') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8 space-y-6">
            {{-- Profile Information Update Form --}}
            <div class="p-4 sm:p-8 bg-white shadow-lg sm:rounded-xl hover:shadow-2xl transition-shadow duration-300 ease-in-out">
                <div class="max-w-xl mx-auto">
                    @include('profile.partials.update-profile-information-form')
                </div>
            </div>

            {{-- Password Update Form --}}
            <div class="p-4 sm:p-8 bg-white shadow-lg sm:rounded-xl hover:shadow-2xl transition-shadow duration-300 ease-in-out">
                <div class="max-w-xl mx-auto">
                    @include('profile.partials.update-password-form')
                </div>
            </div>

            {{-- Delete User Form --}}
            <div class="p-4 sm:p-8 bg-white shadow-lg sm:rounded-xl hover:shadow-2xl transition-shadow duration-300 ease-in-out">
                <div class="max-w-xl mx-auto">
                    @include('profile.partials.delete-user-form')
                </div>
            </div>
        </div>
    </div>
</x-app-layout>
