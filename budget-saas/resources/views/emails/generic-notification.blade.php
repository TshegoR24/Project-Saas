<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $notification->title }}</title>
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
        .notification-box {
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
        .type-badge {
            display: inline-block;
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 10px;
        }
        .type-budget_alert { background: #fee2e2; color: #dc2626; }
        .type-payment_reminder { background: #fef3c7; color: #d97706; }
        .type-spending_warning { background: #fed7aa; color: #ea580c; }
        .type-weekly_summary { background: #dbeafe; color: #2563eb; }
        .type-monthly_report { background: #e0e7ff; color: #7c3aed; }
    </style>
</head>
<body>
    <div class="header">
        <h1>📧 Notification</h1>
        <p>You have a new notification</p>
    </div>
    
    <div class="content">
        <h2>Hello {{ $user->name }},</h2>
        
        <div class="notification-box">
            <span class="type-badge type-{{ $notification->type }}">
                {{ ucfirst(str_replace('_', ' ', $notification->type)) }}
            </span>
            <h3>{{ $notification->title }}</h3>
            <p>{{ $notification->message }}</p>
            
            @if($notification->data)
                <div style="margin-top: 15px; padding: 10px; background: #f8f9fa; border-radius: 4px;">
                    <h4>Additional Information:</h4>
                    <ul>
                        @foreach($notification->data as $key => $value)
                            <li><strong>{{ ucfirst(str_replace('_', ' ', $key)) }}:</strong> 
                                @if(is_array($value))
                                    {{ json_encode($value) }}
                                @else
                                    {{ $value }}
                                @endif
                            </li>
                        @endforeach
                    </ul>
                </div>
            @endif
        </div>
        
        <div style="text-align: center;">
            <a href="{{ route('notifications.index') }}" class="btn">View All Notifications</a>
        </div>
        
        <div class="footer">
            <p>This is an automated notification from your Budget SaaS application.</p>
            <p>You can manage your notification preferences in your account settings.</p>
        </div>
    </div>
</body>
</html>
