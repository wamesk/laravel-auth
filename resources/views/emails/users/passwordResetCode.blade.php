@component('mail::message')

<h1>{{ trans('emails.passwordResetCode.h1') }}</h1>
<p>{{ trans('emails.passwordResetCode.p') }}</p>

<div style="width: 100%; display: flex; justify-content: center;">
    <div style="
        padding: .5em 1em;
        border: 1px solid black;
        display:inline-block;
        letter-spacing: 5px;
        font-size: 26px;
        font-weight:bold;
        margin-bottom: 1em;
        color: #323232;
    ">{{ $code }}</div>
</div>

<small>{{ trans('emails.passwordResetCode.small') }}</small>
<br><br>
<small>{{ trans('emails.passwordResetCode.regards') }}</small>

<br>
{{ config('app.name') }}<br>

@endcomponent
