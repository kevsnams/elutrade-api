Hi {{ $user->full_name }},<br>
<br>
Thanks for using {{ config('app.name') }}!<br>
We'll communicate with you from time to time via email so it's important that we have an up-to-date email address on file.<br>
<br><br>
Verification Code:<br>
<strong>{{ $verificationCode }}</strong><br>
<br><br>
Happy Trading!<br>
<br>
Regards,<br>
The {{ config('app.name') }} Team
