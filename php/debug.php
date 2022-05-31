<?php

    include_once __DIR__.'/main.php';
    include_once __DIR__.'/include_db.php';

    // создание пользователя admin
    $admin_uuid = $admin->createUser('admin', 'r00tPassw0rd');

    // присвоение роли admin пользователю admin
    $admin->setRoleToUser('admin', $admin_uuid);

    // создание пользователя test
    $user_uuid = $admin->createUser('test', '123456');

    // присвоение роли default пользователю test
    $admin->setRoleToUser('default', $user_uuid);
    
    echo('ok');

?>