<x-mail::message>
# You're Invited!

Hi there,

{{ $invitedBy }} has invited you to join {{ config('app.name') }} as an administrator.

To accept this invitation and create your account, please click the button below:

<x-mail::button :url="$acceptUrl">
Accept Invitation
</x-mail::button>

This invitation will expire on {{ $expiresAt->format('F j, Y \a\t g:i A') }}.

If you don't want to accept this invitation, you can simply ignore this email.

Thanks,<br>
{{ config('app.name') }}
</x-mail::message>
