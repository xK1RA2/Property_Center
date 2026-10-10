<!doctype html>
<html lang="en">
<body style="font-family: Arial, Helvetica, sans-serif; color:#14201c; line-height:1.5; max-width:520px; margin:0 auto; padding:24px;">
    <h2 style="color:#00B98E; margin:0 0 16px;">{{ config('app.name') }}</h2>

    <p>Thanks for your interest in our newsletter.</p>
    <p>Please confirm your email address to start receiving property news and offers:</p>

    <p style="margin:24px 0;">
        <a href="{{ $confirmUrl }}"
           style="background:#00B98E; color:#ffffff; padding:12px 20px; border-radius:6px; text-decoration:none; font-weight:bold;">
            Confirm subscription
        </a>
    </p>

    <p style="color:#6b7a75; font-size:13px;">
        If the button does not work, copy this link into your browser:<br>
        <span style="word-break:break-all;">{{ $confirmUrl }}</span>
    </p>
    <p style="color:#6b7a75; font-size:13px;">
        Did not ask for this? Ignore this email and you will not be subscribed.
    </p>
</body>
</html>
