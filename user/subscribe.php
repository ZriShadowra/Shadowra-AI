<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Subscribe - Shadowra.AI</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Oxygen, Ubuntu, Cantarell, sans-serif;
            background: #0a0a0a;
            color: #fff;
        }

        .header {
            background: #111;
            padding: 15px 30px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            border-bottom: 1px solid #222;
        }

        .logo {
            font-size: 20px;
            font-weight: bold;
            color: #fff;
            text-decoration: none;
        }

        .logo span {
            color: #8b5cf6;
        }

        .container {
            max-width: 1400px;
            margin: 0 auto;
            padding: 60px 40px;
        }

        .page-title {
            text-align: center;
            font-size: 48px;
            margin-bottom: 15px;
        }

        .page-subtitle {
            text-align: center;
            font-size: 18px;
            color: #999;
            margin-bottom: 40px;
        }

        .billing-toggle {
            display: flex;
            justify-content: center;
            gap: 10px;
            margin-bottom: 50px;
        }

        .toggle-btn {
            padding: 12px 24px;
            background: #1a1a1a;
            border: 1px solid #333;
            border-radius: 8px;
            color: #fff;
            cursor: pointer;
            font-size: 16px;
            transition: all 0.3s;
        }

        .toggle-btn.active {
            background: #8b5cf6;
            border-color: #8b5cf6;
        }

        .save-badge {
            background: #16a34a;
            padding: 3px 8px;
            border-radius: 4px;
            font-size: 12px;
            margin-left: 5px;
        }

        .plans {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(320px, 1fr));
            gap: 30px;
            margin-bottom: 40px;
        }

        .plan-card {
            background: #1a1a1a;
            border: 2px solid #333;
            border-radius: 16px;
            padding: 30px;
            position: relative;
            transition: transform 0.3s, border-color 0.3s;
        }

        .plan-card:hover {
            transform: translateY(-5px);
            border-color: #8b5cf6;
        }

        .plan-card.popular {
            border-color: #8b5cf6;
            background: linear-gradient(135deg, #1a1a1a, #1a1a2e);
        }

        .popular-badge {
            position: absolute;
            top: -15px;
            left: 50%;
            transform: translateX(-50%);
            background: linear-gradient(135deg, #8b5cf6, #6d28d9);
            padding: 6px 20px;
            border-radius: 20px;
            font-size: 13px;
            font-weight: bold;
        }

        .plan-name {
            font-size: 24px;
            font-weight: bold;
            margin-bottom: 15px;
        }

        .plan-price {
            font-size: 42px;
            font-weight: bold;
            margin-bottom: 5px;
        }

        .plan-price span {
            font-size: 18px;
            color: #999;
        }

        .plan-period {
            color: #999;
            font-size: 14px;
            margin-bottom: 25px;
        }

        .subscribe-btn {
            width: 100%;
            padding: 14px;
            background: linear-gradient(135deg, #8b5cf6, #6d28d9);
            border: none;
            border-radius: 10px;
            color: #fff;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: transform 0.2s;
            margin-bottom: 25px;
        }

        .subscribe-btn:hover {
            transform: scale(1.02);
        }

        .plan-card.popular .subscribe-btn {
            background: linear-gradient(135deg, #16a34a, #15803d);
        }

        .features-title {
            font-weight: 600;
            margin-bottom: 15px;
            font-size: 14px;
            color: #ddd;
        }

        .feature {
            display: flex;
            align-items: flex-start;
            gap: 10px;
            margin-bottom: 12px;
            font-size: 14px;
            line-height: 1.5;
        }

        .feature::before {
            content: "✓";
            color: #16a34a;
            font-weight: bold;
            flex-shrink: 0;
        }

        .learn-more {
            text-align: center;
            color: #8b5cf6;
            font-size: 14px;
            text-decoration: none;
            display: inline-block;
            margin-top: 10px;
        }

        .learn-more:hover {
            text-decoration: underline;
        }

        .footer-note {
            text-align: center;
            color: #666;
            font-size: 14px;
            margin-top: 40px;
            line-height: 1.6;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="index.php" class="logo">Shadowra<span>.AI</span></a>
        <div>
            <?php if(isset($_SESSION['user_id'])): ?>
                <a href="user.php" style="color: #fff; text-decoration: none;">👤 Profile</a>
            <?php else: ?>
                <a href="login.php" style="color: #fff; text-decoration: none;">Sign In</a>
            <?php endif; ?>
        </div>
    </div>

    <div class="container">
        <h1 class="page-title">Pick the plan that's right for you</h1>
        <p class="page-subtitle">Upgrade your experience and unlock your imagination with premium features</p>

        <div class="billing-toggle">
            <button class="toggle-btn active">Pay Annually <span class="save-badge">Save 17%</span></button>
            <button class="toggle-btn">Pay Monthly</button>
        </div>

        <div class="plans">
            <!-- Get a Taste -->
            <div class="plan-card">
                <div class="plan-name">Get a Taste</div>
                <div class="plan-price">€ 4.95<span>/month</span></div>
                <div class="plan-period">Billed annually at €59.40/year</div>
                <button class="subscribe-btn">Subscribe</button>
                
                <div class="features-title">Features Included:</div>
                <div class="feature">Plus Benefits From Free Tier</div>
                <div class="feature">No Ads</div>
                <div class="feature">Skip The Waiting Lines</div>
                <div class="feature">Memory Manager</div>
                <div class="feature">User Personas - Up To 10</div>
                
                <a href="#" class="learn-more">Learn More about Get a Taste Tier</a>
            </div>

            <!-- True Supporter (Popular) -->
            <div class="plan-card popular">
                <div class="popular-badge">Most Popular</div>
                <div class="plan-name">True Supporter</div>
                <div class="plan-price">€ 13.95<span>/month</span></div>
                <div class="plan-period">Billed annually at €167.40/year</div>
                <button class="subscribe-btn">Subscribe</button>
                
                <div class="features-title">Features Included:</div>
                <div class="feature">Plus Benefits From Get A Taste Tier</div>
                <div class="feature">8K Context (Memory)</div>
                <div class="feature">Semantic Memory 2.0</div>
                <div class="feature">Longer Responses</div>
                <div class="feature">Conversation Images</div>
                <div class="feature">Access To Additional Models</div>
                <div class="feature">User Personas - Up To 50</div>
                
                <a href="#" class="learn-more">Learn More about True Supporter Tier</a>
            </div>

            <!-- I'm All In -->
            <div class="plan-card">
                <div class="plan-name">I'm All In</div>
                <div class="plan-price">€ 23.95<span>/month</span></div>
                <div class="plan-period">Billed annually at €287.40/year</div>
                <button class="subscribe-btn">Subscribe</button>
                
                <div class="features-title">Features Included:</div>
                <div class="feature">Plus Benefits From True Supporter Tier</div>
                <div class="feature">16K Context (Memory)</div>
                <div class="feature">Priority Generation Queue</div>
                <div class="feature">Access To SpicyXL And Advanced Models</div>
                <div class="feature">Conversation Images On Private Chatbots</div>
                <div class="feature">User Personas - Up To 100</div>
                <div class="feature">Text-To-Speech (TTS) For AI Responses</div>
                
                <a href="#" class="learn-more">Learn More about I'm All In Tier</a>
            </div>
        </div>

        <div class="footer-note">
            You can also subscribe to a Premium plan through platforms like SubscribeStar or Boosty, offering flexible<br>
            options to suit your needs.
        </div>
    </div>
</body>
</html>
