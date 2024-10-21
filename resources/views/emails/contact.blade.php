<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reminder: Deliver your clothes to us</title>
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
        .reminder-text {
            /* text-align: center; */
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
        <h2>Hello Admin,</h2>
        <p class="reminder-text ">
            {{$main}}
        </p>
        <!-- Add additional promotional content or links here -->
        {{-- <a href="https://fabricspa.com.ng/cart" class="cta-button">View Your Cart</a> --}}
        <p class="signature">
            from
            {{$email}}
        </p>
    </div>
</body>
</html>
