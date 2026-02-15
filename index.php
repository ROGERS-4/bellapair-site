<!DOCTYPE html>
<html>
<head>
    <title>QUEEN BELLA V1 - Pairing</title>
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
            animation: slideUp 0.5s ease;
        }
        @keyframes slideUp {
            from {
                opacity: 0;
                transform: translateY(30px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
        h1 {
            color: #333;
            margin-bottom: 10px;
            font-size: 28px;
            text-align: center;
        }
        .subtitle {
            color: #666;
            text-align: center;
            margin-bottom: 30px;
            font-size: 14px;
        }
        .bot-name {
            background: linear-gradient(135deg, #667eea, #764ba2);
            -webkit-background-clip: text;
            -webkit-text-fill-color: transparent;
            font-weight: bold;
        }
        .form-group {
            margin-bottom: 25px;
        }
        label {
            display: block;
            margin-bottom: 8px;
            color: #555;
            font-weight: 500;
            font-size: 14px;
        }
        input {
            width: 100%;
            padding: 15px;
            border: 2px solid #e0e0e0;
            border-radius: 12px;
            font-size: 16px;
            transition: all 0.3s;
            outline: none;
        }
        input:focus {
            border-color: #667eea;
            box-shadow: 0 0 0 3px rgba(102,126,234,0.1);
        }
        button {
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #667eea, #764ba2);
            color: white;
            border: none;
            border-radius: 12px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: transform 0.2s, box-shadow 0.2s;
        }
        button:hover {
            transform: translateY(-2px);
            box-shadow: 0 10px 30px rgba(102,126,234,0.4);
        }
        .info-box {
            background: #f5f5f5;
            border-radius: 12px;
            padding: 15px;
            margin: 20px 0;
            font-size: 14px;
            color: #666;
        }
        .footer {
            margin-top: 25px;
            text-align: center;
            color: #999;
            font-size: 13px;
        }
        .alert {
            padding: 12px;
            border-radius: 8px;
            margin-bottom: 20px;
            display: none;
        }
        .alert.error {
            background: #fee;
            color: #c33;
            border: 1px solid #fcc;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>🔐 <span class="bot-name">QUEEN BELLA V1</span></h1>
        <div class="subtitle">WhatsApp Bot Pairing System</div>
        
        <div id="alert" class="alert"></div>
        
        <form id="pairForm" onsubmit="generateCode(event)">
            <div class="form-group">
                <label>📱 Your WhatsApp Number</label>
                <input type="tel" id="phone" placeholder="254755660053" required>
                <small style="color: #999; display: block; margin-top: 5px;">Include country code (e.g., 254 for Kenya)</small>
            </div>
            
            <div class="info-box">
                <strong>⚡ How it works:</strong><br>
                1. Enter your number<br>
                2. Get 8-digit code<br>
                3. Open WhatsApp > Linked Devices<br>
                4. Enter the code<br>
                5. Code will be sent to your WhatsApp
            </div>
            
            <button type="submit" id="submitBtn">Generate 8-Digit Code</button>
        </form>
        
        <div class="footer">
            Powered by <strong>Rodgers</strong>
        </div>
    </div>

    <script>
        async function generateCode(e) {
            e.preventDefault();
            
            const phone = document.getElementById('phone').value.replace(/\D/g, '');
            const submitBtn = document.getElementById('submitBtn');
            const alert = document.getElementById('alert');
            
            if (!phone || phone.length < 10) {
                showAlert('Please enter a valid phone number', 'error');
                return;
            }
            
            submitBtn.disabled = true;
            submitBtn.textContent = '⏳ Generating...';
            
            try {
                const response = await fetch('generate.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded',
                    },
                    body: 'phone=' + encodeURIComponent(phone)
                });
                
                const text = await response.text();
                
                if (text.includes('SUCCESS')) {
                    const code = text.split(':')[1];
                    window.location.href = 'code.php?phone=' + phone + '&code=' + code;
                } else {
                    showAlert('Failed to generate code. Try again.', 'error');
                    submitBtn.disabled = false;
                    submitBtn.textContent = 'Generate 8-Digit Code';
                }
            } catch (error) {
                showAlert('Network error. Please try again.', 'error');
                submitBtn.disabled = false;
                submitBtn.textContent = 'Generate 8-Digit Code';
            }
        }
        
        function showAlert(message, type) {
            const alert = document.getElementById('alert');
            alert.className = 'alert ' + type;
            alert.textContent = message;
            alert.style.display = 'block';
            
            setTimeout(() => {
                alert.style.display = 'none';
            }, 5000);
        }
    </script>
</body>
</html>
