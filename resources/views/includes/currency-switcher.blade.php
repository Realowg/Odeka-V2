<form action="{{ route('currency.switch') }}" method="POST" class="d-inline">
  @csrf
  <select name="currency" class="form-control form-control-sm d-inline w-auto" onchange="this.form.submit()">
    @php
      $currentCurrency = app()->bound('display_currency') ? app('display_currency') : config('settings.currency_code');
      $supported = config('currencies.supported') ?? [config('settings.currency_code')];
    @endphp
    @foreach ($supported as $code => $label)
      @php
        $value = is_numeric($code) ? $label : $code;
        $text = is_numeric($code) ? $label : ($label . ' (' . $code . ')');
      @endphp
      <option value="{{ $value }}" @if (strtoupper($currentCurrency) == strtoupper($value)) selected @endif>{{ $text }}</option>
    @endforeach
  </select>
</form>


