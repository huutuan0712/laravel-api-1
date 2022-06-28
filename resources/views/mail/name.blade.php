@component('mail::message')
# Welcome to the first Newsletter

Dear {{$data['name']}} ,

We look forward to communicating more with you. For more information visit our blog.

@component('mail::button', ['url' => 'http://127.0.0.1:8000/api/email/verify/'.$data['id']])
Verifield Email Address
@endcomponent

Thanks,<br>

@endcomponent