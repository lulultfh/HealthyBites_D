<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>HealthyBites - Login</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600&display=swap" rel="stylesheet">
    <link href="css/bootstrap.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet"> 
    <style>
        :root {
            --bs-primary: #17a2b8;
            --bs-secondary: #ffb524;
            --bs-white: #ffffff;
            --bs-dark: #343a40;
            --bs-light: #f8f9fa;
            --bs-primary-rgb: 23, 162, 184;
            --bs-secondary-rgb: 255, 181, 36;
        }
        body {
            font-family: 'Poppins', sans-serif;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            background: linear-gradient(135deg, var(--bs-light), #e6f7ff);
            text-align: center;
            overflow: hidden;
        }
        .login-container {
            background-color: rgba(255, 255, 255, 0.95);
            padding: 40px 60px;
            border-radius: 20px;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
            max-width: 600px;
            width: 90%;
            display: flex;
            flex-direction: column;
            align-items: center;
            gap: 25px;
            animation: fadeIn 1s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .login-title {
            font-size: 3.2em;
            font-weight: 600;
            margin-bottom: 10px;
            color: var(--bs-primary);
        }
        .login-description {
            font-size: 1.1em;
            max-width: 450px;
            color: var(--bs-dark);
        }
        .login-buttons {
            display: flex;
            flex-wrap: wrap;
            justify-content: center;
            gap: 20px;
        }
        .btn-login {
            padding: 15px 40px;
            border: none;
            border-radius: 10px;
            font-size: 1.1em;
            color: white;
            font-weight: 600;
            min-width: 180px;
            text-decoration: none;
            transition: 0.4s;
        }
        .btn-user {
            background-color: var(--bs-primary);
        }
        .btn-user:hover {
            background-color: var(--bs-secondary);
            transform: translateY(-3px);
        }
        .btn-admin {
            background-color: var(--bs-secondary);
        }
        .btn-admin:hover {
            background-color: var(--bs-primary);
            transform: translateY(-3px);
        }
        .brand-logo-box {
            width: 200px;
            height: 200px;
            background-color: var(--bs-primary);
            border-radius: 25px;
            display: flex;
            justify-content: center;
            align-items: center;
            box-shadow: 0 8px 25px rgba(0, 0, 0, 0.2);
        }
        .brand-logo {
            font-size: 3em;
            color: var(--bs-white);
            font-weight: bold;
        }
    </style>
</head>
<body>
    <div class="login-container">
        <h1 class="login-title">HealthyBites</h1>
        <p class="login-description">Welcome to HealthyBites! Please choose your login role to access our wholesome recipes and organic products.</p>
        <div class="login-buttons">
            <a href="login.php" class="btn-login btn-user">Login as Buyer</a>
            <a href="admin/login_admin.php" class="btn-login btn-admin">Login as Admin</a>
        </div>
        <div class="brand-logo-box">
            <span class="brand-logo">HB</span>
        </div>
    </div>
</body>
</html>
