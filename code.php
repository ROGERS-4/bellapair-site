<?php
$phone = $_GET['phone'] ?? '';
$code = $_GET['code'] ?? '';

if (empty($phone) || empty($code)) {
    header('Location: index.php');
    exit;
}
?>
<!DOCTYPE html>
<html>
<head>
    <title>QUEEN BELLA V1 - Your Code</title>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .container {
            background: white;
            border-radius: 20px;
            padding: 40px;
            max-width: 500px;
            width: 100%;
            box-shadow: 0 20px 60px rgba(0,0,0,0.3);
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
            text-align: center;
        }
        .code-display {
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            font-size: 48px;
            font-weight: bold;
            text-align: center;
            padding: 30px;
            border-radius: 15px;
            margin: 20px 0;
            letter-spacing: 10px;
            font-family: monospace;
            animation: pulse 2s infinite;
        }
        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.02); }
            100% { transform: scale(1); }
        }
        .instructions {
            background: #f5f5f5;
            border-radius: 12px;
            padding: 20px;
            margin: 20px 0;
        }
        .instructions ol {
            margin-left: 20px;
            color: #555;
        }
        .instructions li {
            margin: 10px 0;
        }
        .phone {
            text-align: center;
            color: #667eea;
            font-weight: bold;
            font-size: 18px;
            margin: 10px 0;
        }
        .warning {
            background: #fff3cd;
            color: #856404;
            padding: 15px;
            border-radius: 10px;
            margin: 20px 0;
            text-align: center;
        }
        .button {
            display: inline-block;
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            text-decoration: none;
            border-radius: 12px;
            font-weight: 600;
            text-align: center;
            margin-top: 20px;
        }
        .footer {
            margin-top: 25px;
            text-align: center;
            color: #999;
            font-size: 13px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 QUEEN BELLA V1</h1>
        
        <div class="phone">📱 +<?php echo $phone; ?></div>
        
        <div class="code-display"><?php echo $code; ?></div>
        
        <div class="instructions">
            <h3>📱 Steps to link:</h3>
            <ol>
                <li>Open WhatsApp on your phone</li>
                <li>Go to <strong>Settings > Linked Devices</strong></li>
                <li>Tap <strong>Link a Device</strong></li>
                <li>Enter code: <strong><?php echo $code; ?></strong></li>
                <li>Wait for confirmation</li>
            </ol>
        </div>
        
        <div class="warning">
            ⚠️ Code expires in 5 minutes
        </div>
        
        <a href="check-status.php?code=<?php echo $code; ?>" class="button" id="checkBtn">✅ I've Entered the Code</a>
        
        <div class="footer">
            Powered by <strong>Rodgers</strong>
        </div>
    </div>

    <script>
        // Auto redirect after 5 minutes
        setTimeout(() => {
            window.location.href = 'index.php';
        }, 5 * 60 * 1000);
    </script>
</body>
</html>
