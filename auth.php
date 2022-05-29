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
    <title>Авторизация :: <?= $html_title ?></title>
    <link rel="stylesheet" href="css/auth.css">
    <script type="application/javascript" src="js/auth.js"></script>
</head>
<body>
    <div class="container">

    </div>
</body>
</html>