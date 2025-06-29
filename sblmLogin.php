<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0"/>
  <title>HealthyBites - Login</title>
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Inter:wght@400;600&display=swap" rel="stylesheet" />
  <link href="css/bootstrap.min.css" rel="stylesheet" />
  <link href="css/style.css" rel="stylesheet" />
  <style>
    :root {
      --bs-primary: #17a2b8;
      --bs-secondary: #ffb524;
      --bs-white: #ffffff;
      --bs-dark: #343a40;
      --bs-light: #f8f9fa;
    }

    body {
      font-family: 'Inter', sans-serif;
      background: linear-gradient(135deg, var(--bs-light), #e6f7ff);
      margin: 0;
      padding: 40px 20px;
      text-align: center;
    }

    .login-container {
      background-color: rgba(255, 255, 255, 0.95);
      padding: 40px 60px;
      border-radius: 20px;
      box-shadow: 0 10px 30px rgba(0, 0, 0, 0.15);
      max-width: 600px;
      margin: 0 auto;
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
      margin: 0 auto 20px;
      color: var(--bs-dark);
    }

    .login-buttons {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 20px;
      margin-bottom: 20px;
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
      margin: 0 auto;
    }

    .brand-logo {
      font-size: 3em;
      color: var(--bs-white);
      font-weight: bold;
    }

    .team-section {
      margin-top: 60px;
    }

    .team-title {
      font-weight: 600;
      font-size: 1.5em;
      color: var(--bs-dark);
      margin-bottom: 20px;
    }

    .team-list {
      display: flex;
      flex-wrap: wrap;
      justify-content: center;
      gap: 20px;
    }

    .team-member {
      background-color: #ffffffcc;
      padding: 12px 20px;
      border-radius: 10px;
      box-shadow: 0 2px 10px rgba(0,0,0,0.1);
      text-align: center;
      min-width: 120px;
    }

    .team-member p {
      margin: 0;
      font-weight: 600;
    }

    .team-member a img {
      width: 20px;
      margin-top: 5px;
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

  <div class="team-section">
    <h3 class="team-title">Kelompok 2</h3>
    <div class="team-list">
      <div class="team-member">
        <p>Friska Venunza Bayu</p>
        <p>20230140163</p>
        <a href="https://github.com/naxsyambis" target="_blank">
          <img src="https://cdn.jsdelivr.net/npm/simple-icons@v10/icons/github.svg" alt="GitHub">
        </a>
      </div>
      <div class="team-member">
        <p>Dian Fitri Pradini</p>
        <p>20230140177</p>
        <a href="https://github.com/dianfitrip" target="_blank">
          <img src="https://cdn.jsdelivr.net/npm/simple-icons@v10/icons/github.svg" alt="GitHub">
        </a>
      </div>
      <div class="team-member">
        <p>Aini Rana Salsabil</p>
        <p>20230140178</p>
        <a href="https://github.com/ainiranasalsabil" target="_blank">
          <img src="https://cdn.jsdelivr.net/npm/simple-icons@v10/icons/github.svg" alt="GitHub">
        </a>
      </div>
      <div class="team-member">
        <p>Adila Roisa Santosa</p>
        <p>20230140179</p>
        <a href="https://github.com/adilaroisa" target="_blank">
          <img src="https://cdn.jsdelivr.net/npm/simple-icons@v10/icons/github.svg" alt="GitHub">
        </a>
      </div>
      <div class="team-member">
        <p>Putri Aulia Syafira Arif</p>
        <p>20230140194</p>
        <a href="https://github.com/syafiraarif" target="_blank">
          <img src="https://cdn.jsdelivr.net/npm/simple-icons@v10/icons/github.svg" alt="GitHub">
        </a>
      </div>
      <div class="team-member">
        <p>Lu'lu' Luthfiah</p>
        <p>20230140209</p>
        <a href="https://github.com/lulultfh" target="_blank">
          <img src="https://cdn.jsdelivr.net/npm/simple-icons@v10/icons/github.svg" alt="GitHub">
        </a>
      </div>
    </div>
  </div>
</body>
</html>
