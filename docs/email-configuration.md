# Email Configuration Guide

This project uses Resend as the email service provider for both development and production environments.

## Overview

Resend is integrated using a custom Laravel mail transport that directly uses the Resend PHP SDK. This provides better performance and error handling compared to SMTP.

## Configuration

### Environment Variables

Add the following to your `.env` file:

```env
MAIL_MAILER=resend
RESEND_API_KEY=your_resend_api_key_here
MAIL_FROM_ADDRESS="noreply@yourdomain.com"
MAIL_FROM_NAME="${APP_NAME}"
```

### Getting Your Resend API Key

1. Sign up for a Resend account at [https://resend.com](https://resend.com)
2. Navigate to API Keys in your dashboard
3. Create a new API key with "Send emails" permission
4. Copy the API key (starts with `re_`)

### Domain Verification

For production use, you'll need to verify your domain:

1. Add your domain in Resend dashboard
2. Add the required DNS records (SPF, DKIM, etc.)
3. Wait for verification (usually takes a few minutes)

## Testing Email Configuration

Use the built-in test command to verify your email configuration:

```bash
# Send test email to default address (from .env)
php artisan mail:test

# Send test email to specific address
php artisan mail:test recipient@example.com
```

## Implementation Details

### Custom Transport

The Resend integration uses a custom Symfony mail transport located at:
- `app/Mail/Transport/ResendTransport.php`

This transport:
- Converts Laravel/Symfony messages to Resend API format
- Handles attachments and inline images
- Provides proper error handling and logging
- Supports all standard email features (HTML, plain text, CC, BCC, Reply-To)

### Service Provider

The `ResendServiceProvider` registers the custom transport with Laravel's mail system:
- Located at: `app/Providers/ResendServiceProvider.php`
- Automatically registered in `bootstrap/providers.php`

### Configuration Files

- **config/mail.php**: Defines the 'resend' mailer configuration
- **config/services.php**: Stores the Resend API key configuration

## Sending Emails

### Basic Usage

```php
use Illuminate\Support\Facades\Mail;

// Send raw text email
Mail::raw('Your message here', function ($message) {
    $message->to('user@example.com')
        ->subject('Your Subject');
});

// Send using a Mailable class
Mail::to('user@example.com')->send(new YourMailable());
```

### With Attachments

```php
Mail::send('emails.template', $data, function ($message) {
    $message->to('user@example.com')
        ->subject('Your Subject')
        ->attach('/path/to/file.pdf');
});
```

## Troubleshooting

### Common Issues

1. **"Failed to send email" error**
   - Verify your API key is correct
   - Check that your domain is verified (for custom domains)
   - Ensure you have send permissions on the API key

2. **Emails not arriving**
   - Check spam folder
   - Verify recipient email is correct
   - Check Resend dashboard for delivery status

3. **Rate limiting**
   - Free tier: 100 emails/day, 10 emails/second
   - Check Resend dashboard for current usage

### Debug Mode

To enable detailed logging, add to your `.env`:

```env
LOG_LEVEL=debug
```

Then check `storage/logs/laravel.log` for detailed email sending logs.

## Security Considerations

1. **Never commit API keys** - Always use environment variables
2. **Use different API keys** for development and production
3. **Restrict API key permissions** to only what's needed
4. **Monitor usage** through Resend dashboard

## Next Steps

- Implement email templates for user invitations
- Set up email queuing for better performance
- Configure webhooks for delivery tracking