<div>
    <h1>Test View Two</h1>
    {{ config('app.invalid_key') }}
    {{ Config::get('app.invalid_key') }}
    {{ Config::has('app.invalid_key') }}

    {{ config('app.valid_key') }}
    {{ Config::get('app.valid_key') }}
    {{ Config::has('app.valid_key') }}
</div>
