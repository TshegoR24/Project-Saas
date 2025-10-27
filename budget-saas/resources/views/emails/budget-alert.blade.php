<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Budget Alert</title>
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
        .alert-box {
            background: #fff3cd;
            border: 1px solid #ffeaa7;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
        }
        .budget-info {
            background: white;
            border-radius: 8px;
            padding: 20px;
            margin: 20px 0;
            box-shadow: 0 2px 4px rgba(0,0,0,0.1);
        }
        .progress-bar {
            background: #e9ecef;
            border-radius: 10px;
            height: 20px;
            margin: 10px 0;
            overflow: hidden;
        }
        .progress-fill {
            background: linear-gradient(90deg, #ff6b6b, #ee5a24);
            height: 100%;
            border-radius: 10px;
            transition: width 0.3s ease;
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
    </style>
</head>
<body>
    <div class="header">
        <h1>🚨 Budget Alert</h1>
        <p>You're approaching your budget limit</p>
    </div>
    
    <div class="content">
        <h2>Hello {{ $user->name }},</h2>
        
        <div class="alert-box">
            <h3>⚠️ Budget Alert for "{{ $budget->name }}"</h3>
            <p>You've spent <strong>{{ number_format($percentage, 1) }}%</strong> of your budget for this period.</p>
        </div>
        
        <div class="budget-info">
            <h3>Budget Details</h3>
            <p><strong>Budget Name:</strong> {{ $budget->name }}</p>
            <p><strong>Budget Amount:</strong> ${{ number_format($budget->amount, 2) }}</p>
            <p><strong>Current Spending:</strong> ${{ number_format($currentSpending, 2) }}</p>
            <p><strong>Remaining:</strong> ${{ number_format($budget->amount - $currentSpending, 2) }}</p>
            
            <div class="progress-bar">
                <div class="progress-fill" style="width: {{ min($percentage, 100) }}%"></div>
            </div>
            <p style="text-align: center; margin: 10px 0;">
                <strong>{{ number_format($percentage, 1) }}% Used</strong>
            </p>
        </div>
        
        @if($percentage >= 100)
            <div class="alert-box" style="background: #f8d7da; border-color: #f5c6cb;">
                <h3>🚨 Budget Exceeded!</h3>
                <p>You have exceeded your budget by ${{ number_format($currentSpending - $budget->amount, 2) }}.</p>
            </div>
        @elseif($percentage >= 90)
            <div class="alert-box" style="background: #fff3cd; border-color: #ffeaa7;">
                <h3>⚠️ Critical Alert</h3>
                <p>You're very close to your budget limit. Only ${{ number_format($budget->amount - $currentSpending, 2) }} remaining.</p>
            </div>
        @else
            <div class="alert-box" style="background: #d1ecf1; border-color: #bee5eb;">
                <h3>💡 Budget Warning</h3>
                <p>You're approaching your budget limit. Consider monitoring your spending more closely.</p>
            </div>
        @endif
        
        <div style="text-align: center;">
            <a href="{{ route('budgets.show', $budget) }}" class="btn">View Budget Details</a>
        </div>
        
        <div class="footer">
            <p>This is an automated notification from your Budget SaaS application.</p>
            <p>You can manage your notification preferences in your account settings.</p>
        </div>
    </div>
</body>
</html>
