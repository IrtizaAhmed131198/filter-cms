<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>{{ $user->status ? 'Account Verified' : 'Account Unverified' }} - {{ config('app.name') }}</title>
</head>
<body style="margin:0; padding:0; background-color:#f6f6f6; font-family:Arial, Helvetica, sans-serif;">
  <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
      <td align="center" style="padding:20px 0;">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" style="background-color:#ffffff; border-radius:5px; overflow:hidden;">

          <!-- Header -->
          <tr>
            <td align="center" bgcolor="{{ $user->status ? '#28a745' : '#dc3545' }}" style="padding:20px;">
              <h1 style="color:#ffffff; margin:0; font-size:24px;">
                {{ $user->status ? 'Account Verified ✅' : 'Account Unverified ⚠️' }}
              </h1>
            </td>
          </tr>

          <!-- Content -->
          <tr>
            <td style="padding:30px; color:#333333; line-height:1.6;">
              <h2 style="color:#333333;">Hello {{ $user->name }},</h2>

              @if($user->status)
                <p>We’re pleased to inform you that your account has been <strong>verified and activated</strong>.</p>
                <p>You can now log in to your account and access all features available to our verified users.</p>

                <!-- Login Button -->
                <table role="presentation" align="center" style="margin-top:20px;">
                  <tr>
                    <td bgcolor="#28a745" style="border-radius:4px;">
                      <a href="{{ url('/login') }}" style="display:inline-block; padding:12px 24px; color:#ffffff; text-decoration:none; font-weight:bold;">Login to Your Account</a>
                    </td>
                  </tr>
                </table>
              @else
                <p>Your account has been <strong>unverified or temporarily disabled</strong> by our administrator.</p>
                <p>If you believe this was a mistake or wish to reactivate your account, please contact our support team.</p>
              @endif

              <p style="margin-top:30px;">Thank you for being a part of <strong>{{ config('app.name') }}</strong>.</p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
