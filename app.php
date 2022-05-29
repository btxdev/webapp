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
    <title>Панель управления :: <?= $html_title ?></title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Ubuntu:ital,wght@0,300;0,400;0,500;1,300&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="css/reset.css">
    <link rel="stylesheet" href="css/app.css">
    
    <script type="application/javascript" src="js/app.js"></script>
</head>
<body>
    <div class="limiter">
        <div class="container">
            <main>
                123
            </main>
            <aside>
                <div class="aside-title">
                    <div class="aside-title__logo"></div>
                    <div class="aside-title__title">Панель управления</div>
                </div>
                <ul class="aside-ul">
                    <li class="aside-li aside-li__focus">
                        <div class="aside-li__icon"></div>
                        <div class="aside-li__label">Сотрудники</div>
                    </li>
                    <li class="aside-li">
                        <div class="aside-li__icon"></div>
                        <div class="aside-li__label">Услуги</div>
                    </li>
                    <li class="aside-li">
                        <div class="aside-li__icon"></div>
                        <div class="aside-li__label">Договоры</div>
                    </li>
                    <li class="aside-li">
                        <div class="aside-li__icon"></div>
                        <div class="aside-li__label">Сделки</div>
                    </li>
                    <li class="aside-li">
                        <div class="aside-li__icon"></div>
                        <div class="aside-li__label">Недвижимость</div>
                    </li>
                    <li class="aside-li">
                        <div class="aside-li__icon"></div>
                        <div class="aside-li__label">Клиенты</div>
                    </li>
                </ul>
                <div class="aside-line"></div>
                <ul class="aside-ul">
                    <li class="aside-li">
                        <div class="aside-li__icon"></div>
                        <div class="aside-li__label">Администрирование</div>
                    </li>
                </ul>
            </aside>
        </div>
    </div>
</body>
</html>