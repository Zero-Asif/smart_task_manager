<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Task In Progress</title>
    <style>
        body { font-family: sans-serif; background-color: #1f2937; color: #e5e7eb; display: flex; justify-content: center; align-items: center; height: 100vh; margin: 0; text-align: center; }
        .container { padding: 2rem; background-color: #374151; border-radius: 0.5rem; }
        h1 { font-size: 1.5rem; font-weight: 600; }
        p { margin-top: 0.5rem; color: #d1d5db; }
    </style>
</head>
<body>
    <div class="container">
        <h1>🚀 Task is now In Progress!</h1>
        <p>You can now close this tab. It will close automatically in a few seconds.</p>
    </div>

    <script>
        // 3 সেকেন্ড পর ট্যাবটি স্বয়ংক্রিয়ভাবে বন্ধ হয়ে যাবে
        window.setTimeout(function() {
            window.close();
        }, 3000);
    </script>
</body>
</html>