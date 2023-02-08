@component('mail::message')
<h1>@lang('We have received a request to change your password')</h1>
<p>Below we send you the link for password reset.</p>

<p>@component('mail::button', ['url' => $url])@lang('Reset password')@endcomponent</p>

<p><small>If you have not requested a password change, ignore this email or contact the site administrator.</small></p>

<br>
@lang('Regards,') <br>
{{ config('app.name') }}<br>
@endcomponent
