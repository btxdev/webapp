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
                <div class="header">
                    <div class="header__title">Сотрудники</div>
                    <div class="header__profile">
                        <div class="header__username">admin</div>
                        <div class="header__line"></div>
                        <div class="header__name">Иван Петров</div>
                        <div class="header__photo1">
                            <div class="header__photo2"></div>
                        </div>
                    </div>
                </div>
                <div class="content">
                    <div class="page" id="employees">
                        <table class="employees-table">
                            <tr class="employees-table__title-row">
                                    <td style="width: 20px;"><div class="title">#</div></td>
                                    <td><div class="title">Имя</div></td>
                                    <td><div class="title">Фамилия</div></td>
                                    <td><div class="title">Отчество</div></td>
                                    <td><div class="title">Должность</div></td>
                                    <td style="width: 160px;"><div class="title"></div></td>
                            </tr>
                            <tr>
                                <td style="width: 20px;"><div class="field">1</div></td>
                                <td><div class="field">Иван</div></td>
                                <td><div class="field">Петров</div></td>
                                <td><div class="field">Александрович</div></td>
                                <td><div class="field">Агент по недвижимости</div></td>
                                <td style="width: 160px;"><div class="button">Подробнее</div></td>
                            </tr>
                            <tr>
                                <td style="width: 20px;"><div class="field">2</div></td>
                                <td><div class="field">Алексей</div></td>
                                <td><div class="field">Иванов</div></td>
                                <td><div class="field">Игоревич</div></td>
                                <td><div class="field">Директор</div></td>
                                <td style="width: 160px;"><div class="button">Подробнее</div></td>
                            </tr>
                            <tr class="employee">
                                <td style="width: 20px;"><div class="field">3</div></td>
                                <td><div class="field">Михаил</div></td>
                                <td><div class="field">Корнишонов</div></td>
                                <td><div class="field">Константинович</div></td>
                                <td><div class="field">Бухгалтер</div></td>
                                <td style="width: 160px;"><div class="button">Подробнее</div></td>
                            </tr>
                        </table>
                    </div>
                </div>
            </main>
            <aside>
                <div class="aside-title">
                    <div class="aside-title__logo"></div>
                    <div class="aside-title__title">Панель управления</div>
                </div>
                <ul class="aside-ul">
                    <li class="aside-li aside-li_focused">
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