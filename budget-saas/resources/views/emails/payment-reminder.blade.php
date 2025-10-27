<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payment Reminder</title>
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
            padding: 30px;
            text-align: center;
            border-radius: 10px 10px 0 0;
        }
        .content {
            background: #f8f9fa;
            padding: 30px;
            border-radius: 0 0 10px 10px;
        }
        .reminder-box {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .subscription-info {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .btn {
            display: inline-block;
            background: #667eea;
            color: white;
            padding: 12px 24px;
            text-decoration: none;
            border-radius: 6px;
            margin: 20px 0;
        }
        .footer {
            text-align: center;
            margin-top: 30px;
            color: #666;
            font-size: 14px;
        }
        .urgency-high {
            background: #f8d7da;
            border-color: #f5c6cb;
        }
        .urgency-medium {
            background: #fff3cd;
            border-color: #ffeaa7;
        }
        .urgency-low {
            background: #d1ecf1;
            border-color: #bee5eb;
        }
    </style>
</head>
<body>
    <div class="header">
        <h1>💳 Payment Reminder</h1>
        <p>Upcoming subscription payment</p>
    </div>
    
    <div class="content">
        <h2>Hello {{ $user->name }},</h2>
        
        <div class="reminder-box {{ $daysUntilDue <= 1 ? 'urgency-high' : ($daysUntilDue <= 3 ? 'urgency-medium' : 'urgency-low') }}">
            <h3>
                @if($daysUntilDue <= 1)
                    🚨 Payment Due Today or Tomorrow
                @elseif($daysUntilDue <= 3)
                    ⚠️ Payment Due in {{ $daysUntilDue }} Days
                @else
                    💡 Payment Reminder - {{ $daysUntilDue }} Days
                @endif
            </h3>
            <p>Your subscription payment is due soon.</p>
        </div>
        
        <div class="subscription-info">
            <h3>Subscription Details</h3>
            <p><strong>Service:</strong> {{ $subscription->name }}</p>
            <p><strong>Amount:</strong> ${{ number_format($subscription->amount, 2) }}</p>
            <p><strong>Billing Cycle:</strong> {{ ucfirst($subscription->billing_cycle) }}</p>
            <p><strong>Next Due Date:</strong> {{ $subscription->next_due_date->format('F j, Y') }}</p>
            <p><strong>Days Until Due:</strong> {{ $daysUntilDue }} day{{ $daysUntilDue !== 1 ? 's' : '' }}</p>
        </div>
        
        @if($daysUntilDue <= 1)
            <div class="reminder-box urgency-high">
                <h3>🚨 Urgent: Payment Due Soon!</h3>
                <p>Your subscription payment is due today or tomorrow. Please ensure payment is processed to avoid service interruption.</p>
            </div>
        @elseif($daysUntilDue <= 3)
            <div class="reminder-box urgency-medium">
                <h3>⚠️ Payment Due in {{ $daysUntilDue }} Days</h3>
                <p>Your subscription payment will be due in {{ $daysUntilDue }} days. Please prepare for the upcoming payment.</p>
            </div>
        @else
            <div class="reminder-box urgency-low">
                <h3>💡 Payment Reminder</h3>
                <p>This is a friendly reminder that your subscription payment will be due in {{ $daysUntilDue }} days.</p>
            </div>
        @endif
        
        <div style="text-align: center;">
            <a href="{{ route('subscriptions.show', $subscription) }}" class="btn">View Subscription Details</a>
        </div>
        
        <div class="footer">
            <p>This is an automated payment reminder from your Budget SaaS application.</p>
            <p>You can manage your notification preferences in your account settings.</p>
        </div>
    </div>
</body>
</html>
