<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Acknowledgment - AfriScribe</title>
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
            background: linear-gradient(135deg, #4facfe 0%, #00f2fe 100%);
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f9f9f9;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .welcome-box {
            background: white;
            padding: 25px;
            border-radius: 8px;
            margin: 20px 0;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
            border-left: 4px solid #4facfe;
        }
        .request-summary {
            background: #e8f4f8;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
        }
        .next-steps {
            background: #d4edda;
            padding: 20px;
            border-radius: 8px;
            margin: 20px 0;
            border-left: 4px solid #28a745;
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
        .contact-info {
            background: #fff3cd;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #ffc107;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>✅ Request Received!</h1>
        <p>Thank you for choosing AfriScribe for your proofreading needs</p>
    </div>

    <div class="content">
        <div class="welcome-box">
            <h2 style="color: #4facfe; margin-top: 0;">Hello {{ $data['name'] }}!</h2>
            <p>We've successfully received your proofreading request and wanted to thank you for choosing AfriScribe. Our team of professional proofreaders is excited to help elevate the quality of your scholarly writing.</p>
        </div>

        <div class="request-summary">
            <h3 style="margin-top: 0; color: #4facfe;">📋 Request Summary</h3>
            <div style="margin-bottom: 10px;">
                <strong>Request ID:</strong> <span class="request-id">#{{ $data['request_id'] }}</span>
            </div>
            <div style="margin-bottom: 10px;">
                <strong>Service:</strong> {{ $data['service_type'] }}
            </div>
            <div style="margin-bottom: 10px;">
                <strong>Submitted:</strong> {{ now()->format('F j, Y \a\t g:i A') }}
            </div>
            @if(!empty($data['original_filename']))
            <div style="margin-bottom: 10px;">
                <strong>Document:</strong> {{ $data['original_filename'] }}
            </div>
            @endif
        </div>

        <div class="next-steps">
            <h3 style="margin-top: 0; color: #28a745;">🚀 What Happens Next?</h3>
            <ol style="margin: 10px 0 0 20px; padding: 0;">
                <li><strong>Review:</strong> Our team will review your document and requirements</li>
                <li><strong>Quote:</strong> We'll send you a personalized quote within 24 hours</li>
                <li><strong>Timeline:</strong> You'll receive an estimated completion timeline</li>
                <li><strong>Confirmation:</strong> Once you approve the quote, work begins immediately</li>
            </ol>
        </div>

        <div class="contact-info">
            <h4 style="margin-top: 0; color: #856404;">📞 Need to Reach Us?</h4>
            <p style="margin: 5px 0;">If you have any questions about your request or need to make changes:</p>
            <ul style="margin: 10px 0 0 20px; padding: 0;">
                <li><strong>Email:</strong> researchfripub@gmail.com</li>
                <li><strong>Response Time:</strong> Within 24 hours</li>
                <li><strong>Reference:</strong> Please include your Request ID (#{{ $data['request_id'] }})</li>
            </ul>
        </div>

        <div style="text-align: center; margin: 30px 0;">
            <p style="color: #666; font-style: italic;">"Elevating the Quality of Scholarly Writing"</p>
        </div>
    </div>

    <div class="footer">
        <p><strong>AfriScribe</strong> - Professional Academic Proofreading Services</p>
        <p>Research Africa | Empowering Scholarly Publishing Across Africa</p>
        <p style="font-size: 0.8rem; margin-top: 10px;">This is an automated acknowledgment. Please save this email for your records.</p>
    </div>
</body>
</html>
