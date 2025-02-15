<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome to {{ config('app.name') }}!</title>
    <script>
        document.getElementById("currentYear").textContent = new Date().getFullYear();
    </script>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            margin: 0;
            padding: 0;
        }
        .container {
            max-width: 600px;
            margin: 20px auto;
            background: #ffffff;
            padding: 20px;
            border-radius: 10px;
            box-shadow: 0 0 10px rgba(0, 0, 0, 0.1);
        }
        .logo {
            text-align: center;
        }
        .logo img {
            max-width: 150px;
        }
        h1 {
            color: #333;
            text-align: center;
        }
        p {
            color: #555;
            font-size: 16px;
            line-height: 1.6;
            text-align: center;
        }
        .btn {
            display: block;
            width: 200px;
            margin: 20px auto;
            padding: 12px;
            text-align: center;
            background-color: #007bff;
            color: #ffffff;
            text-decoration: none;
            font-size: 16px;
            border-radius: 5px;
        }
        .btn:hover {
            background-color: #0056b3;
        }
        .footer {
            text-align: center;
            margin-top: 20px;
            font-size: 14px;
            color: #888;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="logo">
            <img src="https://avatar.iran.liara.run/public" alt="App Logo">
        </div>
        <h1>Welcome to {{ config('app.name') }}!</h1>
        <p>Hi {{ $user->email }},</p>
        <p>We're thrilled to have you on board! Get ready to explore the amazing features of {{ config('app.name') }}.</p>
        <p>Click the button below to get started:</p>
        <a href="https://forestgreen-donkey-261620.hostingersite.com" class="btn">Get Started</a>
        <p>If you have any questions, feel free to reach out to us at <a href="mailto:resepsiofficial@gmail.com">resepsiofficial@gmail.com</a>.</p>
        <p class="footer">Happy Exploring! 🚀<br> &copy; <span id="currentYear"></span> {{ config('app.name') }}. All rights reserved.</p>
    </div>
</body>
