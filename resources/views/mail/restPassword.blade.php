@component('mail::message')
# Reset Password
Reset or change your password.
@component('mail::button', ['url' => 'http://localhost:3000/change-password?token='.$token])
Change Password
@endcomponent
Thanks,<br>
{{ config('app.name') }}
@endcomponent