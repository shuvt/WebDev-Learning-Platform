<?php
// templates/header.php
ob_start();
?>
<!DOCTYPE html>
<html lang="ru">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= SITE_NAME ?></title>
    <style>
        * { margin: 0; padding: 0; box-sizing: border-box; }
        html, body { 
            height: 100%; 
            font-family: Arial, sans-serif; 
            background: rgb(224, 217, 217);
            color: rgba(38, 76, 73, 1);
        }
        body {
            display: flex;
            flex-direction: column;
            min-height: 100vh;
        }
        .header {
            background: rgb(47, 87, 85);
            color: rgb(224, 217, 217);
            padding: 1rem 0;
            flex-shrink: 0;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
        }
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
        }
        .nav { 
            display: flex; 
            justify-content: space-between; 
            align-items: center; 
        }
        .logo { 
            font-size: 1.5rem; 
            font-weight: bold; 
            color: rgb(224, 217, 217);
        }
        .nav-links a { 
            color: rgb(224, 217, 217); 
            text-decoration: none; 
            margin-left: 20px;
            padding: 8px 16px;
            border-radius: 5px;
            transition: background 0.3s;
        }
        .nav-links a:hover { 
            background: rgb(90, 150, 144);
            text-decoration: none;
        }
        .main { 
            flex: 1;
            padding: 2rem 0; 
        }
        .btn {
            background: rgb(47, 87, 85);
            color: rgb(224, 217, 217);
            padding: 12px 24px;
            border: none;
            border-radius: 8px;
            cursor: pointer;
            text-decoration: none;
            display: inline-block;
            font-size: 16px;
            transition: all 0.3s;
            font-weight: bold;
        }
        .btn:hover { 
            background: rgb(90, 150, 144);
            transform: translateY(-2px);
            box-shadow: 0 4px 12px rgba(0,0,0,0.2);
        }
        .message {
            padding: 15px;
            margin: 15px 0;
            border-radius: 8px;
            border-left: 5px solid;
        }
        .success { 
            background: rgba(90, 150, 144, 0.1); 
            color: rgb(47, 87, 85); 
            border-left-color: rgb(90, 150, 144);
        }
        .error { 
            background: rgba(47, 87, 85, 0.1); 
            color: rgb(47, 87, 85); 
            border-left-color: rgb(47, 87, 85);
        }
        footer {
            background: rgb(47, 87, 85);
            color: rgb(224, 217, 217);
            padding: 2rem 0;
            text-align: center;
            flex-shrink: 0;
            margin-top: auto;
        }
        
        .auth-container {
            max-width: 400px;
            margin: 40px auto;
            padding: 40px;
            background: white;
            border-radius: 15px;
            box-shadow: 0 8px 25px rgba(67, 35, 35, 0.1);
            border: 1px solid rgba(67, 35, 35, 0.1);
        }
        
        .auth-form .form-group {
            margin-bottom: 25px;
        }
        
        .auth-form label {
            display: block;
            margin-bottom: 8px;
            font-weight: bold;
            color: rgb(47, 87, 85);
        }
        
        .auth-form input {
            width: 100%;
            padding: 12px 15px;
            border: 2px solid rgb(224, 217, 217);
            border-radius: 8px;
            font-size: 16px;
            transition: border 0.3s;
        }
        
        .auth-form input:focus {
            border-color: rgb(90, 150, 144);
            outline: none;
            box-shadow: 0 0 0 3px rgba(90, 150, 144, 0.1);
        }
        
        .course-card {
            background: white;
            padding: 25px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(47, 87, 85);
            margin-bottom: 25px;
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
    </style>
</head>
<body>
    <div class="header">
        <div class="container">
            <div class="nav">
                <div class="logo"><?= SITE_NAME ?></div>
                <div class="nav-links">
                    <?php if (isset($_SESSION['user_id'])): ?>
                        <a href="/dashboard.php">Личный кабинет</a>
                        <a href="/courses.php">Обучение</a>
                        <a href="/logout.php">Выйти</a>
                    <?php else: ?>
                        <a href="/login.php">Войти</a>
                        <a href="/register.php">Регистрация</a>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <div class="container main">
        <?php
        if (isset($_SESSION['message'])): ?>
            <div class="message success"><?= $_SESSION['message'] ?></div>
            <?php unset($_SESSION['message']); ?>
        <?php endif; ?>
        
        <?php if (isset($_SESSION['error'])): ?>
            <div class="message error"><?= $_SESSION['error'] ?></div>
            <?php unset($_SESSION['error']); ?>
        <?php endif; ?>