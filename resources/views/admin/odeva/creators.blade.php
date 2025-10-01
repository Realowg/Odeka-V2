@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Odeva Creator Management</h1>
        <p class="mt-2 text-gray-600">Manage creator permissions and subscriptions</p>
    </div>

    @if(session('success'))
        <div class="mb-6 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded">
            {{ session('success') }}
        </div>
    @endif

    <!-- Creators Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Creator</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Status</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Trial Ends</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Price</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Automation</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Actions</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($subscriptions as $subscription)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <div class="flex items-center">
                            <img class="h-10 w-10 rounded-full" src="{{ $subscription->creator->avatar ?? asset('img/default-avatar.png') }}" alt="">
                            <div class="ml-4">
                                <div class="text-sm font-medium text-gray-900">{{ $subscription->creator->name }}</div>
                                <div class="text-sm text-gray-500">@{{ $subscription->creator->username }}</div>
                            </div>
                        </div>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        <span class="px-2 py-1 inline-flex text-xs leading-5 font-semibold rounded-full 
                            @if($subscription->status === 'active') bg-green-100 text-green-800
                            @elseif($subscription->status === 'trial') bg-blue-100 text-blue-800
                            @elseif($subscription->status === 'paused') bg-yellow-100 text-yellow-800
                            @else bg-red-100 text-red-800
                            @endif">
                            {{ ucfirst($subscription->status) }}
                        </span>
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $subscription->trial_ends_at ? $subscription->trial_ends_at->format('M d, Y') : 'N/A' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $subscription->currency }} {{ number_format($subscription->price, 2) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap">
                        @if($subscription->automation_enabled)
                            <span class="text-green-600">✓ Enabled</span>
                        @else
                            <span class="text-gray-400">✗ Disabled</span>
                        @endif
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                        <div class="flex space-x-2">
                            @if($subscription->status !== 'active')
                            <form action="{{ route('admin.odeva.update-creator-permission', $subscription->creator_id) }}" method="POST" class="inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="approve">
                                <button type="submit" class="text-green-600 hover:text-green-900">Approve</button>
                            </form>
                            @endif

                            @if($subscription->status === 'active')
                            <form action="{{ route('admin.odeva.update-creator-permission', $subscription->creator_id) }}" method="POST" class="inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="restrict">
                                <button type="submit" class="text-yellow-600 hover:text-yellow-900">Restrict</button>
                            </form>
                            @endif

                            @if(!in_array($subscription->creator_id, $settings->odeva_whitelisted_creators ?? []))
                            <form action="{{ route('admin.odeva.update-creator-permission', $subscription->creator_id) }}" method="POST" class="inline">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="whitelist">
                                <button type="submit" class="text-blue-600 hover:text-blue-900">Whitelist</button>
                            </form>
                            @else
                                <span class="text-blue-600">✓ Whitelisted</span>
                            @endif

                            <form action="{{ route('admin.odeva.update-creator-permission', $subscription->creator_id) }}" method="POST" class="inline" 
                                  onsubmit="return confirm('Are you sure you want to blacklist this creator?')">
                                @csrf
                                @method('PUT')
                                <input type="hidden" name="action" value="blacklist">
                                <button type="submit" class="text-red-600 hover:text-red-900">Blacklist</button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        No Odeva subscriptions found
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    <!-- Pagination -->
    <div class="mt-6">
        {{ $subscriptions->links() }}
    </div>

    <div class="mt-6">
        <a href="{{ route('admin.odeva.index') }}" class="text-blue-600 hover:underline">← Back to Odeva Settings</a>
    </div>
</div>
@endsection

