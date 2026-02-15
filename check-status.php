<?php
$code = $_GET['code'] ?? '';

if (empty($code)) {
    header('Location: index.php');
    exit;
}

// Check if code file exists
$file = "codes/$code.json";

if (!file_exists($file)) {
    // Code expired
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Code Expired</title>
        <meta http-equiv="refresh" content="3;url=index.php">
        <style>
            body {
                font-family: Arial, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                color: white;
                text-align: center;
            }
            .message {
                background: rgba(255,255,255,0.1);
                padding: 30px;
                border-radius: 15px;
            }
        </style>
    </head>
    <body>
        <div class="message">
            <h2>❌ Code Expired</h2>
            <p>Redirecting to home page...</p>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// Read code data
$data = json_decode(file_get_contents($file), true);

if ($data['used']) {
    // Code already used - show success
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Success!</title>
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
                text-align: center;
            }
            .success-icon {
                font-size: 80px;
                margin-bottom: 20px;
            }
            h1 {
                color: #333;
                margin-bottom: 20px;
            }
            .message {
                background: #d4edda;
                color: #155724;
                padding: 20px;
                border-radius: 10px;
                margin: 20px 0;
            }
            .button {
                display: inline-block;
                padding: 15px 30px;
                background: linear-gradient(135deg, #667eea, #764ba2);
                color: white;
                text-decoration: none;
                border-radius: 10px;
                font-weight: 600;
                margin: 10px;
            }
            .footer {
                margin-top: 25px;
                color: #999;
            }
        </style>
    </head>
    <body>
        <div class="container">
            <div class="success-icon">✅</div>
            <h1>Pairing Successful!</h1>
            
            <div class="message">
                <strong>Your WhatsApp has been linked successfully!</strong>
            </div>
            
            <div>
                <p>Check your WhatsApp for the session file.</p>
                <p>Phone: +<?php echo $data['phone']; ?></p>
            </div>
            
            <a href="index.php" class="button">Pair Another Number</a>
            
            <div class="footer">
                Powered by <strong>Rodgers</strong>
            </div>
        </div>
    </body>
    </html>
    <?php
} else {
    // Code not used yet - show waiting
    ?>
    <!DOCTYPE html>
    <html>
    <head>
        <title>Waiting for Pairing</title>
        <meta http-equiv="refresh" content="5">
        <style>
            body {
                font-family: Arial, sans-serif;
                background: linear-gradient(135deg, #667eea 0%, #764ba2 100%);
                height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                color: white;
                text-align: center;
            }
            .message {
                background: rgba(255,255,255,0.1);
                padding: 30px;
                border-radius: 15px;
            }
            .spinner {
                border: 4px solid rgba(255,255,255,0.3);
                border-top: 4px solid white;
                border-radius: 50%;
                width: 40px;
                height: 40px;
                animation: spin 1s linear infinite;
                margin: 20px auto;
            }
            @keyframes spin {
                0% { transform: rotate(0deg); }
                100% { transform: rotate(360deg); }
            }
        </style>
    </head>
    <body>
        <div class="message">
            <div class="spinner"></div>
            <h2>Waiting for you to enter the code...</h2>
            <p>Code: <strong><?php echo $code; ?></strong></p>
            <p>Open WhatsApp and enter this code</p>
        </div>
    </body>
    </html>
    <?php
}
?>
