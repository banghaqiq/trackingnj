@extends('layouts.app')

@section('title', __('messages.dashboard'))

@section('content')
<div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-4 gap-6">
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ __('messages.total_paket') }}</h3>
        <p class="text-3xl font-bold text-blue-600">-</p>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ __('messages.paket_masuk') }}</h3>
        <p class="text-3xl font-bold text-green-600">-</p>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ __('messages.paket_keluar') }}</h3>
        <p class="text-3xl font-bold text-orange-600">-</p>
    </div>
    
    <div class="bg-white dark:bg-gray-800 rounded-lg shadow p-6">
        <h3 class="text-lg font-semibold text-gray-900 dark:text-white mb-2">{{ __('messages.paket_pending') }}</h3>
        <p class="text-3xl font-bold text-red-600">-</p>
    </div>
</div>

<div class="mt-8 bg-white dark:bg-gray-800 rounded-lg shadow p-6">
    <h2 class="text-xl font-semibold text-gray-900 dark:text-white mb-4">{{ __('messages.welcome') }}</h2>
    <p class="text-gray-600 dark:text-gray-400">
        {{ __('messages.this_is_a_package_management_system') }}
    </p>
</div>
@endsection
