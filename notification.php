<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>BINARY PAY - Notification</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        body {
            background: linear-gradient(135deg, #0c0c1e 0%, #1a1a2e 50%, #16213e 100%);
            color: #e0e0e0;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            position: relative;
            overflow: hidden;
        }

        /* AI-inspired background animation */
        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: 
                radial-gradient(circle at 20% 80%, rgba(0, 188, 212, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 80% 20%, rgba(0, 188, 212, 0.1) 0%, transparent 50%),
                radial-gradient(circle at 40% 40%, rgba(0, 188, 212, 0.05) 0%, transparent 50%);
            animation: pulse 4s ease-in-out infinite alternate;
        }

        @keyframes pulse {
            0% { opacity: 0.5; }
            100% { opacity: 1; }
        }

        .notification-container {
            background: rgba(42, 42, 74, 0.95);
            backdrop-filter: blur(20px);
            border: 1px solid rgba(0, 188, 212, 0.3);
            border-radius: 20px;
            padding: 40px;
            text-align: center;
            max-width: 500px;
            width: 90%;
            box-shadow: 
                0 20px 40px rgba(0, 0, 0, 0.3),
                0 0 20px rgba(0, 188, 212, 0.1),
                inset 0 1px 0 rgba(255, 255, 255, 0.1);
            position: relative;
            z-index: 1;
            animation: slideIn 0.5s ease-out;
        }

        @keyframes slideIn {
            from {
                opacity: 0;
                transform: translateY(-30px) scale(0.9);
            }
            to {
                opacity: 1;
                transform: translateY(0) scale(1);
            }
        }

        .icon {
            font-size: 60px;
            margin-bottom: 20px;
            animation: iconPulse 2s ease-in-out infinite;
        }

        .icon.success {
            color: #00e676;
            text-shadow: 0 0 20px rgba(0, 230, 118, 0.5);
        }

        .icon.error {
            color: #ff5252;
            text-shadow: 0 0 20px rgba(255, 82, 82, 0.5);
        }

        .icon.info {
            color: #00bcd4;
            text-shadow: 0 0 20px rgba(0, 188, 212, 0.5);
        }

        @keyframes iconPulse {
            0%, 100% { transform: scale(1); }
            50% { transform: scale(1.1); }
        }

        h1 {
            color: #00bcd4;
            font-size: 28px;
            margin-bottom: 15px;
            text-shadow: 0 0 10px rgba(0, 188, 212, 0.3);
        }

        .message {
            font-size: 18px;
            line-height: 1.6;
            margin-bottom: 30px;
            color: #e0e0e0;
        }

        .close-btn {
            background: linear-gradient(45deg, #00bcd4, #0097a7);
            color: white;
            border: none;
            padding: 12px 30px;
            border-radius: 25px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            box-shadow: 0 4px 15px rgba(0, 188, 212, 0.3);
        }

        .close-btn:hover {
            background: linear-gradient(45deg, #0097a7, #00838f);
            transform: translateY(-2px);
            box-shadow: 0 6px 20px rgba(0, 188, 212, 0.4);
        }

        .auto-close {
            margin-top: 20px;
            font-size: 14px;
            color: #9e9e9e;
        }

        /* Glowing border animation */
        .notification-container::before {
            content: '';
            position: absolute;
            top: -2px;
            left: -2px;
            right: -2px;
            bottom: -2px;
            background: linear-gradient(45deg, #00bcd4, #0097a7, #00bcd4);
            border-radius: 22px;
            z-index: -1;
            animation: borderGlow 3s linear infinite;
            opacity: 0.5;
        }

        @keyframes borderGlow {
            0% { background-position: 0% 50%; }
            50% { background-position: 100% 50%; }
            100% { background-position: 0% 50%; }
        }
    </style>
</head>
<body>
    <div class="notification-container">
        <?php
        $message = isset($_GET['message']) ? htmlspecialchars($_GET['message']) : 'Notification';
        $type = isset($_GET['type']) ? $_GET['type'] : 'info';
        
        $icon = '💡';
        $iconClass = 'info';
        
        if ($type === 'success') {
            $icon = '✅';
            $iconClass = 'success';
        } elseif ($type === 'error') {
            $icon = '❌';
            $iconClass = 'error';
        } elseif ($type === 'initiated') {
            $icon = '📱';
            $iconClass = 'info';
        }
        ?>
        
        <div class="icon <?php echo $iconClass; ?>"><?php echo $icon; ?></div>
        <h1>BINARY PAY</h1>
        <div class="message"><?php echo $message; ?></div>
        <button class="close-btn" onclick="closeTab()">OK</button>
        <div class="auto-close">This tab will close automatically in <span id="countdown">10</span> seconds</div>
    </div>

    <script>
        let countdown = 10;
        const countdownElement = document.getElementById('countdown');
        
        const timer = setInterval(() => {
            countdown--;
            countdownElement.textContent = countdown;
            
            if (countdown <= 0) {
                clearInterval(timer);
                closeTab();
            }
        }, 1000);
        
        function closeTab() {
            window.close();
        }
        
        // Close tab when pressing Escape key
        document.addEventListener('keydown', function(event) {
            if (event.key === 'Escape') {
                closeTab();
            }
        });
    </script>
</body>
</html>