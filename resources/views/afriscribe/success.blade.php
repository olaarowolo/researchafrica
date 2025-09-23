<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Request Submitted Successfully - AfriScribe</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: 'Segoe UI', sans-serif;
            background: linear-gradient(135deg, #0c1e35, #1a3a5c);
            color: #fff;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        .success-container {
            text-align: center;
            max-width: 500px;
            padding: 3rem 2rem;
            background: rgba(255, 255, 255, 0.1);
            border-radius: 20px;
            backdrop-filter: blur(10px);
            border: 1px solid rgba(255, 255, 255, 0.2);
        }

        .success-icon {
            width: 80px;
            height: 80px;
            background: #f9b233;
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            margin: 0 auto 2rem;
            font-size: 2.5rem;
            color: #0c1e35;
            animation: pulse 2s infinite;
        }

        @keyframes pulse {
            0% { transform: scale(1); }
            50% { transform: scale(1.1); }
            100% { transform: scale(1); }
        }

        .success-container h1 {
            font-size: 2rem;
            margin-bottom: 1rem;
            color: #f9b233;
        }

        .success-container p {
            font-size: 1.1rem;
            margin-bottom: 2rem;
            line-height: 1.6;
        }

        .countdown {
            font-size: 3rem;
            font-weight: bold;
            color: #f9b233;
            margin: 1rem 0;
            text-shadow: 0 0 10px rgba(249, 178, 51, 0.5);
        }

        .loading-spinner {
            width: 40px;
            height: 40px;
            border: 4px solid rgba(249, 178, 51, 0.3);
            border-top: 4px solid #f9b233;
            border-radius: 50%;
            animation: spin 1s linear infinite;
            margin: 1rem auto;
        }

        @keyframes spin {
            0% { transform: rotate(0deg); }
            100% { transform: rotate(360deg); }
        }

        .redirect-message {
            font-size: 0.9rem;
            color: rgba(255, 255, 255, 0.8);
            margin-top: 1rem;
        }

        .home-link {
            display: inline-block;
            background: #f9b233;
            color: #0c1e35;
            padding: 0.75rem 1.5rem;
            border-radius: 25px;
            text-decoration: none;
            font-weight: bold;
            margin-top: 1rem;
            transition: all 0.3s ease;
        }

        .home-link:hover {
            background: #e6a029;
            transform: translateY(-2px);
        }

        @media (max-width: 768px) {
            .success-container {
                margin: 1rem;
                padding: 2rem 1.5rem;
            }

            .success-container h1 {
                font-size: 1.5rem;
            }

            .countdown {
                font-size: 2.5rem;
            }
        }
    </style>
</head>
<body>
    <div class="success-container">
        <div class="success-icon">✓</div>
        <h1>Success!</h1>
        <p>{{ $message }}</p>

        <div class="loading-spinner"></div>
        <div class="countdown" id="countdown">5</div>
        <p class="redirect-message">You are being redirected to the home page...</p>

        <a href="{{ $redirectUrl }}" class="home-link">Go to Home Now</a>
    </div>

    <script>
        let countdown = {{ $countdown }};
        const countdownElement = document.getElementById('countdown');
        const redirectUrl = '{{ $redirectUrl }}';

        function updateCountdown() {
            countdownElement.textContent = countdown;

            if (countdown <= 0) {
                window.location.href = redirectUrl;
                return;
            }

            countdown--;
            setTimeout(updateCountdown, 1000);
        }

        // Start the countdown
        updateCountdown();
    </script>
</body>
</html>
