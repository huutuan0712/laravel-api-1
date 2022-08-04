@component('mail::message')
# Welcome to the first Newsletter

Dear {{$data['name']}} ,

We look forward to communicating more with you. For more information visit our blog.

@component('mail::button', ['url' => 'http://localhost:3000/verified'])
Verifield Email Address
@endcomponent

Thanks,<br>

@endcomponent