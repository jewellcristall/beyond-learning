<?php
session_start();
require_once __DIR__ . '/includes/auth.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';

    $res = authenticate_user($email, $password);

    if ($res['success']) {
        $user = $res['user'];
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['username'] = $user['username'];

        // buffering screen with spinner para hindi derecho
        echo "
        <html>
        <head>
            <style>
                body {
                    margin: 0;
                    padding: 0;
                    font-family: Arial, sans-serif;
                    background: #f8f8f8;
                    display: flex;
                    justify-content: center;
                    align-items: center;
                    height: 100vh;
                }
                .loader {
                    border: 8px solid #f3f3f3;
                    border-top: 8px solid purple;
                    border-radius: 50%;
                    width: 60px;
                    height: 60px;
                    animation: spin 1s linear infinite;
                }
                @keyframes spin {
                    0% { transform: rotate(0deg); }
                    100% { transform: rotate(360deg); }
                }
                .message {
                    margin-top: 20px;
                    font-size: 18px;
                    color: #333;
                    text-align: center;
                }
            </style>
        </head>
        <body>
            <div>
                <div class='loader'></div>
                <div class='message'>Logging you in...</div>
            </div>
            <script>
                setTimeout(function(){
                    window.location.href = 'dashboard.php';
                }, 2000);
            </script>
        </body>
        </html>";
    } else {
        $_SESSION['flash_error'] = $res['message'];
        $_SESSION['open_modal'] = 'login';
        header('Location: index.php');
        exit;
    }
}
