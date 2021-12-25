@component('mail::message')
# Test Invoice

The body of your message.

@component('mail::button', ['url' => ''])
Download Invoice
@endcomponent

Thanks,<br>
{{ config('app.name') }}
@endcomponent
