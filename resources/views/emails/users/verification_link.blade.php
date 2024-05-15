@component('mail::message')

<h1>{{ __('laravel-auth::emails.verificationLink.h1') }}</h1>
<p>{{ __('laravel-auth::emails.verificationLink.p') }}</p>
<p>@component('mail::button', ['url' => $verificationLink]) {{ __('laravel-auth::emails.verificationLink.verify') }} @endcomponent</p>
{{ __('laravel-auth::emails.verificationLink.regards') }} <br>

{{ config('app.name') }}<br>

@endcomponent
