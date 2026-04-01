<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?></title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: rgb(248, 248, 248);
            color: rgb(67, 35, 35);
            line-height: 1.3;
        }
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background: linear-gradient(135deg, rgb(47, 87, 85), rgb(90, 150, 144));
            color: white;
            padding: 15px 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        
        header .container {
            display: flex;
            justify-content: space-between;
            align-items: center;
        }
        
        header h1 {
            font-size: 1.5rem;
        }
        
        header h1 a {
            color: white;
            text-decoration: none;
        }
        
        nav a {
            color: white;
            text-decoration: none;
            margin-left: 20px;
            padding: 8px 15px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        
        nav a:hover {
            background: rgba(255,255,255,0.2);
        }
        
        main {
            padding: 40px 0;
        }
        
        .hero {
            text-align: center;
            padding: 60px 20px;
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(67, 35, 35, 0.1);
        }
        
        .hero h1 {
            color: rgb(47, 87, 85);
            font-size: 2.5rem;
            margin-bottom: 15px;
        }
        
        .btn {
            display: inline-block;
            background: rgb(47, 87, 85);
            color: white;
            padding: 12px 25px;
            border: none;
            border-radius: 8px;
            text-decoration: none;
            font-size: 16px;
            cursor: pointer;
            transition: background 0.3s;
        }
        
        .btn:hover {
            background: rgb(90, 150, 144);
        }
        
        /* Стили для форм авторизации */
        .auth-container {
            max-width: 400px;
            margin: 0 auto;
            background: white;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(67, 35, 35, 0.1);
        }
        
        .auth-container h2 {
            color: rgb(47, 87, 85);
            margin-bottom: 25px;
            text-align: center;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 8px;
            font-weight: 500;
            color: rgb(67, 35, 35);
        }
        
        .form-group input {
            width: 100%;
            padding: 12px;
            border: 2px solid rgb(224, 217, 217);
            border-radius: 8px;
            font-size: 16px;
            transition: border 0.3s;
        }
        
        .form-group input:focus {
            border-color: rgb(90, 150, 144);
            outline: none;
        }
        
        .auth-form .btn {
            width: 100%;
            margin-top: 10px;
        }
        
        .message {
            padding: 15px;
            margin-bottom: 20px;
            border-radius: 8px;
            text-align: center;
        }
        
        .message.success {
            background: rgba(90, 150, 144, 0.1);
            color: rgb(47, 87, 85);
            border: 1px solid rgb(90, 150, 144);
        }
        
        .message.error {
            background: rgba(220, 53, 69, 0.1);
            color: #721c24;
            border: 1px solid #dc3545;
        }
        
        /* Стили для курсов */
        .courses-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
            gap: 25px;
        }
        
        .course-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(67, 35, 35, 0.1);
            border-left: 5px solid rgb(90, 150, 144);
            transition: transform 0.3s;
        }
        
        .course-card:hover {
            transform: translateY(-5px);
        }
        
        .course-card h3 {
            color: rgb(47, 87, 85);
            margin-bottom: 10px;
        }
        
        footer {
            text-align: center;
            padding: 20px;
            color: rgb(100, 100, 100);
            font-size: 0.9rem;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1><a href="/">WebDev Самоучитель</a></h1>
            <nav>
                <?php if (isset($_SESSION['user_id'])): ?>
                    <a href="/dashboard.php">Личный кабинет</a>
                    <a href="/courses.php">Курсы</a>
                    <a href="/sql-practice.php">Практика SQL</a>
                    <a href="/logout.php">Выйти</a>
                <?php else: ?>
                    <a href="/login.php">Войти</a>
                    <a href="/register.php">Регистрация</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>
    
    <main class="container">
        <?php if (isset($_SESSION['message'])): ?>
            <div class="message success"><?= htmlspecialchars($_SESSION['message']) ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="message error"><?= htmlspecialchars($_SESSION['error']) ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>
