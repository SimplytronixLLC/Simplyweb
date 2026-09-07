<!DOCTYPE html>
<html>
<head>
<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
</head>
<body style="margin:0; padding:0; background-color:#f2f4f7; font-family: Arial, Helvetica, sans-serif;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background-color:#f2f4f7; padding:24px 0;">
<tr>
<td align="center">
<table role="presentation" width="600" cellpadding="0" cellspacing="0" style="background-color:#ffffff; border-radius:6px; overflow:hidden; box-shadow:0 1px 3px rgba(0,0,0,0.08);">

  <tr>
    <td style="background-color:#0b2545; padding:24px 32px; text-align:left;">
      <img src="https://simplytronix.com/public/assets/Simplylogo.png" alt="Simplytronix" height="36" style="display:block; border:0;">
    </td>
  </tr>

  <tr>
    <td style="padding:32px;">
      <p style="margin:0 0 16px; font-size:15px; line-height:1.6; color:#1a1a1a;">
        Hi {{ $displayName ?: 'there' }},
      </p>

      <p style="margin:0 0 24px; font-size:15px; line-height:1.6; color:#1a1a1a;">
        {{ $bodyText }}
      </p>

      <table role="presentation" cellpadding="0" cellspacing="0">
        <tr>
          <td style="background-color:#0b2545; border-radius:4px;">
            <a href="https://simplytronix.com/get-a-quote"
               style="display:inline-block; padding:12px 24px; font-size:14px; font-weight:bold; color:#ffffff; text-decoration:none;">
              Request a Quote
            </a>
          </td>
        </tr>
      </table>

      @if($latestPost)
      <p style="margin:28px 0 0; font-size:14px; line-height:1.6; color:#1a1a1a;">
        In the meantime, you might enjoy our latest read:
        <br>
        <a href="{{ url('blog/'.$latestPost->slug) }}" style="color:#0b2545; font-weight:bold; text-decoration:none;">
          {{ $latestPost->title }} &rarr;
        </a>
      </p>
      @endif

      <p style="margin:24px 0 0; font-size:15px; line-height:1.6; color:#1a1a1a;">
        Thanks again for considering Simplytronix.
      </p>

      <p style="margin:16px 0 0; font-size:15px; line-height:1.6; color:#1a1a1a;">
        &mdash; Simplytronix
      </p>
    </td>
  </tr>

  <tr>
    <td style="background-color:#f7f8fa; padding:20px 32px; border-top:1px solid #e6e8eb;">
      <p style="margin:0 0 4px; font-size:12px; color:#6b7280;">Simplytronix LLC &middot; 1007 N Orange St, Suite# 1382, Wilmington, DE 19801, United States</p>
      <p style="margin:0; font-size:12px; color:#6b7280;">+1 302-600-2554 &nbsp;&middot;&nbsp; <a href="https://simplytronix.com" style="color:#6b7280;">simplytronix.com</a></p>
    </td>
  </tr>

</table>
</td>
</tr>
</table>
</body>
</html>
