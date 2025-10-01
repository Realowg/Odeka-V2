@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Odeva AI Admin</h1>
        <p class="mt-2 text-gray-600">Manage AI assistant settings, costs, and creator permissions</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- Stats Cards -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-6 mb-8">
        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Status</p>
                    <p class="text-2xl font-bold {{ $settings->odeva_enabled ? 'text-green-600' : 'text-red-600' }}">
                        {{ $settings->odeva_enabled ? 'Active' : 'Disabled' }}
                    </p>
                </div>
                <div class="p-3 bg-{{ $settings->odeva_enabled ? 'green' : 'red' }}-100 rounded-full">
                    <svg class="w-6 h-6 text-{{ $settings->odeva_enabled ? 'green' : 'red' }}-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">This Month</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($currentSpending, 2) }}</p>
                    <p class="text-xs text-gray-500">of ${{ number_format($settings->odeva_monthly_budget, 2) }}</p>
                </div>
                <div class="p-3 bg-blue-100 rounded-full">
                    <svg class="w-6 h-6 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 8c-1.657 0-3 .895-3 2s1.343 2 3 2 3 .895 3 2-1.343 2-3 2m0-8c1.11 0 2.08.402 2.599 1M12 8V7m0 1v8m0 0v1m0-1c-1.11 0-2.08-.402-2.599-1M21 12a9 9 0 11-18 0 9 9 0 0118 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Active Subscriptions</p>
                    <p class="text-2xl font-bold text-gray-900">{{ $activeSubscriptions }}</p>
                </div>
                <div class="p-3 bg-purple-100 rounded-full">
                    <svg class="w-6 h-6 text-purple-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"></path>
                    </svg>
                </div>
            </div>
        </div>

        <div class="bg-white rounded-lg shadow p-6">
            <div class="flex items-center justify-between">
                <div>
                    <p class="text-sm text-gray-600">Today's Usage</p>
                    <p class="text-2xl font-bold text-gray-900">${{ number_format($todayUsage, 2) }}</p>
                </div>
                <div class="p-3 bg-yellow-100 rounded-full">
                    <svg class="w-6 h-6 text-yellow-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path>
                    </svg>
                </div>
            </div>
        </div>
    </div>

    <!-- Settings Form -->
    <form action="{{ route('admin.odeva.update-settings') }}" method="POST" class="space-y-8">
        @csrf
        @method('PUT')

        <!-- Core Admin Controls -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Core Admin Controls</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="odeva_enabled" value="1" {{ $settings->odeva_enabled ? 'checked' : '' }} class="h-4 w-4 text-blue-600 rounded">
                        <span class="ml-2 text-sm text-gray-700">Enable Odeva Globally</span>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">AI Provider</label>
                    <select name="odeva_provider" class="w-full border-gray-300 rounded-md shadow-sm">
                        <option value="anthropic" {{ $settings->odeva_provider === 'anthropic' ? 'selected' : '' }}>Anthropic (Claude)</option>
                        <option value="openai" {{ $settings->odeva_provider === 'openai' ? 'selected' : '' }}>OpenAI</option>
                    </select>
                </div>

                <div class="md:col-span-2">
                    <label class="block text-sm font-medium text-gray-700 mb-1">API Key</label>
                    <input type="password" name="odeva_api_key" placeholder="Enter API key (encrypted)" class="w-full border-gray-300 rounded-md shadow-sm">
                    <p class="mt-1 text-xs text-gray-500">Current key is encrypted. Leave blank to keep existing.</p>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Model</label>
                    <input type="text" name="odeva_model" value="{{ $settings->odeva_model }}" class="w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Max Tokens</label>
                    <input type="number" name="odeva_max_tokens" value="{{ $settings->odeva_max_tokens }}" min="100" max="100000" class="w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Temperature</label>
                    <input type="number" name="odeva_temperature" value="{{ $settings->odeva_temperature }}" min="0" max="2" step="0.1" class="w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <div>
                    <button type="button" onclick="testApiConnection()" class="px-4 py-2 bg-blue-600 text-white rounded-md hover:bg-blue-700">
                        Test API Connection
                    </button>
                </div>
            </div>
        </div>

        <!-- Budget & Cost Management -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Budget & Cost Management</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Monthly Budget ($)</label>
                    <input type="number" name="odeva_monthly_budget" value="{{ $settings->odeva_monthly_budget }}" min="0" step="0.01" class="w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="odeva_auto_disable_on_budget" value="1" {{ $settings->odeva_auto_disable_on_budget ? 'checked' : '' }} class="h-4 w-4 text-blue-600 rounded">
                        <span class="ml-2 text-sm text-gray-700">Auto-disable when budget exceeded</span>
                    </label>
                </div>

                <div class="md:col-span-2">
                    <a href="{{ route('admin.odeva.cost-analytics') }}" class="text-blue-600 hover:underline">View Cost Analytics →</a>
                    <span class="mx-2">|</span>
                    <a href="{{ route('admin.odeva.export-cost-report') }}" class="text-blue-600 hover:underline">Export Cost Report →</a>
                </div>
            </div>
        </div>

        <!-- Creator Management -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Creator Management</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="odeva_require_approval" value="1" {{ $settings->odeva_require_approval ? 'checked' : '' }} class="h-4 w-4 text-blue-600 rounded">
                        <span class="ml-2 text-sm text-gray-700">Require admin approval for new subscriptions</span>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Message Limit per Creator (monthly)</label>
                    <input type="number" name="odeva_creator_message_limit" value="{{ $settings->odeva_creator_message_limit }}" min="0" class="w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <div class="md:col-span-2">
                    <a href="{{ route('admin.odeva.creators') }}" class="text-blue-600 hover:underline">Manage Creators & Permissions →</a>
                </div>
            </div>
        </div>

        <!-- Feature Toggles -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Feature Toggles</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="odeva_automation_enabled" value="1" {{ $settings->odeva_automation_enabled ? 'checked' : '' }} class="h-4 w-4 text-blue-600 rounded">
                        <span class="ml-2 text-sm text-gray-700">Enable Automation</span>
                    </label>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="odeva_analytics_enabled" value="1" {{ $settings->odeva_analytics_enabled ? 'checked' : '' }} class="h-4 w-4 text-blue-600 rounded">
                        <span class="ml-2 text-sm text-gray-700">Enable Analytics</span>
                    </label>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="odeva_learning_enabled" value="1" {{ $settings->odeva_learning_enabled ? 'checked' : '' }} class="h-4 w-4 text-blue-600 rounded">
                        <span class="ml-2 text-sm text-gray-700">Enable Learning from Examples</span>
                    </label>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="odeva_subscriptions_enabled" value="1" {{ $settings->odeva_subscriptions_enabled ? 'checked' : '' }} class="h-4 w-4 text-blue-600 rounded">
                        <span class="ml-2 text-sm text-gray-700">Enable Subscriptions</span>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Free Trial Duration (days)</label>
                    <input type="number" name="odeva_trial_days" value="{{ $settings->odeva_trial_days }}" min="0" max="365" class="w-full border-gray-300 rounded-md shadow-sm">
                </div>
            </div>
        </div>

        <!-- Subscription Plans -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Subscription Plans</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Subscription Price</label>
                    <input type="number" name="odeva_subscription_price" value="{{ $settings->odeva_subscription_price }}" min="0" step="0.01" class="w-full border-gray-300 rounded-md shadow-sm">
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Currency</label>
                    <select name="odeva_subscription_currency" class="w-full border-gray-300 rounded-md shadow-sm">
                        <option value="USD" {{ $settings->odeva_subscription_currency === 'USD' ? 'selected' : '' }}>USD</option>
                        <option value="EUR" {{ $settings->odeva_subscription_currency === 'EUR' ? 'selected' : '' }}>EUR</option>
                        <option value="GBP" {{ $settings->odeva_subscription_currency === 'GBP' ? 'selected' : '' }}>GBP</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- Safety & Moderation -->
        <div class="bg-white shadow rounded-lg p-6">
            <h2 class="text-xl font-bold text-gray-900 mb-4">Safety & Moderation</h2>
            
            <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="odeva_content_moderation" value="1" {{ $settings->odeva_content_moderation ? 'checked' : '' }} class="h-4 w-4 text-blue-600 rounded">
                        <span class="ml-2 text-sm text-gray-700">Enable Content Moderation</span>
                    </label>
                </div>

                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="odeva_activity_logging" value="1" {{ $settings->odeva_activity_logging ? 'checked' : '' }} class="h-4 w-4 text-blue-600 rounded">
                        <span class="ml-2 text-sm text-gray-700">Enable Activity Logging</span>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Rate Limit (requests/minute)</label>
                    <input type="number" name="odeva_rate_limit" value="{{ $settings->odeva_rate_limit }}" min="1" max="1000" class="w-full border-gray-300 rounded-md shadow-sm">
                </div>
            </div>
        </div>

        <!-- Emergency Controls -->
        <div class="bg-white shadow rounded-lg p-6 border-2 border-red-200">
            <h2 class="text-xl font-bold text-red-600 mb-4">Emergency Controls</h2>
            
            <div class="space-y-4">
                <div>
                    <label class="flex items-center">
                        <input type="checkbox" name="odeva_emergency_stop" value="1" {{ $settings->odeva_emergency_stop ? 'checked' : '' }} class="h-4 w-4 text-red-600 rounded">
                        <span class="ml-2 text-sm font-bold text-red-700">🚨 Emergency Stop (Disable All Odeva Functions)</span>
                    </label>
                </div>

                <div>
                    <label class="block text-sm font-medium text-gray-700 mb-1">Emergency Message</label>
                    <textarea name="odeva_emergency_message" rows="3" class="w-full border-gray-300 rounded-md shadow-sm" placeholder="Message to display when emergency stop is active">{{ $settings->odeva_emergency_message }}</textarea>
                </div>
            </div>
        </div>

        <!-- Submit Button -->
        <div class="flex justify-end">
            <button type="submit" class="px-6 py-3 bg-blue-600 text-white font-semibold rounded-md hover:bg-blue-700 transition">
                Save All Settings
            </button>
        </div>
    </form>
</div>

<script>
function testApiConnection() {
    fetch('{{ route('admin.odeva.test-api') }}', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
            'X-CSRF-TOKEN': '{{ csrf_token() }}'
        }
    })
    .then(response => response.json())
    .then(data => {
        alert(data.message);
    })
    .catch(error => {
        alert('Error testing connection: ' + error);
    });
}
</script>
@endsection

