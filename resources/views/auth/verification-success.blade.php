<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verification Successful</title>
    <style>
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, Helvetica, Arial, sans-serif, "Apple Color Emoji", "Segoe UI Emoji", "Segoe UI Symbol";
            background-color: #f3f4f6;
            color: #1f2937;
            display: flex;
            justify-content: center;
            align-items: center;
            height: 100vh;
            margin: 0;
            text-align: center;
        }
        .container {
            padding: 2rem;
            background-color: white;
            border-radius: 0.5rem;
            box-shadow: 0 4px 6px -1px rgba(0, 0, 0, 0.1), 0 2px 4px -1px rgba(0, 0, 0, 0.06);
        }
        h1 {
            font-size: 1.5rem;
            font-weight: 600;
        }
        p {
            margin-top: 0.5rem;
            color: #4b5563;
        }
    </style>
</head>
<body>
    <div class="container">
        <h1>✅ Verification Successful!</h1>
        <p>Your email has been verified. This tab will close automatically.</p>
    </div>

    <script>
        // এই স্ক্রিপ্টটি স্বয়ংক্রিয়ভাবে এই ট্যাবটি বন্ধ করে দেবে
        window.setTimeout(function() {
            window.close();
        }, 1500); // ১.৫ সেকেন্ড পর বন্ধ হবে
    </script>
</body>
</html>