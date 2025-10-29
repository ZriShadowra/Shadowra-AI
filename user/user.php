<?php
session_start();

if (!isset($_SESSION['user_id'])) {
    header('Location: login.php');
    exit;
}

// Database connection
$host = 'localhost';
$dbname = 'spicychat';
$username = 'root';
$password = '';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch(PDOException $e) {
    die("Connection failed: " . $e->getMessage());
}

$message = '';

// Fetch user data
$stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
$stmt->execute([$_SESSION['user_id']]);
$user = $stmt->fetch();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    if (isset($_POST['update_profile'])) {
        $display_name = trim($_POST['display_name']);
        $bio = trim($_POST['bio']);
        $nsfw_enabled = isset($_POST['nsfw_enabled']) ? 1 : 0;
        
        $stmt = $pdo->prepare("UPDATE users SET display_name = ?, bio = ?, nsfw_enabled = ? WHERE id = ?");
        if ($stmt->execute([$display_name, $bio, $nsfw_enabled, $_SESSION['user_id']])) {
            $message = 'Profile updated successfully!';
            // Refresh user data
            $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
            $stmt->execute([$_SESSION['user_id']]);
            $user = $stmt->fetch();
        }
    } elseif (isset($_POST['logout'])) {
        session_destroy();
        header('Location: index.php');
        exit;
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Profile Settings - Shadowra.AI</title>
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
            color: #8b00f6;
        }

        .container {
            display: flex;
            height: calc(100vh - 60px);
        }

        .sidebar {
            width: 250px;
            background: #111;
            border-right: 1px solid #222;
            padding: 20px;
        }

        .menu-item {
            padding: 12px 15px;
            margin-bottom: 5px;
            border-radius: 8px;
            cursor: pointer;
            transition: background 0.2s;
            display: flex;
            align-items: center;
            gap: 10px;
            color: #ddd;
            text-decoration: none;
        }

        .menu-item:hover {
            background: #1a1a1a;
        }

        .menu-item.active {
            background: #8b00f6;
            color: #fff;
        }

        .main-content {
            flex: 1;
            overflow-y: auto;
            padding: 40px;
        }

        .settings-section {
            max-width: 800px;
            margin: 0 auto;
        }

        h1 {
            font-size: 32px;
            margin-bottom: 30px;
        }

        .form-group {
            margin-bottom: 30px;
        }

        label {
            display: block;
            margin-bottom: 8px;
            color: #ddd;
            font-size: 14px;
            font-weight: 600;
        }

        .label-subtitle {
            font-weight: normal;
            color: #999;
            font-size: 13px;
            margin-top: 3px;
        }

        input[type="text"],
        input[type="email"],
        textarea {
            width: 100%;
            padding: 12px 15px;
            background: #1a1a1a;
            border: 1px solid #333;
            border-radius: 8px;
            color: #fff;
            font-size: 15px;
            font-family: inherit;
        }

        input:focus,
        textarea:focus {
            outline: none;
            border-color: #8b00f6;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .avatar-section {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .avatar {
            width: 120px;
            height: 120px;
            border-radius: 12px;
            background: linear-gradient(135deg, #8b5cf6, #6d28d9);
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 48px;
        }

        .avatar-buttons {
            display: flex;
            gap: 10px;
        }

        .btn {
            padding: 10px 20px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            font-size: 14px;
            font-weight: 600;
            transition: transform 0.2s;
        }

        .btn:hover {
            transform: translateY(-2px);
        }

        .btn-primary {
            background: linear-gradient(135deg, #8b5cf6, #6d28d9);
            color: #fff;
        }

        .btn-secondary {
            background: #2a2a2a;
            color: #fff;
        }

        .btn-danger {
            background: #dc2626;
            color: #fff;
        }

        .plan-badge {
            display: inline-flex;
            align-items: center;
            gap: 8px;
            padding: 8px 16px;
            background: #2a2a2a;
            border-radius: 8px;
            font-size: 14px;
        }

        .toggle-switch {
            position: relative;
            display: inline-block;
            width: 50px;
            height: 24px;
        }

        .toggle-switch input {
            opacity: 0;
            width: 0;
            height: 0;
        }

        .slider {
            position: absolute;
            cursor: pointer;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background-color: #2a2a2a;
            transition: .4s;
            border-radius: 24px;
        }

        .slider:before {
            position: absolute;
            content: "";
            height: 18px;
            width: 18px;
            left: 3px;
            bottom: 3px;
            background-color: white;
            transition: .4s;
            border-radius: 50%;
        }

        input:checked + .slider {
            background-color: #8b5cf6;
        }

        input:checked + .slider:before {
            transform: translateX(26px);
        }

        .message {
            padding: 15px;
            background: #16a34a;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .actions {
            display: flex;
            gap: 15px;
            margin-top: 30px;
            padding-top: 30px;
            border-top: 1px solid #333;
        }
    </style>
</head>
<body>
    <div class="header">
        <a href="index.php" class="logo">Shadowra<span>.AI</span></a>
    </div>

    <div class="container">
        <div class="sidebar">
            <a href="index.php" class="menu-item">🏠 Home</a>
            <a href="#" class="menu-item">💬 Chats</a>
            <a href="#" class="menu-item">🎭 My Personas</a>
            <a href="#" class="menu-item active">⚙️ Settings</a>
            <a href="subscribe.php" class="menu-item">👑 Subscribe</a>
            <form method="POST" style="margin-top: 20px;">
                <button type="submit" name="logout" class="menu-item" style="width: 100%; border: none; background: none; font-family: inherit;">
                    🚪 Sign Out
                </button>
            </form>
        </div>

        <div class="main-content">
            <div class="settings-section">
                <h1>Profile Settings</h1>

                <?php if($message): ?>
                    <div class="message"><?php echo htmlspecialchars($message); ?></div>
                <?php endif; ?>

                <form method="POST">
                    <div class="form-group">
                        <label>Username</label>
                        <input type="text" value="<?php echo htmlspecialchars($user['username']); ?>" disabled>
                        <small class="label-subtitle">You can change this at any time</small>
                    </div>

                    <div class="form-group">
                        <label>Display Name</label>
                        <input type="text" name="display_name" value="<?php echo htmlspecialchars($user['display_name'] ?? $user['username']); ?>">
                        <small class="label-subtitle">The name you'd use for chatting</small>
                    </div>

                    <div class="form-group">
                        <label>Current Plan</label>
                        <div class="plan-badge">
                            <?php if($user['subscription_tier'] == 'premium'): ?>
                                👑 Premium Member
                            <?php else: ?>
                                🆓 Free Tier
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Avatar
                            <div class="label-subtitle">You can either create an image from text or upload an image</div>
                        </label>
                        <div class="avatar-section">
                            <div class="avatar">
                                🐉
                            </div>
                            <div class="avatar-buttons">
                                <button type="button" class="btn btn-primary">Generate Avatar</button>
                                <button type="button" class="btn btn-secondary">Choose File</button>
                            </div>
                        </div>
                    </div>

                    <div class="form-group">
                        <label>Bio / Highlights
                            <div class="label-subtitle">(Optional) Used only in your conversations to help the AI with context. Keep it short (1-2 sentences).</div>
                        </label>
                        <textarea name="bio" placeholder="A petite and slim 22 years old girl, with lavender-long hair, silver-gray eyes..."><?php echo htmlspecialchars($user['bio'] ?? ''); ?></textarea>
                    </div>

                    <div class="form-group">
                        <label style="display: flex; align-items: center; justify-content: space-between;">
                            <span>Display chatbots with explicit images or languages (NSFW)</span>
                            <label class="toggle-switch">
                                <input type="checkbox" name="nsfw_enabled" <?php echo $user['nsfw_enabled'] ? 'checked' : ''; ?>>
                                <span class="slider"></span>
                            </label>
                        </label>
                    </div>

                    <div class="actions">
                        <button type="submit" name="update_profile" class="btn btn-primary">💾 Update</button>
                        <a href="index.php" class="btn btn-secondary">Cancel</a>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>
