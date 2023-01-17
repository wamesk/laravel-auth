@component('mail::message')
<h1>@lang('We have received a request to change your password')</h1>
<p>Below we send you the code necessary for verification when changing the password.</p>

<div style="width: 100%; display: flex; justify-content: center;">
    <div
        style=
            "
            padding: .5em 1em;
            border: 1px solid black;
            display:inline-block;
            letter-spacing: 5px;
            font-size: 26px;
            font-weight:bold;
            margin-bottom: 1em;
            color: #323232;
            "
    >{{ $code }}</div>
</div>

<p><small>If you have not requested a password change, ignore this email or contact the site administrator.</small></p>

<br>
@lang('Regards,') <br>
{{ config('app.name') }}<br>
@endcomponent
