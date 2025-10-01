@extends('admin.layout')

@section('content')
<style>
.odeva-container {
    max-width: 1200px;
    margin: 0 auto;
}
.odeva-header {
    padding: 2rem 0;
    border-bottom: 1px solid #e5e7eb;
}
.odeva-card {
    background: #fff;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1.5rem;
    margin-bottom: 1.5rem;
}
.odeva-card-header {
    font-size: 0.875rem;
    font-weight: 600;
    color: #6b7280;
    margin-bottom: 1rem;
    text-transform: uppercase;
    letter-spacing: 0.05em;
}
.odeva-switch {
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
}
.odeva-input {
    width: 100%;
    padding: 0.5rem 0.75rem;
    border: 1px solid #d1d5db;
    border-radius: 6px;
    font-size: 0.875rem;
}
.odeva-input:focus {
    outline: none;
    border-color: #6366f1;
    box-shadow: 0 0 0 3px rgba(99, 102, 241, 0.1);
}
.odeva-label {
    display: block;
    font-size: 0.875rem;
    font-weight: 500;
    color: #374151;
    margin-bottom: 0.5rem;
}
.odeva-slider {
    width: 100%;
    height: 6px;
    border-radius: 3px;
    background: #e5e7eb;
    outline: none;
}
.odeva-slider::-webkit-slider-thumb {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #6366f1;
    cursor: pointer;
}
.odeva-slider::-moz-range-thumb {
    width: 18px;
    height: 18px;
    border-radius: 50%;
    background: #6366f1;
    cursor: pointer;
    border: none;
}
.odeva-stats {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 1rem;
    margin-bottom: 2rem;
}
.odeva-stat-card {
    background: #f9fafb;
    border: 1px solid #e5e7eb;
    border-radius: 8px;
    padding: 1rem;
}
.odeva-stat-label {
    font-size: 0.75rem;
    color: #6b7280;
    margin-bottom: 0.25rem;
}
.odeva-stat-value {
    font-size: 1.5rem;
    font-weight: 600;
    color: #111827;
}
.odeva-btn {
    padding: 0.5rem 1rem;
    border-radius: 6px;
    font-size: 0.875rem;
    font-weight: 500;
    cursor: pointer;
    transition: all 0.2s;
}
.odeva-btn-primary {
    background: #6366f1;
    color: white;
    border: none;
}
.odeva-btn-primary:hover {
    background: #4f46e5;
}
.odeva-btn-secondary {
    background: white;
    color: #374151;
    border: 1px solid #d1d5db;
}
.odeva-btn-secondary:hover {
    background: #f9fafb;
}
</style>

<div class="odeva-container">
    <!-- Header -->
    <div class="odeva-header">
        <div class="d-flex justify-content-between align-items-start">
            <div>
                <h3 class="mb-1">Odeva AI Configuration</h3>
                <p class="text-muted mb-0">Configure your AI assistant behavior and settings</p>
            </div>
            <div class="d-flex gap-2 align-items-center">
                <a href="{{ route('admin.odeva.cost-analytics') }}" class="odeva-btn odeva-btn-secondary">
                    <i class="bi bi-bar-chart me-1"></i> View Analytics
                </a>
                <a href="{{ route('admin.odeva.creators') }}" class="odeva-btn odeva-btn-secondary">
                    <i class="bi bi-people me-1"></i> Manage Creators
                </a>
                <a href="{{ route('admin.odeva.export-cost-report') }}" class="odeva-btn odeva-btn-secondary">
                    <i class="bi bi-download me-1"></i> Export Report
                </a>
                @if($settings->odeva_enabled)
                    <span class="badge bg-success">Active</span>
                @else
                    <span class="badge bg-secondary">Disabled</span>
                @endif
            </div>
        </div>
    </div>

    @if(session('success'))
        <div class="alert alert-success alert-dismissible fade show mt-3" role="alert">
            <i class="bi bi-check2 me-1"></i> {{ session('success') }}
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
    @endif

    <!-- Stats -->
    <div class="odeva-stats mt-4">
        <div class="odeva-stat-card">
            <div class="odeva-stat-label">Monthly Spending</div>
            <div class="odeva-stat-value">${{ number_format($currentSpending, 2) }}</div>
            <small class="text-muted">of ${{ number_format($settings->odeva_monthly_budget, 2) }} budget</small>
        </div>
        <div class="odeva-stat-card">
            <div class="odeva-stat-label">Active Subscriptions</div>
            <div class="odeva-stat-value">{{ $activeSubscriptions }}</div>
        </div>
        <div class="odeva-stat-card">
            <div class="odeva-stat-label">Trial Users</div>
            <div class="odeva-stat-value">{{ $trialSubscriptions }}</div>
        </div>
        <div class="odeva-stat-card">
            <div class="odeva-stat-label">Today's Usage</div>
            <div class="odeva-stat-value">${{ number_format($todayUsage, 2) }}</div>
        </div>
    </div>

    <form action="{{ route('admin.odeva.update-settings') }}" method="POST">
        @csrf
        @method('PUT')

        <!-- Model Configuration -->
        <div class="odeva-card">
            <div class="odeva-card-header">Model</div>
            
            <div class="mb-3">
                <label class="odeva-label">Provider</label>
                <select name="odeva_provider" class="odeva-input">
                    <option value="anthropic" {{ $settings->odeva_provider === 'anthropic' ? 'selected' : '' }}>Anthropic</option>
                    <option value="openai" {{ $settings->odeva_provider === 'openai' ? 'selected' : '' }}>OpenAI</option>
                </select>
            </div>

            <div class="mb-3">
                <label class="odeva-label">Model</label>
                <input type="text" name="odeva_model" value="{{ $settings->odeva_model }}" class="odeva-input" placeholder="claude-3-5-sonnet-20241022">
            </div>

            <div class="mb-3">
                <label class="odeva-label">API Key</label>
                <input type="password" name="odeva_api_key" class="odeva-input" placeholder="sk-ant-...">
                <small class="text-muted d-block mt-1">Leave blank to keep current key</small>
            </div>

            <button type="button" onclick="testApiConnection()" class="odeva-btn odeva-btn-secondary">
                <i class="bi bi-plug me-1"></i> Test Connection
            </button>
        </div>

        <!-- Model Configuration Parameters -->
        <div class="odeva-card">
            <div class="odeva-card-header">Model Configuration</div>
            
            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="odeva-label mb-0">Temperature</label>
                    <span class="text-muted" id="tempValue">{{ $settings->odeva_temperature }}</span>
                </div>
                <input type="range" name="odeva_temperature" value="{{ $settings->odeva_temperature }}" min="0" max="2" step="0.1" class="odeva-slider" oninput="document.getElementById('tempValue').textContent = this.value">
                <div class="d-flex justify-content-between mt-1">
                    <small class="text-muted">Precise</small>
                    <small class="text-muted">Creative</small>
                </div>
            </div>

            <div class="mb-4">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="odeva-label mb-0">Maximum tokens</label>
                    <span class="text-muted" id="tokensValue">{{ $settings->odeva_max_tokens }}</span>
                </div>
                <input type="range" name="odeva_max_tokens" value="{{ $settings->odeva_max_tokens }}" min="100" max="100000" step="100" class="odeva-slider" oninput="document.getElementById('tokensValue').textContent = this.value">
            </div>

            <div class="mb-3">
                <div class="d-flex justify-content-between align-items-center mb-2">
                    <label class="odeva-label mb-0">Rate limit (requests/min)</label>
                    <span class="text-muted" id="rateLimitValue">{{ $settings->odeva_rate_limit }}</span>
                </div>
                <input type="range" name="odeva_rate_limit" value="{{ $settings->odeva_rate_limit }}" min="1" max="100" class="odeva-slider" oninput="document.getElementById('rateLimitValue').textContent = this.value">
            </div>
        </div>

        <!-- Features -->
        <div class="odeva-card">
            <div class="odeva-card-header">Features</div>
            
            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="odeva_enabled" value="1" {{ $settings->odeva_enabled ? 'checked' : '' }} id="odevaEnabled">
                    <label class="form-check-label" for="odevaEnabled">Enable Odeva</label>
                </div>
            </div>

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="odeva_automation_enabled" value="1" {{ $settings->odeva_automation_enabled ? 'checked' : '' }} id="automation">
                    <label class="form-check-label" for="automation">Automation</label>
                </div>
            </div>

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="odeva_analytics_enabled" value="1" {{ $settings->odeva_analytics_enabled ? 'checked' : '' }} id="analytics">
                    <label class="form-check-label" for="analytics">Analytics</label>
                </div>
            </div>

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="odeva_learning_enabled" value="1" {{ $settings->odeva_learning_enabled ? 'checked' : '' }} id="learning">
                    <label class="form-check-label" for="learning">Learning from examples</label>
                </div>
            </div>

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="odeva_content_moderation" value="1" {{ $settings->odeva_content_moderation ? 'checked' : '' }} id="moderation">
                    <label class="form-check-label" for="moderation">Content moderation</label>
                </div>
            </div>

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="odeva_activity_logging" value="1" {{ $settings->odeva_activity_logging ? 'checked' : '' }} id="logging">
                    <label class="form-check-label" for="logging">Activity logging</label>
                </div>
            </div>
        </div>

        <!-- Subscriptions -->
        <div class="odeva-card">
            <div class="odeva-card-header">Subscriptions</div>
            
            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="odeva_subscriptions_enabled" value="1" {{ $settings->odeva_subscriptions_enabled ? 'checked' : '' }} id="subscriptions">
                    <label class="form-check-label" for="subscriptions">Enable subscriptions</label>
                </div>
            </div>

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="odeva_require_approval" value="1" {{ $settings->odeva_require_approval ? 'checked' : '' }} id="approval">
                    <label class="form-check-label" for="approval">Require admin approval</label>
                </div>
            </div>

            <div class="row">
                <div class="col-md-4 mb-3">
                    <label class="odeva-label">Subscription Price</label>
                    <input type="number" name="odeva_subscription_price" value="{{ $settings->odeva_subscription_price }}" class="odeva-input" step="0.01">
                </div>
                <div class="col-md-4 mb-3">
                    <label class="odeva-label">Currency</label>
                    <select name="odeva_subscription_currency" class="odeva-input">
                        @foreach (config('currencies.supported') as $code => $label)
                            @php 
                                $currencyCode = is_numeric($code) ? $label : $code;
                                $currencyLabel = is_numeric($code) ? $label : $label . ' (' . $code . ')';
                            @endphp
                            <option value="{{ $currencyCode }}" {{ $settings->odeva_subscription_currency === $currencyCode ? 'selected' : '' }}>
                                {{ $currencyLabel }}
                            </option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-3">
                    <label class="odeva-label">Free Trial (days)</label>
                    <input type="number" name="odeva_trial_days" value="{{ $settings->odeva_trial_days }}" class="odeva-input" min="0" max="365">
                </div>
            </div>

            <div class="mb-3">
                <label class="odeva-label">Message Limit (per creator/month)</label>
                <input type="number" name="odeva_creator_message_limit" value="{{ $settings->odeva_creator_message_limit }}" class="odeva-input">
            </div>
        </div>

        <!-- Budget -->
        <div class="odeva-card">
            <div class="odeva-card-header">Budget & Cost</div>
            
            <div class="mb-3">
                <label class="odeva-label">Monthly Budget ($)</label>
                <input type="number" name="odeva_monthly_budget" value="{{ $settings->odeva_monthly_budget }}" class="odeva-input" step="0.01">
            </div>

            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="odeva_auto_disable_on_budget" value="1" {{ $settings->odeva_auto_disable_on_budget ? 'checked' : '' }} id="autoBudget">
                    <label class="form-check-label" for="autoBudget">Auto-disable when budget exceeded</label>
                </div>
            </div>
        </div>

        <!-- Emergency -->
        <div class="odeva-card border-danger">
            <div class="odeva-card-header text-danger">Emergency Controls</div>
            
            <div class="mb-3">
                <div class="form-check form-switch">
                    <input class="form-check-input" type="checkbox" name="odeva_emergency_stop" value="1" {{ $settings->odeva_emergency_stop ? 'checked' : '' }} id="emergency">
                    <label class="form-check-label text-danger fw-bold" for="emergency">
                        🚨 Emergency Stop (Disable All)
                    </label>
                </div>
            </div>

            <div class="mb-3">
                <label class="odeva-label">Emergency Message</label>
                <textarea name="odeva_emergency_message" rows="3" class="odeva-input" placeholder="This message will be displayed when emergency mode is active">{{ $settings->odeva_emergency_message }}</textarea>
            </div>
        </div>

        <!-- Submit -->
        <div class="d-flex justify-content-end gap-2 mb-4">
            <a href="{{ url('panel/admin') }}" class="odeva-btn odeva-btn-secondary">Cancel</a>
            <button type="submit" class="odeva-btn odeva-btn-primary">
                <i class="bi bi-check-lg me-1"></i> Save Configuration
            </button>
        </div>
    </form>
</div>

<script>
function testApiConnection() {
    const btn = event.target;
    const originalText = btn.innerHTML;
    btn.disabled = true;
    btn.innerHTML = '<span class="spinner-border spinner-border-sm me-1"></span> Testing...';
    
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
        alert('❌ Connection test failed: ' + error);
    })
    .finally(() => {
        btn.disabled = false;
        btn.innerHTML = originalText;
    });
}
</script>
@endsection
