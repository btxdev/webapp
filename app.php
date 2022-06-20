<?php

include_once __DIR__.'/php/include_db.php';

$html_title = $settings->get('html_title');

$authorized = $access->checkSessionCookie($settings->get('session_name'));
if (!$authorized) {
    header('Location: auth');
}

function component($name) {
    $path = 'components/'.$name.'.html';
    require($path);
}

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
                <div class="header">
                    <div class="header__title" id="header-title">Договоры</div>
                    <div class="header__profile">
                        <div class="header__username">admin</div>
                        <!--<div class="header__line"></div>-->
                        <div class="header__name"></div>
                        <div class="header__photo1">
                            <div class="header__photo2"></div>
                        </div>
                    </div>
                </div>
                <div class="content">

                <?php component('admin'); ?>
                <?php component('apartments'); ?>
                <?php component('clients'); ?>
                <?php component('contracts'); ?>
                <?php component('deals'); ?>
                <?php component('employees'); ?>
                <?php component('positions'); ?>
                <?php component('services'); ?>

                </div>

                <div id="windows" style="display: none;">

                <?php component('popup/alert'); ?>
                <?php component('popup/clients'); ?>
                <?php component('popup/employees'); ?>
                <?php component('popup/positions'); ?>
                <?php component('popup/services'); ?>

                </div>

            </main>

            <aside>
                <div class="aside-title">
                    <div class="aside-title__logo"></div>
                    <div class="aside-title__title">Панель управления</div>
                </div>
                <ul class="aside-ul">
                    <li class="aside-li aside-li_focused" onclick="openPage('employees');">
                        <div class="aside-li__icon"></div>
                        <div class="aside-li__label">Сотрудники</div>
                    </li>
                    <li class="aside-li" onclick="openPage('services');">
                        <div class="aside-li__icon"></div>
                        <div class="aside-li__label">Услуги</div>
                    </li>
                    <li class="aside-li" onclick="openPage('contracts');">
                        <div class="aside-li__icon"></div>
                        <div class="aside-li__label">Договоры</div>
                    </li>
                    <li class="aside-li" onclick="openPage('deals');">
                        <div class="aside-li__icon"></div>
                        <div class="aside-li__label">Сделки</div>
                    </li>
                    <li class="aside-li" onclick="openPage('apartments');">
                        <div class="aside-li__icon"></div>
                        <div class="aside-li__label">Недвижимость</div>
                    </li>
                    <li class="aside-li" onclick="openPage('clients');">
                        <div class="aside-li__icon"></div>
                        <div class="aside-li__label">Клиенты</div>
                    </li>
                </ul>
                <div class="aside-line"></div>
                <ul class="aside-ul">
                    <li class="aside-li" onclick="openPage('positions');">
                        <div class="aside-li__icon"></div>
                        <div class="aside-li__label">Должности</div>
                    </li>
                    <li class="aside-li" onclick="logout();">
                        <div class="aside-li__icon"></div>
                        <div class="aside-li__label">Выход</div>
                    </li>
                </ul>
            </aside>

        </div>
    </div>
</body>
</html>