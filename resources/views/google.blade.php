<!DOCTYPE html>
<html>
<head>
    <meta name='color-scheme' content='light dark'>
    <title>Login Successful</title>
</head>
<body>
    <pre style='word-wrap: break-word; white-space: pre-wrap;'>" . json_encode([
        'code' => 200,
        'data' => [
            'id' => 19,
            'user_id' => 25,
            'first_name' => 'sandy',
            'last_name' => 'ali basha',
            'email' => 'sandw094537@gmail.com',
            'points' => 50,
            'token' => 'your_jwt_token_here', // Replace with actual token
            // Add any other fields you need
        ]
    ]) . "</pre>

    <script>
        // Get the content of the <pre> tag
        const preContent = document.querySelector('pre').textContent;

        // Parse the JSON response
        const responseData = JSON.parse({
            'code': 200,
            'data': {
                'id' : 19,
                'user_id' : 25,
                'first_name' : 'sandy',
                'last_name' : 'ali basha',
                'email':'sandw094537@gmail.com',
                'points': 50,
                'token' : 'your_jwt_token_here', // Replace with actual token
                // Add any other fields you need
            }
        });

        // Send data back to the parent window
        window.opener.postMessage({
            type: 'login',
            token: responseData.data.token,
            userData: responseData.data
        }, ''); // Use '' only for development; restrict to your origin in production
        
        // Close the popup
        window.close();
    </script>
    <p>Logging you in...</p>
</body>
</html>