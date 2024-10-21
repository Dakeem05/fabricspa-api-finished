<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Happy Birthday!</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Nunito:ital,wght@0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;0,1000&family=Original+Surfer&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Nunito', sans-serif;
            background-color: #f4f4f4;
            padding: 20px;
        }
        .container {
            max-width: 600px;
            margin: 0 auto;
            background-color: #ffffff;
            padding: 20px;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }
        .header {
            text-align: center;
            margin-bottom: 20px;
        }
        .brand {
            color: #000000;
            font-family: 'Surfer', cursive;
            font-weight: 400;
        }
        .highlight {
            color: #41aef1;
        }
        .birthday-wish {
            text-align: center;
            font-size: 18px;
            margin-bottom: 20px;
        }
        .cta-button {
            display: block;
            width: 100%;
            max-width: 200px;
            margin: 0 auto;
            padding: 10px;
            background-color: #41aef1;
            color: #ffffff;
            text-decoration: none;
            text-align: center;
            border-radius: 4px;
        }
        .signature {
            text-align: right;
            margin-top: 20px;
            font-style: italic;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="header">
            <h1 class="brand">
                Fabric<span class="highlight">SPA</span>
            </h1>
        </div>
        <h2>Happy Birthday, {{ $user->last_name }}!</h2>
        <p class="birthday-wish">
            Wishing you a day filled with joy, laughter, and unforgettable moments. May this year bring you great success and happiness.
        </p>
        <!-- Add additional birthday wishes or promotional content here -->
        <a href="https://fabricspa.com.ng" class="cta-button">Shop Birthday Specials</a>
        <p class="signature">
            Best Wishes,
            <br>
            The FabricSPA Team
        </p>
    </div>
</body>
</html>

