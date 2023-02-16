@component('mail::message')

<h1>{{ trans('emails.verificationLink.h1') }}</h1>
<p>{{ trans('emails.verificationLink.p') }}</p>
<p>@component('mail::button', ['url' => $verificationLink]) {{ trans('emails.verificationLink.verify') }} @endcomponent</p>
{{ trans('emails.verificationLink.regards') }} <br>

{{ config('app.name') }}<br>

@endcomponent

