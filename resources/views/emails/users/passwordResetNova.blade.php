@component('mail::message')

<h1>{{ trans('emails.passwordResetNova.h1') }}</h1>
<p>{{ trans('emails.passwordResetNova.p') }} </p>
<p>@component('mail::button', ['url' => $url]) {{ trans('emails.passwordResetNova.reset password') }} @endcomponent</p>
<p><small>{{ trans('emails.passwordResetNova.small') }} </small></p>
<br>
{{ trans('emails.passwordResetNova.regards') }}

<br>
{{ config('app.name') }}<br>

@endcomponent
