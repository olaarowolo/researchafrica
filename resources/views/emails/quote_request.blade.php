<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>New Quote Request - AfriScribe</title>
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
            background-color: #f8f9fa;
            padding: 20px;
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
        .field {
            margin-bottom: 15px;
            padding: 10px;
            background-color: #f8f9fa;
            border-radius: 3px;
        }
        .field-label {
            font-weight: bold;
            color: #495057;
            margin-bottom: 5px;
        }
        .field-value {
            color: #212529;
        }
        .footer {
            margin-top: 30px;
            padding: 20px;
            background-color: #f8f9fa;
            border-radius: 5px;
            text-align: center;
            color: #6c757d;
        }
        .status-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 12px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
        }
        .status-pending {
            background-color: #fff3cd;
            color: #856404;
        }
        .status-quoted {
            background-color: #cce5ff;
            color: #004085;
        }
        .status-accepted {
            background-color: #d4edda;
            color: #155724;
        }
        .status-rejected {
            background-color: #f8d7da;
            color: #721c24;
        }
        .status-completed {
            background-color: #d1ecf1;
            color: #0c5460;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>AfriScribe Proofreading Service</h1>
        <p style="margin: 0; color: #6c757d;">New Quote Request Received</p>
    </div>

    <div class="content">
        <h2>Quote Request Details</h2>

        <div class="field">
            <div class="field-label">Client Name:</div>
            <div class="field-value">{{ $quoteRequest->name }}</div>
        </div>

        <div class="field">
            <div class="field-label">Email:</div>
            <div class="field-value">{{ $quoteRequest->email }}</div>
        </div>

        <div class="field">
            <div class="field-label">Service:</div>
            <div class="field-value">{{ $quoteRequest->ra_service }}</div>
        </div>

        <div class="field">
            <div class="field-label">Product Type:</div>
            <div class="field-value">{{ $quoteRequest->product }}</div>
        </div>

        <div class="field">
            <div class="field-label">Location:</div>
            <div class="field-value">{{ $quoteRequest->location }}</div>
        </div>

        <div class="field">
            <div class="field-label">Service Type:</div>
            <div class="field-value">{{ $quoteRequest->service_type }}</div>
        </div>

        @if($quoteRequest->word_count)
        <div class="field">
            <div class="field-label">Word Count:</div>
            <div class="field-value">{{ number_format($quoteRequest->word_count) }}</div>
        </div>
        @endif

        @if($quoteRequest->addons)
        <div class="field">
            <div class="field-label">Add-ons:</div>
            <div class="field-value">{{ implode(', ', json_decode($quoteRequest->addons, true)) }}</div>
        </div>
        @endif

        @if($quoteRequest->referral)
        <div class="field">
            <div class="field-label">Referral Code:</div>
            <div class="field-value">{{ $quoteRequest->referral }}</div>
        </div>
        @endif

        @if($quoteRequest->message)
        <div class="field">
            <div class="field-label">Additional Notes:</div>
            <div class="field-value">{{ $quoteRequest->message }}</div>
        </div>
        @endif

        <div class="field">
            <div class="field-label">Status:</div>
            <div class="field-value">
                <span class="status-badge status-{{ $quoteRequest->status }}">
                    {{ ucfirst($quoteRequest->status) }}
                </span>
            </div>
        </div>

        @if($quoteRequest->estimated_cost)
        <div class="field">
            <div class="field-label">Estimated Cost:</div>
            <div class="field-value">£{{ number_format($quoteRequest->estimated_cost, 2) }}</div>
        </div>
        @endif

        @if($quoteRequest->estimated_turnaround)
        <div class="field">
            <div class="field-label">Estimated Turnaround:</div>
            <div class="field-value">{{ $quoteRequest->estimated_turnaround }}</div>
        </div>
        @endif

        @if($quoteRequest->original_filename)
        <div class="field">
            <div class="field-label">Attached File:</div>
            <div class="field-value">{{ $quoteRequest->original_filename }}</div>
        </div>
        @endif

        <div class="field">
            <div class="field-label">Submitted:</div>
            <div class="field-value">{{ $quoteRequest->created_at->format('F j, Y \a\t g:i A') }}</div>
        </div>
    </div>

    <div class="footer">
        <p>This is an automated message from AfriScribe Proofreading Service.</p>
        <p>Please log in to the admin panel to manage this quote request.</p>
    </div>
</body>
</html>
