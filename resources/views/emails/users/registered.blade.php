@component('mail::message')
<h1>@lang('Verify your email address')</h1>
<p>To continue setting up your account, please verify that this is your email address.</p>

<p>@component('mail::button', ['url' => $url])@lang('Verify email address')@endcomponent</p>


@lang('Regards,') <br>
{{ config('app.name') }}<br>
@endcomponent
