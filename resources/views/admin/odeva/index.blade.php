@extends('admin.layout')

@section('content')
<h5 class="mb-4 fw-light">
    <a class="text-reset" href="{{ url('panel/admin') }}">{{ __('admin.dashboard') }}</a>
    <i class="bi-chevron-right me-1 fs-6"></i>
    <span class="text-muted">Odeva AI Admin</span>
</h5>

<div class="content">
    <div class="row">
        <div class="col-lg-12">

            @if(session('success'))
                <div class="alert alert-success alert-dismissible fade show" role="alert">
                    <i class="bi bi-check2 me-1"></i> {{ session('success') }}
                    <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                </div>
            @endif

            <!-- Stats Cards -->
            <div class="row mb-4">
                <div class="col-xl-3 col-sm-6 mb-3">
                    <div class="card shadow-custom border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="bi {{ $settings->odeva_enabled ? 'bi-lightning-charge-fill text-success' : 'bi-lightning-slash-fill text-danger' }} fs-1"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 text-muted">Status</h6>
                                    <h4 class="mb-0 {{ $settings->odeva_enabled ? 'text-success' : 'text-danger' }}">
                                        {{ $settings->odeva_enabled ? 'Active' : 'Disabled' }}
                                    </h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6 mb-3">
                    <div class="card shadow-custom border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-cash-stack text-primary fs-1"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 text-muted">This Month</h6>
                                    <h4 class="mb-0">${{ number_format($currentSpending, 2) }}</h4>
                                    <small class="text-muted">of ${{ number_format($settings->odeva_monthly_budget, 2) }}</small>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6 mb-3">
                    <div class="card shadow-custom border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-people-fill text-info fs-1"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 text-muted">Active Subscriptions</h6>
                                    <h4 class="mb-0">{{ $activeSubscriptions }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                <div class="col-xl-3 col-sm-6 mb-3">
                    <div class="card shadow-custom border-0">
                        <div class="card-body">
                            <div class="d-flex align-items-center">
                                <div class="flex-shrink-0">
                                    <i class="bi bi-graph-up text-warning fs-1"></i>
                                </div>
                                <div class="flex-grow-1 ms-3">
                                    <h6 class="mb-1 text-muted">Today's Usage</h6>
                                    <h4 class="mb-0">${{ number_format($todayUsage, 2) }}</h4>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Settings Form -->
            <form action="{{ route('admin.odeva.update-settings') }}" method="POST">
                @csrf
                @method('PUT')

                <!-- Core Admin Controls -->
                <div class="card shadow-custom border-0 mb-4">
                    <div class="card-header bg-transparent border-bottom">
                        <h5 class="mb-0"><i class="bi bi-gear me-2"></i>Core Admin Controls</h5>
                    </div>
                    <div class="card-body p-lg-4">
                        
                        <fieldset class="row mb-3">
                            <legend class="col-form-label col-sm-2 pt-0 text-lg-end">Status</legend>
                            <div class="col-sm-10">
                                <div class="form-check form-switch form-switch-md">
                                    <input class="form-check-input" type="checkbox" name="odeva_enabled" value="1" {{ $settings->odeva_enabled ? 'checked' : '' }} role="switch">
                                    <label class="form-check-label">Enable Odeva Globally</label>
                                </div>
                            </div>
                        </fieldset>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label text-lg-end">AI Provider</label>
                            <div class="col-sm-10">
                                <select name="odeva_provider" class="form-select">
                                    <option value="anthropic" {{ $settings->odeva_provider === 'anthropic' ? 'selected' : '' }}>Anthropic (Claude)</option>
                                    <option value="openai" {{ $settings->odeva_provider === 'openai' ? 'selected' : '' }}>OpenAI</option>
                                </select>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label text-lg-end">API Key</label>
                            <div class="col-sm-10">
                                <input type="password" name="odeva_api_key" class="form-control" placeholder="Enter API key (encrypted)">
                                <small class="form-text text-muted">Current key is encrypted. Leave blank to keep existing.</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label text-lg-end">Model</label>
                            <div class="col-sm-10">
                                <input type="text" name="odeva_model" value="{{ $settings->odeva_model }}" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label text-lg-end">Max Tokens</label>
                            <div class="col-sm-10">
                                <input type="number" name="odeva_max_tokens" value="{{ $settings->odeva_max_tokens }}" min="100" max="100000" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label text-lg-end">Temperature</label>
                            <div class="col-sm-10">
                                <input type="number" name="odeva_temperature" value="{{ $settings->odeva_temperature }}" min="0" max="2" step="0.1" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-10 offset-sm-2">
                                <button type="button" onclick="testApiConnection()" class="btn btn-primary">
                                    <i class="bi bi-plug me-1"></i> Test API Connection
                                </button>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Budget & Cost Management -->
                <div class="card shadow-custom border-0 mb-4">
                    <div class="card-header bg-transparent border-bottom">
                        <h5 class="mb-0"><i class="bi bi-cash me-2"></i>Budget & Cost Management</h5>
                    </div>
                    <div class="card-body p-lg-4">
                        
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label text-lg-end">Monthly Budget ($)</label>
                            <div class="col-sm-10">
                                <input type="number" name="odeva_monthly_budget" value="{{ $settings->odeva_monthly_budget }}" min="0" step="0.01" class="form-control">
                            </div>
                        </div>

                        <fieldset class="row mb-3">
                            <div class="col-sm-10 offset-sm-2">
                                <div class="form-check form-switch form-switch-md">
                                    <input class="form-check-input" type="checkbox" name="odeva_auto_disable_on_budget" value="1" {{ $settings->odeva_auto_disable_on_budget ? 'checked' : '' }}>
                                    <label class="form-check-label">Auto-disable when budget exceeded</label>
                                </div>
                            </div>
                        </fieldset>

                        <div class="row mb-3">
                            <div class="col-sm-10 offset-sm-2">
                                <a href="{{ route('admin.odeva.cost-analytics') }}" class="btn btn-outline-primary me-2">
                                    <i class="bi bi-bar-chart me-1"></i> View Cost Analytics
                                </a>
                                <a href="{{ route('admin.odeva.export-cost-report') }}" class="btn btn-outline-secondary">
                                    <i class="bi bi-download me-1"></i> Export Cost Report
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Creator Management -->
                <div class="card shadow-custom border-0 mb-4">
                    <div class="card-header bg-transparent border-bottom">
                        <h5 class="mb-0"><i class="bi bi-person-check me-2"></i>Creator Management</h5>
                    </div>
                    <div class="card-body p-lg-4">
                        
                        <fieldset class="row mb-3">
                            <div class="col-sm-10 offset-sm-2">
                                <div class="form-check form-switch form-switch-md">
                                    <input class="form-check-input" type="checkbox" name="odeva_require_approval" value="1" {{ $settings->odeva_require_approval ? 'checked' : '' }}>
                                    <label class="form-check-label">Require admin approval for new subscriptions</label>
                                </div>
                            </div>
                        </fieldset>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label text-lg-end">Message Limit (monthly)</label>
                            <div class="col-sm-10">
                                <input type="number" name="odeva_creator_message_limit" value="{{ $settings->odeva_creator_message_limit }}" min="0" class="form-control">
                                <small class="form-text text-muted">Per creator, per month</small>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <div class="col-sm-10 offset-sm-2">
                                <a href="{{ route('admin.odeva.creators') }}" class="btn btn-outline-primary">
                                    <i class="bi bi-people me-1"></i> Manage Creators & Permissions
                                </a>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Feature Toggles -->
                <div class="card shadow-custom border-0 mb-4">
                    <div class="card-header bg-transparent border-bottom">
                        <h5 class="mb-0"><i class="bi bi-toggles me-2"></i>Feature Toggles</h5>
                    </div>
                    <div class="card-body p-lg-4">
                        
                        <div class="row mb-3">
                            <div class="col-sm-10 offset-sm-2">
                                <div class="form-check form-switch form-switch-md mb-2">
                                    <input class="form-check-input" type="checkbox" name="odeva_automation_enabled" value="1" {{ $settings->odeva_automation_enabled ? 'checked' : '' }}>
                                    <label class="form-check-label">Enable Automation</label>
                                </div>
                                <div class="form-check form-switch form-switch-md mb-2">
                                    <input class="form-check-input" type="checkbox" name="odeva_analytics_enabled" value="1" {{ $settings->odeva_analytics_enabled ? 'checked' : '' }}>
                                    <label class="form-check-label">Enable Analytics</label>
                                </div>
                                <div class="form-check form-switch form-switch-md mb-2">
                                    <input class="form-check-input" type="checkbox" name="odeva_learning_enabled" value="1" {{ $settings->odeva_learning_enabled ? 'checked' : '' }}>
                                    <label class="form-check-label">Enable Learning from Examples</label>
                                </div>
                                <div class="form-check form-switch form-switch-md">
                                    <input class="form-check-input" type="checkbox" name="odeva_subscriptions_enabled" value="1" {{ $settings->odeva_subscriptions_enabled ? 'checked' : '' }}>
                                    <label class="form-check-label">Enable Subscriptions</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label text-lg-end">Free Trial (days)</label>
                            <div class="col-sm-10">
                                <input type="number" name="odeva_trial_days" value="{{ $settings->odeva_trial_days }}" min="0" max="365" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Subscription Plans -->
                <div class="card shadow-custom border-0 mb-4">
                    <div class="card-header bg-transparent border-bottom">
                        <h5 class="mb-0"><i class="bi bi-credit-card me-2"></i>Subscription Plans</h5>
                    </div>
                    <div class="card-body p-lg-4">
                        
                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label text-lg-end">Subscription Price</label>
                            <div class="col-sm-10">
                                <input type="number" name="odeva_subscription_price" value="{{ $settings->odeva_subscription_price }}" min="0" step="0.01" class="form-control">
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label text-lg-end">Currency</label>
                            <div class="col-sm-10">
                                <select name="odeva_subscription_currency" class="form-select">
                                    <option value="USD" {{ $settings->odeva_subscription_currency === 'USD' ? 'selected' : '' }}>USD</option>
                                    <option value="EUR" {{ $settings->odeva_subscription_currency === 'EUR' ? 'selected' : '' }}>EUR</option>
                                    <option value="GBP" {{ $settings->odeva_subscription_currency === 'GBP' ? 'selected' : '' }}>GBP</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Safety & Moderation -->
                <div class="card shadow-custom border-0 mb-4">
                    <div class="card-header bg-transparent border-bottom">
                        <h5 class="mb-0"><i class="bi bi-shield-check me-2"></i>Safety & Moderation</h5>
                    </div>
                    <div class="card-body p-lg-4">
                        
                        <div class="row mb-3">
                            <div class="col-sm-10 offset-sm-2">
                                <div class="form-check form-switch form-switch-md mb-2">
                                    <input class="form-check-input" type="checkbox" name="odeva_content_moderation" value="1" {{ $settings->odeva_content_moderation ? 'checked' : '' }}>
                                    <label class="form-check-label">Enable Content Moderation</label>
                                </div>
                                <div class="form-check form-switch form-switch-md">
                                    <input class="form-check-input" type="checkbox" name="odeva_activity_logging" value="1" {{ $settings->odeva_activity_logging ? 'checked' : '' }}>
                                    <label class="form-check-label">Enable Activity Logging</label>
                                </div>
                            </div>
                        </div>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label text-lg-end">Rate Limit (req/min)</label>
                            <div class="col-sm-10">
                                <input type="number" name="odeva_rate_limit" value="{{ $settings->odeva_rate_limit }}" min="1" max="1000" class="form-control">
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Emergency Controls -->
                <div class="card shadow-custom border-0 border-danger mb-4">
                    <div class="card-header bg-danger bg-opacity-10 border-bottom border-danger">
                        <h5 class="mb-0 text-danger"><i class="bi bi-exclamation-triangle me-2"></i>Emergency Controls</h5>
                    </div>
                    <div class="card-body p-lg-4">
                        
                        <fieldset class="row mb-3">
                            <div class="col-sm-10 offset-sm-2">
                                <div class="form-check form-switch form-switch-md">
                                    <input class="form-check-input" type="checkbox" name="odeva_emergency_stop" value="1" {{ $settings->odeva_emergency_stop ? 'checked' : '' }}>
                                    <label class="form-check-label fw-bold text-danger">🚨 Emergency Stop (Disable All Odeva Functions)</label>
                                </div>
                            </div>
                        </fieldset>

                        <div class="row mb-3">
                            <label class="col-sm-2 col-form-label text-lg-end">Emergency Message</label>
                            <div class="col-sm-10">
                                <textarea name="odeva_emergency_message" rows="3" class="form-control" placeholder="Message to display when emergency stop is active">{{ $settings->odeva_emergency_message }}</textarea>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- Submit Button -->
                <div class="row">
                    <div class="col-12 text-end">
                        <button type="submit" class="btn btn-primary btn-lg px-5">
                            <i class="bi bi-check-lg me-1"></i> Save All Settings
                        </button>
                    </div>
                </div>
            </form>

        </div>
    </div>
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
        if (data.success) {
            alert('✅ ' + data.message);
        } else {
            alert('❌ ' + data.message);
        }
    })
    .catch(error => {
        alert('❌ Error testing connection: ' + error);
    });
}
</script>
@endsection
