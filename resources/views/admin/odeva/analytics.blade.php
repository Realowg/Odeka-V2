@extends('admin.layout')

@section('content')
<style>
.odeva-container {
    max-width: 1400px;
    margin: 0 auto;
}
.odeva-header {
    padding: 2rem 0;
    border-bottom: 1px solid #e5e7eb;
    margin-bottom: 2rem;
}
.odeva-tabs {
    display: flex;
    gap: 1rem;
    margin-bottom: 2rem;
}
.odeva-tab {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    text-decoration: none;
    transition: all 0.2s;
}
.odeva-tab.active {
    background: #6366f1;
    color: white;
}
.odeva-tab:not(.active) {
    background: #f3f4f6;
    color: #6b7280;
}
.odeva-tab:not(.active):hover {
    background: #e5e7eb;
}
.odeva-table {
    width: 100%;
    background: white;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    overflow: hidden;
}
.odeva-table th {
    background: #f9fafb;
    padding: 0.75rem 1rem;
    font-size: 0.75rem;
    font-weight: 600;
    color: #6b7280;
    text-transform: uppercase;
    letter-spacing: 0.05em;
    text-align: left;
}
.odeva-table td {
    padding: 1rem;
    border-top: 1px solid #e5e7eb;
    font-size: 0.875rem;
}
</style>

<div class="odeva-container">
    <div class="odeva-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1">Odeva Cost Analytics</h3>
                <p class="text-muted mb-0">Track AI usage and spending across creators</p>
            </div>
            <a href="{{ route('admin.odeva.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Settings
            </a>
        </div>
    </div>

    <!-- Period Tabs -->
    <div class="odeva-tabs">
        <a href="{{ route('admin.odeva.cost-analytics', ['period' => 'day']) }}" class="odeva-tab {{ $period === 'day' ? 'active' : '' }}">Today</a>
        <a href="{{ route('admin.odeva.cost-analytics', ['period' => 'week']) }}" class="odeva-tab {{ $period === 'week' ? 'active' : '' }}">This Week</a>
        <a href="{{ route('admin.odeva.cost-analytics', ['period' => 'month']) }}" class="odeva-tab {{ $period === 'month' ? 'active' : '' }}">This Month</a>
        <a href="{{ route('admin.odeva.cost-analytics', ['period' => 'year']) }}" class="odeva-tab {{ $period === 'year' ? 'active' : '' }}">This Year</a>
    </div>

    <!-- Analytics Table -->
    <table class="odeva-table">
        <thead>
            <tr>
                <th>Date</th>
                <th>Creator</th>
                <th>Requests</th>
                <th>Tokens</th>
                <th>Cost</th>
                <th>Breakdown</th>
            </tr>
        </thead>
        <tbody>
            @forelse($analytics as $record)
            <tr>
                <td>{{ $record->date->format('M d, Y') }}</td>
                <td>{{ $record->creator->name ?? 'All Creators' }}</td>
                <td>{{ number_format($record->total_requests) }}</td>
                <td>{{ number_format($record->total_tokens) }}</td>
                <td class="fw-bold">${{ number_format($record->total_cost, 6) }}</td>
                <td>
                    @if($record->breakdown)
                        <div class="text-muted small">
                            @foreach($record->breakdown as $type => $amount)
                                <div>{{ ucfirst($type) }}: ${{ number_format($amount, 6) }}</div>
                            @endforeach
                        </div>
                    @endif
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-4">
                    No analytics data available for this period
                </td>
            </tr>
            @endforelse
        </tbody>
        @if($analytics->count() > 0)
        <tfoot style="background: #f9fafb; border-top: 2px solid #e5e7eb;">
            <tr>
                <td colspan="2" class="fw-bold">Total</td>
                <td class="fw-bold">{{ number_format($analytics->sum('total_requests')) }}</td>
                <td class="fw-bold">{{ number_format($analytics->sum('total_tokens')) }}</td>
                <td class="fw-bold">${{ number_format($analytics->sum('total_cost'), 2) }}</td>
                <td></td>
            </tr>
        </tfoot>
        @endif
    </table>
</div>
@endsection
