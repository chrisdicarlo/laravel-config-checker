<div>
    <h1>Test View One</h1>

    {{ config('app.valid_key') }}
    {{ Config::get('app.valid_key') }}
    {{ Config::has('app.valid_key') }}
</div>
