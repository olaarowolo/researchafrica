<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New AfriScribe Request</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            line-height: 1.6;
            color: #333;
            max-width: 600px;
            margin: 0 auto;
            padding: 20px;
        }
        .header {
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            color: white;
            padding: 20px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .request-details {
            background: white;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .detail-row {
            display: flex;
            margin-bottom: 10px;
        }
        .detail-label {
            font-weight: bold;
            color: #555;
            width: 120px;
            flex-shrink: 0;
        }
        .detail-value {
            color: #333;
        }
        .message-box {
            background: #e8f4f8;
            padding: 15px;
            border-radius: 5px;
            border-left: 4px solid #4facfe;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            padding-top: 20px;
            border-top: 1px solid #eee;
            color: #666;
        }
        .request-id {
            background: #4facfe;
            color: white;
            padding: 5px 10px;
            border-radius: 15px;
            font-size: 0.8rem;
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>🔔 New AfriScribe Request</h1>
        <p>You have received a new proofreading request</p>
    </div>

    <div class="content">
        <div class="request-details">
            <div style="text-align: center; margin-bottom: 20px;">
                <span class="request-id">Request #{{ $data['request_id'] }}</span>
            </div>

            <div class="detail-row">
                <div class="detail-label">Name:</div>
                <div class="detail-value">{{ $data['name'] }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Email:</div>
                <div class="detail-value">{{ $data['email'] }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Service Type:</div>
                <div class="detail-value">{{ $data['service_type'] }}</div>
            </div>

            <div class="detail-row">
                <div class="detail-label">Submitted:</div>
                <div class="detail-value">{{ now()->format('F j, Y \a\t g:i A') }}</div>
            </div>

            @if(!empty($data['original_filename']))
            <div class="detail-row">
                <div class="detail-label">Document:</div>
                <div class="detail-value">{{ $data['original_filename'] }}</div>
            </div>
            @endif
        </div>

        <div class="message-box">
            <h4 style="margin-top: 0; color: #4facfe;">Client Message:</h4>
            <div style="white-space: pre-wrap;">{{ $data['message'] }}</div>
        </div>

        <div style="background: #fff3cd; padding: 15px; border-radius: 5px; border-left: 4px solid #ffc107; margin: 20px 0;">
            <h4 style="margin-top: 0; color: #856404;">📋 Action Required:</h4>
            <ul style="margin: 10px 0 0 20px; padding: 0;">
                <li>Review the request details above</li>
                <li>Download and review the attached document (if provided)</li>
                <li>Respond to the client within 24 hours</li>
                <li>Provide a quote and timeline for the service</li>
            </ul>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <a href="{{ url('/admin/afriscribe-requests') }}" style="background: #4facfe; color: white; padding: 12px 30px; text-decoration: none; border-radius: 25px; font-weight: bold;">View All Requests</a>
        </div>
    </div>

    <div class="footer">
        <p><strong>AfriScribe</strong> - Professional Academic Proofreading Services</p>
        <p>This is an automated message from the AfriScribe platform.</p>
    </div>
</body>
</html>
