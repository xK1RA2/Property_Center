<!doctype html>
<html lang="en">
<body style="font-family: Arial, Helvetica, sans-serif; color:#14201c; line-height:1.6; max-width:560px; margin:0 auto; padding:24px;">
    <h2 style="color:#00B98E; margin:0 0 16px;">{{ config('app.name') }}</h2>

    {{-- plain text from the operator: escaped, with line breaks kept --}}
    <div>{!! nl2br(e($body)) !!}</div>

    <hr style="border:0; border-top:1px solid #dfe6e3; margin:32px 0 12px;">
    <p style="color:#6b7a75; font-size:12px;">
        You receive this because you subscribed on {{ config('app.url') }}.
        <a href="{{ $unsubscribeUrl }}" style="color:#6b7a75;">Unsubscribe</a>
    </p>
</body>
</html>
