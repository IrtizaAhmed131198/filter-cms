<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Welcome to Our Service</title>
</head>
<body style="margin:0; padding:0; background-color:#f6f6f6; font-family:Arial, Helvetica, sans-serif;">
  <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="100%">
    <tr>
      <td align="center" style="padding:20px 0;">
        <table role="presentation" border="0" cellpadding="0" cellspacing="0" width="600" style="background-color:#ffffff; border-radius:5px; overflow:hidden;">

          <!-- Header -->
          <tr>
            <td align="center" bgcolor="#4a6ee0" style="padding:20px;">
              <h1 style="color:#ffffff; margin:0; font-size:24px;">Welcome to Our Service!</h1>
            </td>
          </tr>

          <!-- Content -->
          <tr>
            <td style="padding:30px; color:#333333; line-height:1.6;">
              <h2 style="color:#333333;">Hello {{ $user->name }},</h2>
              <p>Your account has been successfully created.</p>
              <p><strong>Login Email:</strong> {{ $user->email }}</p>

              <p>With our service, you'll be able to:</p>
              <ul style="padding-left:20px; margin:10px 0;">
                <li>Access exclusive features</li>
                <li>Connect with like-minded people</li>
                <li>Get personalized recommendations</li>
                <li>Stay updated with the latest news</li>
              </ul>

              <p>To get started, simply explore our platform and don't hesitate to reach out if you have any questions.</p>

              <!-- Button -->
              <table role="presentation" align="center" style="margin-top:20px;">
                <tr>
                  <td bgcolor="#4a6ee0" style="border-radius:4px;">
                    <a href="{{ url('/login') }}" style="display:inline-block; padding:12px 24px; color:#ffffff; text-decoration:none; font-weight:bold;">Login Now</a>
                  </td>
                </tr>
              </table>
            </td>
          </tr>

          <!-- Footer -->
          <tr>
            <td align="center" bgcolor="#f0f0f0" style="padding:20px; font-size:12px; color:#666666;">
              <p style="margin:0;">You're receiving this email because you signed up for our service.</p>
              <p style="margin:10px 0;">
                <a href="#" style="color:#4a6ee0; text-decoration:none;">Website</a> |
                <a href="#" style="color:#4a6ee0; text-decoration:none;">Facebook</a> |
                <a href="#" style="color:#4a6ee0; text-decoration:none;">Twitter</a> |
                <a href="#" style="color:#4a6ee0; text-decoration:none;">Instagram</a>
              </p>
              <p style="margin:0;">&copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.</p>
            </td>
          </tr>

        </table>
      </td>
    </tr>
  </table>
</body>
</html>
