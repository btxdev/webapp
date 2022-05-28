<?php

    include_once __DIR__.'/main.php';
    include_once __DIR__.'/include_db.php';

    $db->run('SET FOREIGN_KEY_CHECKS = 0;');
    $db->run('SET UNIQUE_CHECKS = 0;');

    // создание таблицы employees
    $db->run('DROP TABLE IF EXISTS `employees`;');
    $db->run(
        'CREATE TABLE `employees` ( 
            `employee_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , 
            `first_name` TINYTEXT NOT NULL , 
            `seconds_name` TINYTEXT NOT NULL , 
            `patronymic` TINYTEXT NOT NULL , 
            `phone` TINYTEXT NOT NULL , 
            `email` TINYTEXT NOT NULL , 
            `reg_date` DATE NOT NULL , 
            PRIMARY KEY (`emp_id`)) 
        ENGINE = InnoDB'
    );

    // создание таблицы clients
    $db->run('DROP TABLE IF EXISTS `clients`;');
    $db->run(
        'CREATE TABLE `clients` ( 
            `client_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , 
            `first_name` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
            `seconds_name` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
            `patronymic` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
            `birth_date` DATE NOT NULL , 
            `phone` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
            `email` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
            `reg_date` DATE NOT NULL , 
            PRIMARY KEY (`client_id`)) 
        ENGINE = InnoDB'
    );

    // создание таблицы roles
    $db->run('DROP TABLE IF EXISTS `roles`;');
    $db->run(
        'CREATE TABLE `roles` ( 
            `role_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , 
            `role` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
            PRIMARY KEY (`role_id`)) 
        ENGINE = InnoDB;'
    );

    // создание таблицы positions
    $db->run('DROP TABLE IF EXISTS `positions`;');
    $db->run(
        'CREATE TABLE `positions` ( 
            `position_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , 
            `position` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
            PRIMARY KEY (`position_id`)) 
        ENGINE = InnoDB;'
    );

    // создание таблицы apartments
    $db->run('DROP TABLE IF EXISTS `apartments`;');
    $db->run(
        'CREATE TABLE `apartments` ( 
            `apartment_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , 
            `type` INT UNSIGNED NOT NULL , 
            `address` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
            `developer` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
            `construction_date` DATE NOT NULL , 
            `publication_date` DATE NOT NULL , 
            `price` INT UNSIGNED NOT NULL , 
            PRIMARY KEY (`apartment_id`)) 
        ENGINE = InnoDB;'
    );

    // создание таблицы contracts
    $db->run('DROP TABLE IF EXISTS `contracts`;');
    $db->run(
        'CREATE TABLE `contracts` ( 
            `contract_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , 
            `type` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
            `date` DATE NOT NULL , 
            PRIMARY KEY (`contract_id`)) 
        ENGINE = InnoDB'
    );

    // создание таблицы passports
    $db->run('DROP TABLE IF EXISTS `passports`;');
    $db->run(
        'CREATE TABLE `passports` ( 
            `passport_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , 
            `number` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
            `birth_location` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
            `address` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
            `birth_date` DATE NOT NULL , 
            `organ` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
            `issue_date` DATE NOT NULL , 
            PRIMARY KEY (`passport_id`)) 
        ENGINE = InnoDB;'
    );

    // создание таблицы services
    $db->run('DROP TABLE IF EXISTS `services`;');
    $db->run(
        'CREATE TABLE `services` ( 
            `service_id` INT NOT NULL AUTO_INCREMENT , 
            `service` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
            `description` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
            `price` INT NOT NULL , 
            PRIMARY KEY (`service_id`)) 
        ENGINE = InnoDB;'
    );

    // создание таблицы deals
    $db->run('DROP TABLE IF EXISTS `deals`;');
    $db->run(
        'CREATE TABLE `deals` ( 
            `deal_id` INT UNSIGNED NOT NULL AUTO_INCREMENT , 
            `deal` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
            `deal_date` DATE NOT NULL , 
            PRIMARY KEY (`deal_id`)) 
        ENGINE = InnoDB;'
    );

    // создание таблицы sessions
    $db->run(
        'CREATE TABLE `sessions` ( 
        `sesshash` TINYTEXT CHARACTER SET utf8mb4 COLLATE utf8mb4_general_ci NOT NULL , 
        `employee_id` INT UNSIGNED NOT NULL , 
        `created` TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP , 
        PRIMARY KEY (`sesshash`(32))),
        INDEX `employee_id` (`employee_id`)
        ENGINE = InnoDB;'
    );





    // // пересоздание таблицы users_roles
    // $db->run('DROP TABLE IF EXISTS `users_roles`;');
    // $db->run(
    //     'CREATE TABLE `users_roles` ( 
    //     `uuid` INT UNSIGNED NOT NULL , 
    //     `role_id` TINYINT UNSIGNED NOT NULL , 
    //     PRIMARY KEY (`uuid`)) 
    //     ENGINE = InnoDB;'
    // );


    // // ALTER TABLE `dbmyapp`.`sessions` ADD INDEX `uuid` (`uuid`);

    // $db->run('SET FOREIGN_KEY_CHECKS = 1;');
    // $db->run('SET UNIQUE_CHECKS = 1;');

    // // установка связи между таблицами users и users_roles по индексу uuid
    // // при удалении пользователя, будет удалена запись о роли этого пользователя
    // $db->run(
    //     'ALTER TABLE `users_roles` ADD FOREIGN KEY (`uuid`) 
    //     REFERENCES `users`(`uuid`) ON DELETE CASCADE ON UPDATE CASCADE;'
    // );

    // // установка связи между таблицами roles и users_roles по индексу role_id
    // // при удалении роли, эта роль будет удалена у всех пользователей
    // $db->run(
    //     'ALTER TABLE `users_roles` ADD FOREIGN KEY (`role_id`) 
    //     REFERENCES `roles`(`role_id`) ON DELETE CASCADE ON UPDATE CASCADE;'
    // );

    // // установка связи между таблицами users и sessions по индексу uuid
    // // при удалении пользователя, будет удалены все сессии этого пользователя
    // $db->run(
    //     'ALTER TABLE `sessions` ADD FOREIGN KEY (`uuid`) 
    //     REFERENCES `users`(`uuid`) ON DELETE CASCADE ON UPDATE CASCADE;'
    // );

    // // создание ролей
    // $admin->createRole('admin');
    // $admin->createRole('default');

    // // создание пользователя admin
    // $admin_uuid = $admin->createUser('admin', 'password');

    // // присвоение роли admin пользователю admin
    // $admin->setRoleToUser('admin', $admin_uuid);

    // // создание пользователя user
    // $user_uuid = $admin->createUser('user', 'password');

    // // присвоение роли default пользователю user
    // $admin->setRoleToUser('default', $user_uuid);
    
    // echo('ok');

?>