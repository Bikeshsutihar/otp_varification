<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Your Verification Code</title>
</head>
<body style="margin:0;padding:0;background-color:#E5F0FF;font-family:-apple-system,Segoe UI,Roboto,Helvetica,Arial,sans-serif;">
  <table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#E5F0FF;padding:32px 0;">
    <tr>
      <td align="center">
        <table role="presentation" width="480" cellpadding="0" cellspacing="0" style="background-color:#ffffff;border-radius:16px;overflow:hidden;box-shadow:0 4px 24px rgba(0,20,51,0.08);">
          <tr>
            <td style="background-color:#003D99;padding:28px 32px;">
              <table role="presentation" width="100%">
                <tr>
                  <td style="color:#ffffff;font-size:20px;font-weight:700;letter-spacing:.5px;">
                    {{ config('app.name') }}
                  </td>
                </tr>
              </table>
            </td>
          </tr>
          <tr>
            <td style="padding:36px 32px 8px 32px;">
              <p style="color:#002966;font-size:16px;margin:0 0 8px 0;">Hi {{ $name }},</p>
              <p style="color:#334155;font-size:14px;line-height:1.6;margin:0 0 24px 0;">
                Use the verification code below to finish creating your account. This code is valid for
                <strong>{{ $expiresInMinutes }} minutes</strong>.
              </p>
            </td>
          </tr>
          <tr>
            <td align="center" style="padding:0 32px 28px 32px;">
              <div style="background-color:#E5F0FF;border:1px dashed #99C2FF;border-radius:12px;padding:20px 24px;display:inline-block;">
                <span style="font-size:34px;font-weight:800;letter-spacing:10px;color:#001433;">{{ $code }}</span>
              </div>
            </td>
          </tr>
          <tr>
            <td style="padding:0 32px 32px 32px;">
              <p style="color:#64748b;font-size:13px;line-height:1.6;margin:0;">
                Didn't request this code? You can safely ignore this email — no account will be created
                without verifying this code.
              </p>
            </td>
          </tr>
          <tr>
            <td style="background-color:#F1F6FF;padding:18px 32px;">
              <p style="color:#99C2FF;font-size:12px;margin:0;text-align:center;">
                &copy; {{ date('Y') }} {{ config('app.name') }}. All rights reserved.
              </p>
            </td>
          </tr>
        </table>
      </td>
    </tr>
  </table>
</body>
</html>
