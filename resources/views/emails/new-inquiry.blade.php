<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>New Inquiry Received</title>
    <style>
        body { font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif; background-color: #f8fafc; color: #334155; margin: 0; padding: 24px; }
        .card { background-color: #ffffff; max-width: 600px; margin: 0 auto; border-radius: 8px; border: 1px solid #e2e8f0; padding: 32px; box-shadow: 0 1px 3px rgba(0,0,0,0.05); }
        .header { border-bottom: 2px solid #3b82f6; padding-bottom: 16px; margin-bottom: 24px; }
        .header h1 { font-size: 20px; color: #1e293b; margin: 0; }
        .field { margin-bottom: 16px; }
        .label { font-size: 12px; font-weight: 700; text-transform: uppercase; color: #64748b; margin-bottom: 4px; }
        .value { font-size: 15px; color: #0f172a; word-break: break-word; }
        .message-box { background-color: #f1f5f9; padding: 16px; border-radius: 6px; white-space: pre-wrap; font-size: 14px; line-height: 1.6; border-left: 3px solid #3b82f6; }
        .footer { margin-top: 32px; padding-top: 16px; border-top: 1px solid #e2e8f0; font-size: 12px; color: #94a3b8; text-align: center; }
    </style>
</head>
<body>
    <div class="card">
        <div class="header">
            <h1>New Contact Form Submission</h1>
        </div>

        <div class="field">
            <div class="label">Sender Name</div>
            <div class="value"><strong>{{ $inquiry->name }}</strong></div>
        </div>

        <div class="field">
            <div class="label">Email Address</div>
            <div class="value"><a href="mailto:{{ $inquiry->email }}">{{ $inquiry->email }}</a></div>
        </div>

        @if(!empty($inquiry->phone))
        <div class="field">
            <div class="label">Phone Number</div>
            <div class="value"><a href="tel:{{ $inquiry->phone }}">{{ $inquiry->phone }}</a></div>
        </div>
        @endif

        @if(!empty($inquiry->serviceInterested))
        <div class="field">
            <div class="label">Service of Interest</div>
            <div class="value"><span style="background: #eff6ff; color: #1d4ed8; padding: 2px 8px; border-radius: 4px; font-weight: 600;">{{ $inquiry->serviceInterested }}</span></div>
        </div>
        @endif

        <div class="field">
            <div class="label">Message</div>
            <div class="message-box">{{ $inquiry->message }}</div>
        </div>

        <div class="field" style="margin-top: 24px; font-size: 13px; color: #64748b;">
            <span>Received on: {{ $inquiry->created_at?->format('F j, Y, g:i a') ?? now()->toDateTimeString() }}</span>
            @if(!empty($inquiry->ipAddress))
                <span> &bull; IP: {{ $inquiry->ipAddress }}</span>
            @endif
        </div>

        <div class="footer">
            This message was sent from your website's contact inquiry form.
        </div>
    </div>
</body>
</html>

