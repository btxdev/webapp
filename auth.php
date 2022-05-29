<?php

include_once __DIR__.'/php/main.php';

$html_title = $settings->get('html_title');

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="robots" content="noindex, follow">
    <title>Авторизация :: <?= $html_title ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/auth.css">
    
    <script type="application/javascript" src="js/auth.js"></script>
</head>
<body>
    <div class="limiter">
        <div class="container">
            <div class="login-form">
                <form action="" class="login-form__form">
                    <span class="login-form__title">Авторизация</span>
                    <div class="login-form__input">
                        <input type="text" class="input" name="username" placeholder="Аккаунт">
                        <span class="focus-input"></span>
                    </div>
                    <div class="login-form__input">
                        <input type="password" class="input" name="password" placeholder="Пароль">
                        <span class="focus-input"></span>
                    </div>
                    <div class="login-form__button">
                        <button class="button">Логин</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</body>
</html>