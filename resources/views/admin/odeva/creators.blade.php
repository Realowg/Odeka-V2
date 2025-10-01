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
.status-badge {
    padding: 0.25rem 0.75rem;
    border-radius: 9999px;
    font-size: 0.75rem;
    font-weight: 500;
}
.status-active {
    background: #dcfce7;
    color: #166534;
}
.status-trial {
    background: #dbeafe;
    color: #1e40af;
}
.status-paused {
    background: #fef3c7;
    color: #92400e;
}
.status-cancelled {
    background: #fee2e2;
    color: #991b1b;
}
.action-btn {
    padding: 0.25rem 0.75rem;
    border-radius: 6px;
    font-size: 0.75rem;
    font-weight: 500;
    border: none;
    cursor: pointer;
    transition: all 0.2s;
}
.action-btn-approve {
    background: #dcfce7;
    color: #166534;
}
.action-btn-restrict {
    background: #fef3c7;
    color: #92400e;
}
.action-btn-whitelist {
    background: #dbeafe;
    color: #1e40af;
}
.action-btn-blacklist {
    background: #fee2e2;
    color: #991b1b;
}
</style>

<div class="odeva-container">
    <div class="odeva-header">
        <div class="d-flex justify-content-between align-items-center">
            <div>
                <h3 class="mb-1">Odeva Creator Management</h3>
                <p class="text-muted mb-0">Manage creator permissions and subscriptions</p>
            </div>
            <a href="{{ route('admin.odeva.index') }}" class="btn btn-sm btn-outline-secondary">
                <i class="bi bi-arrow-left me-1"></i> Back to Settings
            </a>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mb-4" role="alert">
            <i class="bi bi-check2 me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Creators Table -->
    <table class="odeva-table">
        <thead>
            <tr>
                <th>Creator</th>
                <th>Status</th>
                <th>Trial Ends</th>
                <th>Price</th>
                <th>Automation</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @forelse($subscriptions as $subscription)
            <tr>
                <td>
                    <div class="d-flex align-items-center">
                        <img src="{{ $subscription->creator->avatar ?? asset('img/default-avatar.png') }}" 
                             alt="{{ $subscription->creator->name }}"
                             class="rounded-circle me-2" 
                             style="width: 32px; height: 32px; object-fit: cover;">
                        <div>
                            <div class="fw-medium">{{ $subscription->creator->name }}</div>
                            <div class="text-muted small">@{{ $subscription->creator->username }}</div>
                        </div>
                    </div>
                </td>
                <td>
                    <span class="status-badge status-{{ $subscription->status }}">
                        {{ ucfirst($subscription->status) }}
                    </span>
                </td>
                <td>{{ $subscription->trial_ends_at ? $subscription->trial_ends_at->format('M d, Y') : 'N/A' }}</td>
                <td>{{ $subscription->currency }} {{ number_format($subscription->price, 2) }}</td>
                <td>
                    @if($subscription->automation_enabled)
                        <span class="text-success"><i class="bi bi-check-circle-fill me-1"></i>Enabled</span>
                    @else
                        <span class="text-muted"><i class="bi bi-x-circle me-1"></i>Disabled</span>
                    @endif
                </td>
                <td>
                    <div class="d-flex gap-1">
                        @if($subscription->status !== 'active')
                        <form action="{{ route('admin.odeva.update-creator-permission', $subscription->creator_id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action" value="approve">
                            <button type="submit" class="action-btn action-btn-approve">Approve</button>
                        </form>
                        @endif

                        @if($subscription->status === 'active')
                        <form action="{{ route('admin.odeva.update-creator-permission', $subscription->creator_id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action" value="restrict">
                            <button type="submit" class="action-btn action-btn-restrict">Restrict</button>
                        </form>
                        @endif

                        @if(!in_array($subscription->creator_id, $settings->odeva_whitelisted_creators ?? []))
                        <form action="{{ route('admin.odeva.update-creator-permission', $subscription->creator_id) }}" method="POST" class="d-inline">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action" value="whitelist">
                            <button type="submit" class="action-btn action-btn-whitelist">Whitelist</button>
                        </form>
                        @else
                            <span class="text-primary small">✓ Whitelisted</span>
                        @endif

                        <form action="{{ route('admin.odeva.update-creator-permission', $subscription->creator_id) }}" method="POST" class="d-inline" 
                              onsubmit="return confirm('Are you sure you want to blacklist this creator?')">
                            @csrf
                            @method('PUT')
                            <input type="hidden" name="action" value="blacklist">
                            <button type="submit" class="action-btn action-btn-blacklist">Blacklist</button>
                        </form>
                    </div>
                </td>
            </tr>
            @empty
            <tr>
                <td colspan="6" class="text-center text-muted py-4">
                    No Odeva subscriptions found
                </td>
            </tr>
            @endforelse
        </tbody>
    </table>

    <!-- Pagination -->
    @if($subscriptions->hasPages())
    <div class="mt-4">
        {{ $subscriptions->links() }}
    </div>
    @endif
</div>
@endsection
