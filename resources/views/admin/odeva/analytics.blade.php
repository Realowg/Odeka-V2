@extends('layouts.app')

@section('content')
<div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8 py-8">
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-900">Odeva Cost Analytics</h1>
        <p class="mt-2 text-gray-600">Track AI usage and spending across creators</p>
    </div>

    <!-- Period Selector -->
    <div class="mb-6 bg-white shadow rounded-lg p-4">
        <div class="flex space-x-4">
            <a href="{{ route('admin.odeva.cost-analytics', ['period' => 'day']) }}" 
               class="px-4 py-2 rounded {{ $period === 'day' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                Today
            </a>
            <a href="{{ route('admin.odeva.cost-analytics', ['period' => 'week']) }}" 
               class="px-4 py-2 rounded {{ $period === 'week' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                This Week
            </a>
            <a href="{{ route('admin.odeva.cost-analytics', ['period' => 'month']) }}" 
               class="px-4 py-2 rounded {{ $period === 'month' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                This Month
            </a>
            <a href="{{ route('admin.odeva.cost-analytics', ['period' => 'year']) }}" 
               class="px-4 py-2 rounded {{ $period === 'year' ? 'bg-blue-600 text-white' : 'bg-gray-200 text-gray-700' }}">
                This Year
            </a>
        </div>
    </div>

    <!-- Analytics Table -->
    <div class="bg-white shadow rounded-lg overflow-hidden">
        <table class="min-w-full divide-y divide-gray-200">
            <thead class="bg-gray-50">
                <tr>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Date</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Creator</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Requests</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Tokens</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Cost</th>
                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase">Breakdown</th>
                </tr>
            </thead>
            <tbody class="bg-white divide-y divide-gray-200">
                @forelse($analytics as $record)
                <tr>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $record->date->format('M d, Y') }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ $record->creator->name ?? 'All Creators' }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ number_format($record->total_requests) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                        {{ number_format($record->total_tokens) }}
                    </td>
                    <td class="px-6 py-4 whitespace-nowrap text-sm font-semibold text-gray-900">
                        ${{ number_format($record->total_cost, 6) }}
                    </td>
                    <td class="px-6 py-4 text-sm text-gray-500">
                        @if($record->breakdown)
                            <div class="text-xs">
                                @foreach($record->breakdown as $type => $amount)
                                    <div>{{ ucfirst($type) }}: ${{ number_format($amount, 6) }}</div>
                                @endforeach
                            </div>
                        @endif
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6" class="px-6 py-4 text-center text-gray-500">
                        No analytics data available for this period
                    </td>
                </tr>
                @endforelse
            </tbody>
            <tfoot class="bg-gray-50">
                <tr>
                    <td colspan="2" class="px-6 py-3 text-sm font-bold text-gray-900">Total</td>
                    <td class="px-6 py-3 text-sm font-bold text-gray-900">{{ number_format($analytics->sum('total_requests')) }}</td>
                    <td class="px-6 py-3 text-sm font-bold text-gray-900">{{ number_format($analytics->sum('total_tokens')) }}</td>
                    <td class="px-6 py-3 text-sm font-bold text-gray-900">${{ number_format($analytics->sum('total_cost'), 2) }}</td>
                    <td></td>
                </tr>
            </tfoot>
        </table>
    </div>

    <div class="mt-6">
        <a href="{{ route('admin.odeva.index') }}" class="text-blue-600 hover:underline">← Back to Odeva Settings</a>
    </div>
</div>
@endsection

