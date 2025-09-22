<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quote Request Received - AfriScribe</title>
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
            background-color: #007bff;
            color: white;
            padding: 30px;
            text-align: center;
            border-radius: 5px;
            margin-bottom: 30px;
        }
        .content {
            background-color: #ffffff;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .highlight-box {
            background-color: #e3f2fd;
            padding: 20px;
            border-radius: 5px;
            margin: 20px 0;
            border-left: 4px solid #007bff;
        }
        .footer {
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
            text-align: center;
            color: #6c757d;
        }
        .button {
            display: inline-block;
            background-color: #007bff;
            color: white;
            padding: 12px 30px;
            text-decoration: none;
            border-radius: 5px;
            margin: 20px 0;
        }
        .contact-info {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin: 20px 0;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>AfriScribe Proofreading Service</h1>
        <p style="margin: 0; font-size: 18px;">Thank you for your quote request!</p>
    </div>

    <div class="content">
        <h2>Hello {{ $quoteRequest->name }},</h2>

        <p>Thank you for submitting your quote request to AfriScribe. We've received your request and our team will review it shortly.</p>

        <div class="highlight-box">
            <h3 style="margin-top: 0; color: #007bff;">Request Summary:</h3>
            <ul style="margin-bottom: 0;">
                <li><strong>Service:</strong> {{ $quoteRequest->service_type }}</li>
                <li><strong>Location:</strong> {{ $quoteRequest->location }}</li>
                <li><strong>Product Type:</strong> {{ $quoteRequest->product }}</li>
                @if($quoteRequest->word_count)
                <li><strong>Word Count:</strong> {{ number_format($quoteRequest->word_count) }}</li>
                @endif
                <li><strong>Submitted:</strong> {{ $quoteRequest->created_at->format('F j, Y \a\t g:i A') }}</li>
            </ul>
        </div>

        <h3>What happens next?</h3>
        <ol>
            <li><strong>Review:</strong> Our team will review your document and requirements</li>
            <li><strong>Quote:</strong> We'll send you a detailed quote within 24-48 hours</li>
            <li><strong>Approval:</strong> You can approve the quote and proceed with the service</li>
            <li><strong>Processing:</strong> Once approved, we'll start working on your document</li>
        </ol>

        <div class="contact-info">
            <h4>Need to contact us?</h4>
            <p><strong>Email:</strong> researchfripub@gmail.com</p>
            <p><strong>Response Time:</strong> We typically respond within 24 hours</p>
        </div>

        <p>If you have any questions about your request or need to make changes, please don't hesitate to contact us using the email address above.</p>

        <p>Best regards,<br>The AfriScribe Team</p>
    </div>

    <div class="footer">
        <p><strong>AfriScribe</strong> - Elevating the Quality of Scholarly Writing</p>
        <p>This is an automated confirmation email. Please keep this email for your records.</p>
        <p>Request ID: {{ $quoteRequest->id }}</p>
    </div>
</body>
</html>
